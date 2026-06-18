# 🤖 Assistant IA BK_Business — Documentation Complète

## 📋 Table des matières

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Architecture](#architecture)
4. [Utilisation](#utilisation)
5. [Modes de fonctionnement](#modes)
6. [Accès par rôle](#acces)
7. [Dépannage](#troubleshooting)

---

## 🔧 Installation

### 1. Clé API Anthropic

L'Agent IA utilise **Claude 3.5 Sonnet** d'Anthropic.

1. Créez un compte sur [console.anthropic.com](https://console.anthropic.com/)
2. Générez une clé API
3. Copiez la clé

### 2. Variables d'environnement

Dans votre fichier `.env` à la racine du projet :

```bash
# Ajouter cette ligne
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxxxxx
```

### 3. Structure de fichiers

Les fichiers ont été créés automatiquement :

```
app/
├── controllers/
│   └── AgentIAController.php       ← Cerveau du système
├── models/
│   └── ApiClaude.php               ← Intégration API Claude
└── views/
    └── agent/
        └── chat.php                ← Interface de chat

config/
└── agent.php                       ← Configuration

routes/
└── web.php                         ← Routes (déjà modifié)

app/views/layouts/
└── footer.php                      ← Bouton flottant (déjà modifié)
```

---

## ⚙️ Configuration

### Fichier : `config/agent.php`

Contrôle la plupart des comportements :

```php
'model' => 'claude-3-5-sonnet-20241022',  // Modèle Claude
'max_tokens' => 1024,                      // Longueur max réponse
'roles' => [...],                          // Accès par rôle
'modes' => [...],                          // Modes disponibles
'thresholds' => [...],                     // Seuils critiques
```

### Fichier : `app/models/ApiClaude.php`

Gère la communication avec l'API Claude via cURL.

---

## 🏗️ Architecture

### Flux de données

```
Utilisateur
    ↓
Interface Chat (chat.php ou bouton flottant)
    ↓
POST /api/agent/ask (AgentIAController::ask)
    ↓
[1] Collecte données MySQL (soldes, alertes, transactions, commissions)
[2] Construit prompt contextualisé
[3] Appelle Claude via ApiClaude
[4] Retourne réponse JSON
    ↓
Affichage dans l'interface
```

### Classe : AgentIAController

**Méthode `ask()`** — Endpoint principal

- Reçoit : `{ question, mode }`
- Retourne : `{ success, reponse, mode, timestamp }`

**Méthode `collecteDataRealtime()`** — Agrège toutes les données

- `getSoldes()` — États actuels des floats et caisses
- `getAlertes()` — Alertes actives
- `getTransactionsAujourdhui()` — Mouvements du jour
- `getCommissionsMois()` — Revenus du mois
- `getHistorique30j()` — Tendances

**Méthode `construirPrompt()`** — Formule le contexte

Construit un prompt du type :

```
Tu es BK Assistant...

DONNÉES EN TEMPS RÉEL :

SOLDES ACTUELS :
  Orange Money :
    FLOAT: 75 000 FCFA (seuil: 50 000 FCFA) ✅ OK
    CAISSE: 125 000 FCFA (seuil: 100 000 FCFA) ✅ OK

  MTN Money :
    FLOAT: 40 000 FCFA (seuil: 50 000 FCFA) ⚠️ ALERTE
    ...

ALERTES ACTIVES (2) :
  - MTN Money (FLOAT): 40 000 FCFA (seuil: 50 000 FCFA)
  ...

TRANSACTIONS AUJOURD'HUI :
  Nombre: 47 | Volume: 2 450 000 FCFA

COMMISSIONS CE MOIS :
  Total: 253 100 FCFA

QUESTION DE L'UTILISATEUR :
[Question de l'utilisateur]

CONSIGNES :
1. Utilise UNIQUEMENT les données fournies ci-dessus
2. Ne jamais inventer de chiffres
3. ...
```

---

## 💬 Utilisation

### 1. Interface principale : `/agent`

Accès via menu ou directement :

```
http://localhost:8000/agent
```

**Zone de chat** — Les 5 modes :

- 💬 **Chat** — Réponse rapide
- 📊 **Analyser** — Tendances et anomalies
- 📄 **Rapport** — Rapport structuré
- 🔮 **Prédire** — Anticipation 24h-7j
- 🚨 **Alerte** — Gestion des alertes

**Raccourcis** — Boutons prédéfinis :

- "Quel est l'état des stocks ?"
- "Combien de transactions aujourd'hui ?"
- "Quelles sont les alertes actives ?"
- "Combien de commissions ce mois ?"
- "Fais une analyse des 30 derniers jours"

### 2. Bouton flottant (toutes les pages)

Accessible depuis n'importe quelle page en bas à droite : 🤖

- Modal léger avec chat intégré
- Utilise le mode Chat par défaut
- N'interrompt pas le travail en cours

### 3. Appel API direct

```bash
curl -X POST http://localhost:8000/api/agent/ask \
  -H "Content-Type: application/json" \
  -d '{
    "question": "Combien on a fait aujourd'"'"'hui ?",
    "mode": "chat"
  }'
```

Réponse :

```json
{
  "success": true,
  "reponse": "Aujourd'hui vous avez enregistré 47 transactions pour un volume total de 2 450 000 FCFA.",
  "mode": "chat",
  "timestamp": "2026-06-15T10:30:00+00:00"
}
```

---

## 🎯 Modes de fonctionnement

### MODE 1 — CHAT (Questions libres)

**Utilisation** : Questions rapides du jour

**Exemple** :
- Q : "Combien on a fait aujourd'hui ?"
- R : "Aujourd'hui vous avez enregistré 47 transactions pour un volume total de 2 450 000 FCFA. Le service le plus actif est Orange Money."

**Longueur** : 2-5 lignes

---

### MODE 2 — ANALYSE (Tendances et anomalies)

**Utilisation** : Compréhension du contexte

**Analyse automatique** :
- Volumes vs moyennes 30j
- Transactions > 3x la moyenne
- Services à surveiller
- Heures de pic

**Format** :

```
📊 ANALYSE — 15/06/2026

✅ Points positifs :
• Orange Money en hausse de 15% vs la semaine dernière
• Toutes les caisses sont au-dessus des seuils

⚠️ Points d'attention :
• MTN Money FLOAT à 40 000 FCFA (sous le seuil de 50 000)
• Activité basse le samedi (préparer le rechargement vendredi)

💡 Recommandations :
1. Recharger MTN Money dès aujourd'hui
2. Augmenter les seuils Orange Money (demande en hausse)
3. Monitorer les transactions > 500 000 FCFA
```

**Longueur** : 10-20 lignes

---

### MODE 3 — RAPPORT (Génération automatique)

**Utilisation** : Rapports pour les superviseurs/DG

**Types** :
- Rapport journalier (complet)
- Rapport mensuel pour le DG

**Format** :

```
═══════════════════════════════════════
  RAPPORT JOURNALIER — BK_BUSINESS
  15 juin 2026 — 23:59:59
═══════════════════════════════════════

1. RÉSUMÉ EXÉCUTIF
   • Transactions : 47 opérations
   • Volume total : 2 450 000 FCFA
   • Commissions générées : 14 700 FCFA

2. PERFORMANCE PAR SERVICE
   | Service         | Transactions | Volume      | Commissions |
   |-----------------|-------------|-------------|-------------|
   | Orange Money    | 25          | 1 200 000   | 8 400 FCFA  |
   | MTN Money       | 15          | 900 000     | 4 500 FCFA  |
   | Western Union   | 7           | 350 000     | 1 800 FCFA  |

3. ÉTAT DES STOCKS
   [Tableau des soldes avec alertes]

   ⚠️ Alertes actives : MTN Money FLOAT

4. INCIDENTS ET ANOMALIES
   • Aucun incident majeur détecté

5. RECOMMANDATIONS
   • Recharger MTN Money
   • Surveiller la tendance à la baisse du samedi

═══════════════════════════════════════
  Généré par BK Assistant le 15/06/2026 23:59:59
═══════════════════════════════════════
```

---

### MODE 4 — PRÉDICTION (Anticipation 24h-7j)

**Utilisation** : Planifier à l'avance

**Prédictions** :
- Jours à risque de rupture
- Volumes anticipés
- Rechargements recommandés

**Format** :

```
🔮 PRÉDICTIONS — Horizon 7 jours

⚡ URGENT (< 24h) :
- MTN Money FLOAT atteindra zéro dans 2 jours
  → Recharger 100 000 FCFA AVANT MERCREDI

📅 CETTE SEMAINE :
- Vendredi très actif (+30% vs moyenne)
  → Recharger jeudi soir
- Samedi activité basse (préparer rechargement)

💡 ACTIONS RECOMMANDÉES :
→ Recharger MTN Money dès aujourd'hui
→ Préparer 500 000 FCFA pour jeudi
→ Vérifier les fournisseurs (délai 24h)
```

---

### MODE 5 — ALERTE (Résolution d'alertes)

**Utilisation** : Gérer une alerte spécifique

**Format** :

```
🚨 ALERTE — MTN Money (FLOAT)

Situation actuelle :
  Solde actuel  : 40 000 FCFA
  Seuil minimum : 50 000 FCFA
  Écart         : -10 000 FCFA

Analyse :
  La caisse MTN Money est en dessous du seuil depuis 2 jours.
  Au rythme actuel (5 000 FCFA/jour), elle atteindra 0 dans 8 jours.

Actions immédiates recommandées :
  1. Recharger 100 000 FCFA dès aujourd'hui
  2. Contacter le fournisseur MTN Money
  3. Vérifier les transactions anormales

Montant de rechargement suggéré :
  150 000 FCFA
  (basé sur la moyenne des 7 derniers jours)
```

---

## 👥 Accès par rôle

Défini dans `config/agent.php` :

| Rôle | Accès | Exclusions |
|------|-------|-----------|
| **AGENT** | Transactions, soldes | ❌ Commissions |
| **SUPERVISEUR** | Transactions, soldes, alertes | - |
| **COMPTABLE** | Tout + commissions détaillées | - |
| **DG** | Accès COMPLET | - |

**Implémentation** :

```php
// Dans le prompt
$roleInfo = match($user['role']) {
    'AGENT' => 'Tu as accès aux données de base (transactions, soldes) mais NON aux commissions.',
    'SUPERVISEUR' => 'Tu as accès aux soldes, alertes et transactions.',
    // ...
};
```

---

## 🆘 Dépannage

### Erreur : "Clé API non configurée"

**Solution** :
1. Vérifiez le fichier `.env`
2. Ajoutez : `ANTHROPIC_API_KEY=sk-ant-...`
3. Rechargez la page

### Erreur : "Erreur réseau"

**Solution** :
1. Vérifiez votre connexion Internet
2. Vérifiez que l'API Anthropic est accessible
3. Vérifiez la clé API (pas d'espaces, valide)

### Réponse lente

**Causes** :
- Base de données volumineuse (optimiser les requêtes)
- Réseau lent
- Limite de tokens dépassée

**Solutions** :
- Réduire `max_tokens` dans `config/agent.php`
- Optimiser les index MySQL
- Limiter l'historique 30j à 15j

### Claude ne répondrait pas aux questions spécifiques

**Cause** : Données manquantes ou pas de seuil suffisant

**Solution** : Utiliser le mode **Analyse** pour voir les données collectées

---

## 📝 Exemples de questions

### Chat
- "Combien on a fait aujourd'hui ?"
- "Quel est l'état du float Orange Money ?"
- "Qui a fait le plus de transactions ce mois ?"

### Analyse
- "Fais une analyse des 30 derniers jours"
- "Y a-t-il des anomalies dans les transactions ?"
- "Quels services sont rentables ?"

### Rapport
- "Génère un rapport journalier"
- "Rapport mensuel pour le DG"

### Prédiction
- "Quand on va manquer de cash ?"
- "Quel jour de la semaine est le plus chargé ?"
- "Anticipe mes besoins pour cette semaine"

### Alerte
- "Aide-moi à gérer cette alerte MTN Money"
- "Qu'est-ce que je dois faire pour Orange Money ?"

---

## 📞 Support

Pour plus de détails, consultez :
- [Documentation Anthropic Claude](https://docs.anthropic.com/)
- Code source : `app/controllers/AgentIAController.php`
- Configuration : `config/agent.php`

---

**Version** : 1.0.0  
**Dernière mise à jour** : 15/06/2026  
**Auteur** : BK Assistant
