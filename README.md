# Learn=>Audit — Simulateur d'audit ISO 2700x

Application web de simulation d'audit de conformité de la sécurité de l'information, basée sur les normes **ISO/IEC 27001:2022**, **ISO/IEC 27002:2022** et **ISO/IEC 27005:2022**.

Elle reproduit les étapes d'un audit : préparation (entreprise, périmètre, référentiels), évaluation des clauses et des contrôles, analyse et traitement des risques, collecte des preuves, puis rédaction d'un rapport.

> ⚠️ Projet pédagogique à l'état de prototype. Il n'est pas destiné à un usage en production.

## Objectifs

- Simuler un audit de conformité ISO 27001 / 27002 de bout en bout.
- Associer chaque écart à une évaluation du risque et à un plan d'action.
- Mesurer l'avancement et la conformité d'un audit avec des indicateurs.

## Fonctionnalités

- **Authentification** : inscription, connexion, déconnexion ; rôles `auditeur`, `formateur`, `admin`.
- **Entreprises** : CRUD, activation et désactivation, archivage (soft delete), restauration, suppression définitive. La suppression est bloquée si des audits sont liés.
- **Audits** : création (entreprise et référentiels), modification, suppression, démarrage, sélection de l'« audit en cours ».
- **Clauses ISO 27001 (§4 à §10)** : statut de conformité, justification, recommandation, score.
- **Contrôles ISO 27002** : statut, gravité et probabilité du risque, niveau de risque calculé, points obtenus.
- **Gestion des risques** : plan d'action, responsable, échéance, priorité, statut de traitement.
- **Preuves** : ajout de preuves avec fichier joint (images, PDF, documents Office, texte ; 5 Mo maximum), réservé aux rôles `admin` et `formateur`.
- **Indicateurs** : taux de progression, taux de conformité, score pondéré, nombre de non-conformités et de risques élevés ou critiques.
- **Rapports** : rapport d'audit éditable et enregistré, impression ou export PDF via le navigateur, historique des audits, statistiques (Chart.js).

## Technologies

| Couche | Outils |
|---|---|
| Back-end | PHP 8.4+, Laravel 13 |
| Front-end | Blade, Tailwind CSS 4, Vite 8, Chart.js |
| Base de données | SQLite (par défaut) |
| Outils | Composer, npm, PHPUnit 12, Laravel Pint, Docker |

## Architecture

Application Laravel MVC classique :

- `app/Http/Controllers` : `AuditController`, `EntrepriseController`, `ControleController`, `ReportController`, `DashboardController`, `AuthController`
- `app/Models` : `Utilisateur`, `Auditeur`, `Entreprise`, `Reference`, `Clause`, `Controle`, `Preuve`, `Audit`, `Evaluation`, `AuditClauseEvaluation`, `Rapport`…
- `database/migrations` : schéma complet (clés UUID, soft deletes)
- `database/seeders` : comptes de démonstration et données de référence ISO
- `resources/views` : vues Blade (`pages/audits`, `pages/entreprises`, `reports`…)
- `routes/web.php` : routes de l'application

### Modèle de données (simplifié)

```
Utilisateur ─1:1─ Auditeur ─1:n─ Audit ─n:1─ Entreprise
                                   │
            ┌──────────────────────┼─────────────────────┐
     AuditReference          Evaluation           AuditClauseEvaluation
            │                (contrôles)               (clauses)
        Reference ─1:n─ Clause ─1:n─ Controle ─1:n─ Preuve
Audit ─1:1─ Rapport
```

## Démarrage rapide avec Docker

Le moyen le plus simple de tester le projet : seul Docker (avec Docker Compose) est nécessaire.

```bash
git clone <url-du-depot>
cd Audit_Project
docker compose up --build
```

Puis ouvrir http://localhost:8000.

Au premier démarrage, le conteneur :

1. crée le fichier `.env` à partir de `.env.example` et génère `APP_KEY` ;
2. crée la base SQLite, applique les migrations et charge les données de démonstration ;
3. crée le lien `public/storage` pour les fichiers de preuves.

La base et les fichiers déposés sont conservés dans les volumes Docker `db-data` et `uploads`.

| Action | Commande |
|---|---|
| Changer le port (si 8000 est occupé) | `APP_PORT=8081 docker compose up --build` |
| Lancer en arrière-plan | `docker compose up -d --build` |
| Voir les logs | `docker compose logs -f app` |
| Lancer les tests | `docker compose run --rm app composer test` |
| Commande Artisan | `docker compose exec app php artisan <commande>` |
| Arrêter | `docker compose down` |
| Réinitialiser la base et les fichiers | `docker compose down -v` |

> Les tests utilisent une base SQLite en mémoire (`phpunit.xml`) et ne modifient pas la base de démonstration.
>
> Pour explorer toutes les pages, connectez-vous avec le compte **formateur** de démonstration : le compte administrateur n'a pas de profil auditeur, ce qui provoque des erreurs sur certaines pages d'audit.
>
> L'image Docker est prévue pour le développement et les tests, pas pour la production (serveur `php artisan serve`, mode debug actif).

## Prérequis (installation sans Docker)

- PHP **8.4** ou supérieur (requis par les dépendances verrouillées dans `composer.lock`), avec `pdo_sqlite`
- Composer 2
- Node.js et npm

## Installation sans Docker

```bash
git clone <url-du-depot>
cd Audit_Project

composer install
npm install

# Fichier d'environnement
cp .env.example .env
php artisan key:generate

# Base SQLite
touch database/database.sqlite
php artisan migrate

# Données de démonstration et référentiels ISO
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=IsoMasterDataSeeder

# Accès public aux fichiers de preuves
php artisan storage:link
```

### Réinitialiser la base

```bash
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=IsoMasterDataSeeder
```

## Configuration

Variables principales du fichier `.env` (ne jamais versionner ce fichier) :

| Variable | Rôle | Valeur par défaut |
|---|---|---|
| `APP_KEY` | Clé de chiffrement | générée par `php artisan key:generate` |
| `APP_ENV` / `APP_DEBUG` | Environnement / mode debug | `local` / `true` en développement |
| `DB_CONNECTION` | Base de données | `sqlite` |

## Utilisation

```bash
# Serveur, file d'attente et Vite en parallèle (script Composer)
composer run dev

# Ou séparément
php artisan serve
npm run dev
```

Puis ouvrir http://localhost:8000.

Le seeder `DatabaseSeeder` crée des comptes de démonstration (administrateur, formateur, auditeur). Leurs identifiants sont définis dans `database/seeders/DatabaseSeeder.php`. Ces comptes servent uniquement au développement local.

Déroulé type :

1. Créer une entreprise (**Entreprises**).
2. Créer un audit et choisir les référentiels (**Audits**).
3. Démarrer l'audit, puis évaluer les clauses et les contrôles.
4. Renseigner les risques, les plans d'action et les preuves.
5. Consulter les indicateurs et compléter le rapport (**Rapports**).

## Tests

```bash
composer test     # équivaut à php artisan test
```

Seuls les tests d'exemple Laravel sont présents pour l'instant. La logique métier (indicateurs, calcul du risque) n'est pas encore couverte.

## Limites connues

- Le référentiel ISO 27002 chargé par le seeder ne contient que quelques contrôles d'exemple.
- Certaines pages publiques (À propos, Documentation, FAQ, réinitialisation du mot de passe) ne sont pas encore implémentées.
- L'export PDF passe par la fonction d'impression du navigateur.
- Projet pédagogique, non audité pour un déploiement en production.

## Pistes d'amélioration

- Compléter le référentiel ISO 27002 et ajouter la gestion des référentiels depuis l'interface.
- Mettre en place des policies Laravel pour cloisonner les audits par utilisateur.
- Harmoniser le calcul du niveau de risque dans une seule classe de service.
- Ajouter des tests unitaires et fonctionnels.
- Générer le rapport en PDF côté serveur.

