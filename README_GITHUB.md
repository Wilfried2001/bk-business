# BK Business - Plateforme de Gestion Commerciale Intelligente

<div align="center">

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Node.js](https://img.shields.io/badge/Node.js-339933?style=flat-square&logo=node.js&logoColor=white)](https://nodejs.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

Une application web robuste de gestion commerciale avec intégration d'intelligence artificielle pour l'analyse et l'optimisation des processus métier.

[Démarrage rapide](#installation) • [Documentation](#documentation) • [Features](#fonctionnalités) • [Architecture](#architecture)

</div>

---

## À propos

**BK Business** est une plateforme complète de gestion commerciale conçue pour les entreprises de services financiers. Elle combine une architecture MVC PHP classique avec des capacités d'analyse intelligente pour optimiser les transactions, gérer les commissions et générer des alertes commerciales pertinentes.

### Cas d'usage
- Gestion des transactions commerciales
- Analyse et reporting avancé
- Suivi des commissions et bonifications
- Système d'alertes intelligentes
- Gestion des stocks et inventaires
- Insights IA basés sur Claude et Groq

---

## Fonctionnalités principales

### Contrôle d'accès granulaire
- **DG** : Administration complète, gestion utilisateurs, configuration des commissions
- **COMPTABLE** : Gestion des commissions et paramétrage
- **SUPERVISEUR** : Stocks, alertes, rapports
- **AGENT** : Transactions commerciales

### Modules métier
- **Transactions** : Enregistrement et suivi des opérations commerciales
- **Commissions** : Configuration dynamique et calcul automatique
- **Alertes** : Système de seuils et notifications intelligentes
- **Rapports** : Génération d'analyses détaillées
- **Stocks** : Gestion d'inventaire en temps réel
- **Dashboard** : Vue d'ensemble des performances

### Intégration IA
- Analyse de données avec Claude et Groq
- Suggestions d'optimisation basées sur l'IA
- Logging et audit des opérations IA
- Chat interactif pour insights commerciaux

---

## Stack Technique

| Composant | Technologie |
|-----------|------------|
| **Backend** | PHP 8.0+ (MVC pur, sans framework) |
| **Frontend** | HTML5, CSS3, Tailwind CSS v3.4.4 |
| **Build Tool** | npm + PostCSS + Autoprefixer |
| **Base de données** | MySQL 8.0 / MariaDB |
| **APIs IA** | Claude (Anthropic), Groq |
| **Gestion dépendances** | npm (frontend), Composer (backend) |
| **Pattern** | MVC (Model-View-Controller) |

---

## Architecture

```
bk-business/
├── app/
│   ├── controllers/        # Logique métier
│   │   ├── AgentIAController.php
│   │   ├── TransactionController.php
│   │   ├── CommissionController.php
│   │   └── ...
│   ├── models/            # Accès base de données
│   │   ├── Transaction.php
│   │   ├── Utilisateur.php
│   │   ├── ApiClaude.php
│   │   ├── ApiGroq.php
│   │   └── ...
│   └── views/             # Templates HTML + Tailwind
│       ├── agent/
│       ├── transactions/
│       ├── commissions/
│       └── ...
├── config/                # Configuration app & BDD
├── core/                  # Classes de base MVC
│   ├── Router.php
│   ├── Database.php
│   ├── Controller.php
│   └── ...
├── database/              # Scripts SQL & migrations
├── public/                # Point d'entrée web
│   ├── index.php          # Front controller
│   └── css/
│       ├── style.css      # CSS compilé (généré)
│       └── business-views.css
├── src/                   # Source Tailwind CSS
│   └── css/
│       └── style.css      # Source (directives Tailwind)
├── routes/                # Définition des routes
├── package.json           # Dépendances npm (Tailwind)
├── tailwind.config.js     # Configuration Tailwind
├── postcss.config.js      # Configuration PostCSS
├── composer.json          # Dépendances PHP
└── vendor/                # Dépendances Composer (PHP)
```

---

## Installation

### Prérequis
- **PHP** 8.0+
- **MySQL** 8.0 / MariaDB
- **Node.js** 16+ (npm pour Tailwind CSS)
- **Composer** (dépendances PHP)
- **Git**

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/Wilfried2001/bk-business.git
cd bk-business
```

2. **Installer les dépendances npm (Tailwind CSS & PostCSS)**
```bash
npm install
```

3. **Installer les dépendances PHP (Composer)**
```bash
composer install
```

4. **Configurer l'environnement**
```bash
# Copier le fichier d'exemple (si disponible)
cp config/.env.example config/.env

# Éditer config/.env avec vos paramètres :
# - DB_HOST, DB_NAME, DB_USER, DB_PASSWORD
# - CLAUDE_API_KEY, GROQ_API_KEY (pour IA)
# - APP_NAME, BASE_URL, APP_ENV, APP_DEBUG
```

5. **Compiler les styles Tailwind CSS**
```bash
# Build une seule fois
npm run build:css

# Ou mode développement avec watch (recommandé)
npm run watch:css
```

6. **Initialiser la base de données**
```bash
mysql -u root -p bk_business < database/bk_business.sql
```

7. **Lancer l'application (dans un autre terminal)**
```bash
# Serveur PHP intégré (localhost:8000)
composer serve

# Alternative : Configurer Apache/Nginx avec document root = ./public/
```

8. **Accéder à l'application**
```
http://localhost:8000
```

---

## Build & Workflow de développement

### Pipeline Tailwind CSS

Tous les styles utilisent **Tailwind CSS v3.4.4** pour une maintenance simplifiée et des bundles CSS optimisés.

**Fichiers clés :**
- `src/css/style.css` — Source avec directives Tailwind (`@tailwind`, `@apply`, etc.)
- `public/css/style.css` — CSS compilé & minifié (généré automatiquement)
- `tailwind.config.js` — Configuration personnalisée et thème
- `postcss.config.js` — Plugins PostCSS (Tailwind, Autoprefixer)

**Workflow recommandé :**
```bash
# Terminal 1 : Watch Tailwind CSS (recompile au changement)
npm run watch:css

# Terminal 2 : Serveur PHP
composer serve
```

Les modifications aux fichiers `.php` des vues déclenchent automatiquement la recompilation CSS.

### Scripts disponibles

| Commande | Description |
|----------|-------------|
| `npm run build:css` | Compiler & minifier Tailwind (production) |
| `npm run watch:css` | Watch mode - recompile au changement (dev) |
| `composer serve` | Lancer le serveur PHP intégré (port 8000) |

### Personnalisation Tailwind

Le fichier `tailwind.config.js` définit un système de couleurs personnalisé :

```javascript
// tailwind.config.js
theme: {
  extend: {
    colors: {
      primary: "#0d47a1",    // Bleu professionnel
      secondary: "#1565c0",  // Bleu clair
      accent: "#ff6f00",     // Orange vibrant
      danger: "#d32f2f",     // Rouge
      success: "#388e3c",    // Vert
      warning: "#f57f17",    // Ambre
      info: "#0097a7",       // Cyan
    },
    boxShadow: {
      card: "0 4px 20px rgba(0, 0, 0, 0.1)",
      "card-hover": "0 8px 40px rgba(0, 0, 0, 0.15)",
    },
  },
}
```

---

## Authentification

### Comptes de démonstration

Tous les comptes utilisent le mot de passe par défaut : **`password`**

| Email | Rôle | Accès |
|-------|------|-------|
| `dg@bkbusiness.cm` | DG | Administration complète |
| `comptable@bkbusiness.cm` | COMPTABLE | Commissions & Paramétrage |
| `superviseur@bkbusiness.cm` | SUPERVISEUR | Stocks, Alertes, Rapports |
| `agent@bkbusiness.cm` | AGENT | Transactions |

**Sécurité** : Modifier les mots de passe par défaut en production et activer HTTPS.

---

## Documentation

Documentation détaillée disponible dans le repository :

- [SETUP_AGENT_IA.md](SETUP_AGENT_IA.md) - Guide d'installation complet
- [ARCHITECTURE_AGENT_IA.md](ARCHITECTURE_AGENT_IA.md) - Architecture IA et intégrations
- [QUICKSTART_AGENT_IA.md](QUICKSTART_AGENT_IA.md) - Démarrage rapide
- [CHECKLIST_AGENT_IA.md](CHECKLIST_AGENT_IA.md) - Checklist pré-déploiement

---

## Intégration APIs IA

### Configuration Claude (Anthropic)

```bash
# .env
CLAUDE_API_KEY=your-api-key-here
CLAUDE_MODEL=claude-3-sonnet-20240229
```

### Configuration Groq

```bash
# .env
GROQ_API_KEY=your-api-key-here
GROQ_MODEL=mixtral-8x7b-instant-v0.1
```

### Logging des opérations IA

Toutes les opérations IA sont loggées dans la table `agent_ia_logs` pour audit et débogage.

---

## Structure de la base de données

### Tables principales

| Table | Description |
|-------|-------------|
| `utilisateurs` | Comptes utilisateurs avec rôles RBAC |
| `transactions` | Opérations commerciales |
| `commissions` | Configuration & calcul des commissions |
| `commission_transactions` | Mapping transactions ↔ commissions |
| `alertes_solde` | Configuration des alertes |
| `mouvements_solde` | Historique des mouvements |
| `agences` | Succursales/Agences |
| `services` | Types de services/opérations |
| `agent_ia_logs` | Audit des opérations IA |
| `login_attempts` | Historique des tentatives de connexion |

### Initialisation

```bash
# Importer le schéma SQL complet
mysql -u root -p bk_business < database/bk_business.sql

# Scripts supplémentaires disponibles
ls database/2026_*
```

---

## Contribution

Les contributions sont bienvenues ! Pour contribuer :

1. **Fork** le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une **Pull Request**

---

## Licence

Ce projet est sous licence MIT.

---

## Auteur

**Wilfried2001** - [GitHub Profile](https://github.com/Wilfried2001)

---

## Support

Pour toute question, bug ou demande :
- [Créer une Issue](https://github.com/Wilfried2001/bk-business/issues)
- Consulter la [Documentation](./SETUP_AGENT_IA.md)

---

## Roadmap

- [ ] Tests unitaires complets (PHPUnit)
- [ ] API REST publique pour intégrations tierces
- [ ] Application mobile (React Native)
- [ ] Dashboard IA avancé avec graphiques temps réel
- [ ] Intégration blockchain pour audit immuable
- [ ] Export multi-formats (PDF, Excel, CSV)
- [ ] Webhooks pour notifications externes
- [ ] Cache distribué (Redis)

---

<div align="center">

**Si ce projet vous a été utile, n'hésitez pas à laisser une star !**

Fait avec amour par [Wilfried2001](https://github.com/Wilfried2001)

</div>
