# BK Business — Application Web MVC

## Stack technique
- Backend  : PHP pur (sans framework)
- Frontend : HTML / CSS / Bootstrap 5
- BDD      : MySQL / MariaDB
- Pattern  : MVC

## Installation
1. Copier le dossier `bk_business/` dans votre répertoire web (ex: `htdocs/` ou `www/`) ou utilisez un serveur PHP intégré.
2. Installer les dépendances Composer :
   ```bash
   cd bk_business
   composer install
   ```
3. Copier `config/.env.example` en `.env` et adapter les valeurs :
   ```bash
   copy .env.example .env
   ```
4. Importer `database/bk_business.sql` dans MySQL via phpMyAdmin ou :
   ```bash
   mysql -u root -p < database/bk_business.sql
   ```
5. Accéder à l'application :
   ```bash
   composer serve
   ```

## Contrôle d'accès
Les routes sont désormais protégées par rôle :
- `DG` : administration complète, gestion des utilisateurs, paramétrage des commissions
- `COMPTABLE` : accès aux commissions et au paramétrage des commissions
- `SUPERVISEUR` : accès aux stocks, alertes et rapports
- `AGENT` : accès aux transactions

## Comptes par défaut (mot de passe : password)
| Email                        | Rôle        |
|------------------------------|-------------|
| dg@bkbusiness.cm             | DG          |
| superviseur@bkbusiness.cm    | SUPERVISEUR |
| comptable@bkbusiness.cm      | COMPTABLE   |
| agent@bkbusiness.cm          | AGENT       |

## Structure MVC
```
bk_business/
├── app/
│   ├── controllers/   ← Logique métier
│   ├── models/        ← Accès base de données
│   └── views/         ← Interfaces HTML
├── config/            ← Paramètres BDD et app
├── core/              ← Classes de base (Router, Model, Controller...)
├── database/          ← Script SQL
├── public/            ← Point d'entrée (index.php + .htaccess)
└── routes/            ← Définition des routes
```
