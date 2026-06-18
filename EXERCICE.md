# Exercice J3 — US06 : Tests 80 % de couverture + MR ready

## User Story

> En tant qu'**équipe**, nous voulons **atteindre 80 % de couverture de tests** sur le module Return et préparer une Merge Request propre, afin de livrer la feature en production avec confiance.

---

## Contexte

C'est la dernière US du sprint. Votre binôme a implémenté les US01 à US05.
Vous disposez maintenant d'un module Return complet :

```
src/Return/
├── Entity/
│   ├── ReturnRequest.php   (+ rejectionReason depuis US04)
│   └── ReturnStatus.php
├── Repository/
│   ├── ReturnRequestRepositoryInterface.php
│   └── ReturnRequestRepository.php
└── Service/
    └── ReturnAdminService.php
        ├── getPendingRequests()   (US02)
        ├── approveReturn()        (US03)
        ├── rejectReturn()         (US04)
        └── refundReturn()         (US05)

src/Payment/Gateway/
└── PaymentGatewayInterface.php   (US05)

tests/Unit/Return/Service/
├── ReturnAdminServiceTest.php
├── ReturnAdminServiceApproveTest.php
├── ReturnAdminServiceRejectTest.php
└── ReturnAdminServiceRefundTest.php
```

---

## Ce que vous allez faire

### Objectif 1 : Mesurer la couverture actuelle

```bash
php bin/phpunit tests/Unit/Return/ \
    --coverage-text \
    --coverage-filter src/Return
```

Ciblez : **≥ 80 % de couverture de lignes** sur `src/Return/`.

### Objectif 2 : Identifier les trous de couverture

Cas typiques non encore couverts :

```php
// Dans ReturnAdminService::getPendingRequests()
// - Tri fonctionne-t-il vraiment sur plusieurs éléments ?

// Dans ReturnRequest::rejectionReason
// - Cas où rejectionReason est null après approve (ne doit pas être défini)

// Cas limites à ajouter :
public function test_reject_sets_rejection_reason_on_entity(): void
public function test_approve_does_not_set_rejection_reason(): void
public function test_refund_does_not_flush_if_gateway_throws(): void
```

### Objectif 3 : PHPStan niveau 6

```bash
php vendor/bin/phpstan analyse src/Return src/Payment --level=6
```

Corrigez toutes les erreurs avant la MR.

### Objectif 4 : Préparer la MR

#### Checklist MR

- [ ] Tous les tests passent : `php bin/phpunit tests/Unit/Return/`
- [ ] Couverture ≥ 80 % : `--coverage-text --coverage-filter src/Return`
- [ ] PHPStan niveau 6 sans erreur : `phpstan analyse src/Return src/Payment`
- [ ] Code review : noms de méthodes clairs, pas de TODO, pas de `var_dump`
- [ ] `EXERCICE.md` retiré ou déplacé dans `docs/` avant merge
- [ ] Commit message de la MR suit le format : `feat: module Return — Retours et Remboursements (US01-US06)`

#### Description MR à rédiger avec Claude

```
Génère une description de Merge Request pour ce module :

Résumé : Feature "Retours et Remboursements" implémentée en 6 User Stories.
- US01 : Initier une demande de retour (Entity + Service)
- US02 : Lister les demandes en attente (Repository + Service)
- US03 : Approuver une demande (state machine)
- US04 : Refuser avec motif (validation + state machine)
- US05 : Remboursement automatique (PaymentGateway)
- US06 : Couverture ≥ 80% + PHPStan 6

Format : titre court, résumé 3 bullets, section "Comment tester",
section "Checklist MR" avec cases à cocher.
```

### Objectif 5 : Rétrospective binôme (5 min)

Répondez ensemble à ces 3 questions :

1. **Qu'est-ce que Claude a bien géré** pendant ce sprint ?
2. **Où avez-vous dû corriger ou guider Claude** ?
3. **Si vous recommenciez**, comment changeriez-vous votre façon de prompter ?

Notez vos réponses — elles serviront au debriefing collectif.

---

## Étapes

### 1. Mesurer la couverture

```bash
php bin/phpunit tests/Unit/Return/ --coverage-text --coverage-filter src/Return
```

Si < 80 % : identifiez les lignes non couvertes dans le rapport.

### 2. Écrire les tests manquants

Ajoutez-les dans le fichier de test le plus approprié (ou créez
`tests/Unit/Return/Service/ReturnAdminServiceCoverageTest.php`).

### 3. Vérifier PHPStan

```bash
php vendor/bin/phpstan analyse src/Return src/Payment --level=6
```

Corrigez les erreurs une par une.

### 4. Commit final

```bash
git add src/ tests/
git commit -m "test: couverture ≥80% module Return + corrections PHPStan"
```

### 5. Rédiger la description MR avec Claude

Utilisez le prompt fourni ci-dessus. Copiez la description générée
dans votre interface GitLab/GitHub pour créer la MR.

---

## Prompts Claude suggérés

```
Voici le rapport de couverture PHPUnit pour src/Return/ :
[coller la sortie --coverage-text]

Identifie les lignes non couvertes et génère les tests PHPUnit
qui permettraient d'atteindre 80% de couverture.
Priorise les cas limites métier plutôt que les getters/setters.
```

```
PHPStan niveau 6 me donne ces erreurs :
[coller la sortie phpstan]

Corrige-les une par une. Explique chaque correction.
```

---

## Critères de succès

- [ ] Couverture ≥ 80 % sur `src/Return/`
- [ ] PHPStan niveau 6 sans erreur
- [ ] Description MR générée et complète
- [ ] Rétrospective binôme réalisée
- [ ] Commit final propre sur la branche

---

## Bilan de la journée

Vous avez construit une feature complète en 6 US progressives :

| US | Compétence principale |
|----|-----------------------|
| US01 | Entity + Service (initiation) |
| US02 | Repository + Interface (DIP) |
| US03 | State machine (transitions) |
| US04 | Validation + ordre de préconditions |
| US05 | Gateway externe + atomicité |
| US06 | Couverture + qualité statique + MR |

C'est le workflow d'un développeur Symfony professionnel travaillant
avec Claude comme copilote de développement.
