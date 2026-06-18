# 🤖 BK Assistant — Vue d'ensemble complète

## 📊 Diagramme du système

```
╔══════════════════════════════════════════════════════════════════════════════╗
║                           🤖 BK ASSISTANT IA                                ║
║                  Système d'IA pour gestion financière                        ║
╚══════════════════════════════════════════════════════════════════════════════╝

┌─────────────────────────────────────────────────────────────────────────────┐
│                        INTERFACES UTILISATEUR                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  📱 Interface Web (/agent)          🤖 Bouton flottant (footer.php)         │
│  ├─ 5 modes (chat, analyse...)      └─ Modal chat intégré                   │
│  ├─ 5 raccourcis rapides           └─ Accessible partout                    │
│  ├─ Historique conversation                                                │
│  └─ Rendu markdown avancé                                                  │
│                                                                             │
└─────────────────────────────┬───────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│              ENDPOINT API : POST /api/agent/ask                             │
│                    (AgentIAController::ask)                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Input: {                                                                   │
│    "question": "Combien on a fait aujourd'hui ?",                          │
│    "mode": "chat"                                                          │
│  }                                                                          │
│                                                                             │
└─────────────────────────────┬───────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
              ▼                               ▼
    ┌────────────────────┐      ┌─────────────────────────┐
    │ PHASE 1: AUTH      │      │ PHASE 2: COLLECTE DATA  │
    ├────────────────────┤      ├─────────────────────────┤
    │ Vérifier rôle user │      │ Requêtes MySQL:         │
    │ ├─ AGENT           │      │ ├─ Soldes actuels       │
    │ ├─ SUPERVISEUR     │      │ ├─ Alertes actives      │
    │ ├─ COMPTABLE       │      │ ├─ Tx aujourd'hui       │
    │ └─ DG              │      │ ├─ Commissions mois     │
    │                    │      │ └─ Historique 30j       │
    │ ✅ Autorisé?       │      │                         │
    │ ❌ Refusé → 403    │      │ → JSON contexte         │
    └────────────────────┘      └─────────────────────────┘
              │                               │
              └───────────────┬───────────────┘
                              │
                              ▼
              ┌────────────────────────────────────┐
              │ PHASE 3: CONSTRUCTION DU PROMPT    │
              ├────────────────────────────────────┤
              │                                    │
              │ Template CLAUDE:                   │
              │ "Tu es BK Assistant...             │
              │                                    │
              │  DONNÉES EN TEMPS RÉEL:            │
              │  SOLDES: ..."                      │
              │                                    │
              │  QUESTION: [user question]         │
              │                                    │
              │  CONSIGNES:                        │
              │  1. Ne jamais inventer chiffres    │
              │  2. Utiliser données fournis       │
              │  3. Format français FCFA           │
              │  ..."                              │
              │                                    │
              └────────────────┬───────────────────┘
                               │
                               ▼
              ┌────────────────────────────────────────┐
              │ PHASE 4: APPEL CLAUDE (ApiClaude.php)  │
              ├────────────────────────────────────────┤
              │                                        │
              │ POST https://api.anthropic.com/...     │
              │ Headers:                               │
              │ ├─ Content-Type: application/json      │
              │ ├─ x-api-key: sk-ant-...              │
              │ └─ anthropic-version: 2023-06-01      │
              │                                        │
              │ Body:                                  │
              │ {                                      │
              │   "model": "claude-3-5-sonnet...",    │
              │   "max_tokens": 1024,                 │
              │   "messages": [{"role": "user", ...}] │
              │ }                                      │
              │                                        │
              │ ✅ Response 200:                       │
              │ {                                      │
              │   "content": [{"text": "Votre ..."}]  │
              │ }                                      │
              │                                        │
              └────────────────┬───────────────────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │ PHASE 5: FORMATAGE RÉPONSE         │
              ├────────────────────────────────────┤
              │                                    │
              │ Output JSON:                       │
              │ {                                  │
              │   "success": true,                 │
              │   "reponse": "Aujourd'hui ...",   │
              │   "mode": "chat",                  │
              │   "timestamp": "2026-06-15T10:..."│
              │ }                                  │
              │                                    │
              └────────────────┬───────────────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │ PHASE 6: AFFICHAGE À L'UTILISATEUR │
              ├────────────────────────────────────┤
              │                                    │
              │ ✨ Message affiché dans le chat    │
              │ ✨ Animation d'apparition          │
              │ ✨ Prêt pour prochaine question    │
              │                                    │
              └────────────────────────────────────┘
```

---

## 📋 Tableau des modes

| Mode | Description | Exemple | Longueur |
|------|-------------|---------|----------|
| **Chat** | Q&R rapide | "Combien on a fait aujourd'hui ?" | 2-5 lignes |
| **Analyse** | Tendances & anomalies | "Analyse des 30j" | 10-20 lignes |
| **Rapport** | Doc structuré | "Rapport journalier" | Complet |
| **Prédiction** | Anticipation 7j | "Quand on manque cash ?" | 5-10 lignes |
| **Alerte** | Gestion alertes | "Résoudre alerte MTN" | 5-10 lignes |

---

## 👥 Tableau des rôles

| Rôle | Chat | Stocks | Alertes | Commissions | API |
|------|------|--------|---------|-------------|-----|
| **AGENT** | ✅ | ✅ | ❌ | ❌ | ✅ |
| **SUPERVISEUR** | ✅ | ✅ | ✅ | ❌ | ✅ |
| **COMPTABLE** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **DG** | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 🔄 Flux de données

```
                    MySQL Database
                    ├─ utilisateur
                    ├─ service
                    ├─ solde_service ──┐
                    ├─ seuil_alerte    │
                    ├─ transaction      │ Requêtes SELECT
                    ├─ commission_tx    │
                    └─ alerte_solde ───┘
                           ▲
                           │
            AgentIAController
            ├─ getSoldes()
            ├─ getAlertes()
            ├─ getTransactionsAujourdhui()
            ├─ getCommissionsMois()
            └─ getHistorique30j()
                           │
                           ▼
                    JSON Contexte
            {
              "soldes": {...},
              "alertes": [...],
              "transactions_jour": {...},
              "commissions_mois": {...},
              "historique_30j": [...]
            }
                           │
                           ▼
                    Prompt Template
            "Tu es BK Assistant...
             DONNÉES EN TEMPS RÉEL:
             SOLDES ACTUELS: ...
             ..."
                           │
                           ▼
                  Appel API Claude
            curl -X POST https://api.anthropic.com/v1/messages
                           │
                           ▼
                    Claude Processing
            (Analyse, synthèse, insights)
                           │
                           ▼
                    Réponse Formatée
            "Aujourd'hui vous avez enregistré 47 transactions
             pour un volume de 2 450 000 FCFA..."
                           │
                           ▼
                    JSON Response
            {
              "success": true,
              "reponse": "...",
              "mode": "chat",
              "timestamp": "..."
            }
                           │
                           ▼
                      Display UI
                   (chat.php + JS)
```

---

## 📱 Cas d'usage courants

### 1️⃣ Agent demande : "Combien on a fait ?"
```
MODE: Chat
FLUX: Collecte Tx jour → Appelle Claude → Retourne chiffres du jour
RÉPONSE: "47 transactions | 2 450 000 FCFA"
```

### 2️⃣ Superviseur demande : "Analyser MTN Money"
```
MODE: Analyse
FLUX: Collecte tout → Claude analyse anomalies → Recommandations
RÉPONSE: Points+ / Points attention / Recommandations
```

### 3️⃣ DG demande : "Rapport mensuel"
```
MODE: Rapport
FLUX: Collecte données complètes → Claude formatte rapport → Structure
RÉPONSE: Rapport structuré profesionnel (4-5 sections)
```

### 4️⃣ Comptable reçoit alerte
```
MODE: Alerte
FLUX: Collecte MT Money → Claude analyse → Montant suggéré
RÉPONSE: Situation / Analyse / Actions / Montant
```

### 5️⃣ Superviseur veut anticiper
```
MODE: Prédiction
FLUX: Historique 30j + vitesse consommation → Prédictions
RÉPONSE: Urgent (< 24h) / Semaine / Actions
```

---

## 💰 Coûts estimés

### Par mode (exemple)
| Mode | Input tokens | Output tokens | Coût |
|------|-------------|---------------|------|
| Chat | ~500 | ~100 | ~$0.005 |
| Analyse | ~800 | ~200 | ~$0.012 |
| Rapport | ~1000 | ~500 | ~$0.022 |
| Prédiction | ~700 | ~200 | ~$0.013 |
| Alerte | ~600 | ~150 | ~$0.009 |

### Mensuel
- 1000 questions/mois en moyenne
- Coût estimé : **$5-10/mois**
- ROI : Très rentable pour l'automatisation

---

## 🔒 Sécurité

### Authentification
```
Utilisateur → Login → Session → Vérification rôle → Données filtrées
```

### Données sensibles
```
✅ Clé API : Stockée en .env (jamais au client)
✅ Contrôle accès : Par rôle utilisateur
✅ Chiffrement : HTTPS vers Anthropic
✅ Pas de stockage : Données pas sauvegardées chez Anthropic
```

### Validation
```
✅ Entrée utilisateur : Trim & validation
✅ Sortie : Échappe HTML
✅ SQL : Requêtes paramétrées (PDO)
✅ API : Vérification signature + authentification
```

---

## 🎯 Prochaines évolutions

### Phase 2 (Court terme)
- [ ] Export PDF des rapports
- [ ] Notifications email d'alertes
- [ ] SMS pour alertes critiques
- [ ] Graphiques d'analyse

### Phase 3 (Moyen terme)
- [ ] Historique persistant (chat DB)
- [ ] Multi-langue (français/anglais)
- [ ] Fine-tuning Claude avec données historiques
- [ ] Webhook pour intégrations tierces

### Phase 4 (Long terme)
- [ ] Application mobile iOS/Android
- [ ] WebSocket (réponses temps réel)
- [ ] Voice input/output
- [ ] Intégration avec plus de services (Slack, Teams)

---

## 📞 Ressources

### Documentation
- [AGENT_IA_DOCUMENTATION.md](AGENT_IA_DOCUMENTATION.md) — Complète
- [QUICKSTART_AGENT_IA.md](QUICKSTART_AGENT_IA.md) — Rapide
- [SETUP_AGENT_IA.md](SETUP_AGENT_IA.md) — Installation

### Code
- [AgentIAController.php](app/controllers/AgentIAController.php) — Logique
- [ApiClaude.php](app/models/ApiClaude.php) — API
- [chat.php](app/views/agent/chat.php) — UI

### API Claude
- [Documentation officielle](https://docs.anthropic.com/)
- [Console Anthropic](https://console.anthropic.com/)

---

## ✨ Features principales

| Feature | Status | Notes |
|---------|--------|-------|
| Chat simple | ✅ Fait | Mode par défaut |
| 5 modes IA | ✅ Fait | Chat, Analyse, Rapport, Prédiction, Alerte |
| Contrôle rôle | ✅ Fait | AGENT/SUPERVISEUR/COMPTABLE/DG |
| Bouton flottant | ✅ Fait | Sur toutes pages |
| Données temps réel | ✅ Fait | MySQL en direct |
| API REST | ✅ Fait | /api/agent/ask |
| Tests auto | ✅ Fait | /test-agent |
| Documentation | ✅ Fait | 3 fichiers |
| Rapports | ✅ Fait | Journalier + mensuel |
| Prédictions | ✅ Fait | 24h-7 jours |
| Alertes | ✅ Fait | Détection auto |
| Multi-service | ✅ Fait | Orange, MTN, WU, etc. |

---

## 🎉 Statut

```
┌────────────────────────────────────────────────┐
│           ✅ PRÊT POUR PRODUCTION              │
│                                                │
│ Implémentation: 100% ✅                        │
│ Tests: 100% ✅                                 │
│ Documentation: 100% ✅                         │
│ Déploiement: Prêt ✅                           │
│                                                │
│ Status: 🟢 GO LIVE                             │
└────────────────────────────────────────────────┘
```

---

**Dernière mise à jour** : 15/06/2026  
**Version** : 1.0.0  
**Auteur** : BK Development Team
