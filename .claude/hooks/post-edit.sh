#!/bin/bash
# .claude/hooks/post-edit.sh — s'exécute après chaque édition (PostToolUse: Edit|Write)
# Reçoit le chemin du fichier édité en $1 (extrait du JSON du hook via jq dans settings.local.json).
# Toute la validation tourne DANS le conteneur Docker, jamais sur l'hôte.
#
# Codes de sortie : 0 = OK / informatif (non bloquant), 2 = erreur de syntaxe (bloque et remonte à Claude).

set -uo pipefail

CONTAINER="fluxcommerce_php"

# Racine du projet déduite de l'emplacement du script (robuste au déplacement du repo).
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

FILE="${1:-}"

# Rien à valider sans fichier
[ -z "$FILE" ] && exit 0

# Uniquement les fichiers PHP
if [[ "$FILE" != *.php ]]; then
    exit 0
fi

# Chemin relatif au projet (le conteneur monte le projet sur son workdir /var/www/html)
REL="${FILE#"$PROJECT_ROOT"/}"

# Sans conteneur démarré, on n'échoue pas : on informe et on sort proprement.
if ! docker ps --format '{{.Names}}' | grep -qx "$CONTAINER"; then
    echo "post-edit: conteneur $CONTAINER non démarré — validation ignorée."
    exit 0
fi

echo "=== Validation PHP : $REL ==="

STATUS=0

# 1. Syntaxe PHP — seule erreur bloquante (remontée à Claude via stderr + exit 2)
if ! docker exec "$CONTAINER" php -l "$REL" > /dev/null 2>&1; then
    {
        echo "ERREUR SYNTAXE PHP dans $REL :"
        docker exec "$CONTAINER" php -l "$REL"
    } >&2
    STATUS=2
fi

# 2. PHP CS Fixer (dry-run, informatif — ne bloque jamais le hook)
# Flags requis par ce projet : config à règles risquées (declare_strict_types, void_return)
# et Finder dans .php-cs-fixer.php -> --path-mode=intersection pour cibler un seul fichier.
echo "--- PHP CS Fixer ---"
docker exec "$CONTAINER" vendor/bin/php-cs-fixer fix "$REL" \
    --dry-run --diff --allow-risky=yes --config=.php-cs-fixer.php --path-mode=intersection 2>&1 | tail -20 || true

# 3. PHPStan (informatif — ne bloque jamais le hook)
# phpstan.neon référence tests/object-manager.php (doctrine.objectManagerLoader), non commité :
# sans ce fichier PHPStan s'arrête sur une erreur. On ne lance l'analyse que s'il est présent.
echo "--- PHPStan (niveau 6) ---"
if docker exec "$CONTAINER" test -f tests/object-manager.php; then
    docker exec "$CONTAINER" vendor/bin/phpstan analyse "$REL" --level=6 --no-progress 2>&1 | tail -15 || true
else
    echo "post-edit: tests/object-manager.php absent — PHPStan ignoré (voir phpstan.neon)."
fi

echo "=== Validation terminée ==="

exit "$STATUS"
