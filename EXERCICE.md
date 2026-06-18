# Exercice — Corriger les erreurs PHPStan avec Claude

## Contexte

La CI bloque sur ce ticket. PHPStan (level 6) remonte **5 erreurs** dans deux fichiers :
- `src/Order/Service/OrderExporter.php` (nouveau service du sprint Q2)
- `src/Reporting/Service/ReportingService.php` (existant)

## Objectif

Corriger toutes les erreurs PHPStan en utilisant le skill `/fix-phpstan` et les hooks de validation automatique.

## Étapes suggérées

1. Lancer PHPStan pour voir les erreurs :
   ```bash
   vendor/bin/phpstan analyse
   ```

2. Utiliser le skill Claude `/fix-phpstan` sur les fichiers incriminés

3. Vérifier que PHPStan passe à 0 erreur avant de committer

## Types d'erreurs présentes

| Fichier | Erreur |
|---|---|
| `OrderExporter.php` | Return type `string` déclaré mais retourne `array` |
| `OrderExporter.php` | Appel sur `getTier()` potentiellement `null` (×2) |
| `OrderExporter.php` | Variable `$total` utilisée sans initialisation |
| `OrderExporter.php` | `@return array` non typé (PHPDoc incomplet) |
| `ReportingService.php` | `@return array` non typé |

## Critère de succès

```
 [OK] No errors
```
