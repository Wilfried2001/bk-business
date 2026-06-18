# ✅ CHECKLIST D'INSTALLATION — Agent IA BK_Business

**Utilisez ce document pour vérifier que tout fonctionne correctement**

---

## ÉTAPE 1 — Configuration (5 min)

### Configuration initiale
- [ ] Vous avez créé un compte sur https://console.anthropic.com/
- [ ] Vous avez généré une clé API (commençant par `sk-ant-`)
- [ ] Vous avez copié la clé complètement (sans espaces)
- [ ] Vous avez édité le fichier `.env` du projet
- [ ] Vous avez ajouté `ANTHROPIC_API_KEY=sk-ant-xxxxxx` dans `.env`
- [ ] Vous avez sauvegardé le fichier `.env`

**Vérification rapide** :
```bash
# Ouvrez .env et vérifiez la ligne:
ANTHROPIC_API_KEY=sk-ant-...

# Vous devriez voir quelque chose comme:
# ✅ ANTHROPIC_API_KEY=sk-ant-abc123def456ghi789
# ❌ Ne pas voir: ANTHROPIC_API_KEY= (vide)
```

---

## ÉTAPE 2 — Fichiers créés (10 min)

### Vérifier les fichiers
- [ ] Fichier existe : `app/controllers/AgentIAController.php`
- [ ] Fichier existe : `app/models/ApiClaude.php`
- [ ] Fichier existe : `app/views/agent/chat.php`
- [ ] Fichier existe : `config/agent.php`
- [ ] Fichier existe : `app/controllers/TestAgentController.php`

**Via le terminal** :
```bash
# Windows
dir app\controllers\AgentIAController.php
dir app\models\ApiClaude.php
dir app\views\agent\chat.php
dir config\agent.php
```

---

## ÉTAPE 3 — Routes configurées (5 min)

### Vérifier web.php
- [ ] Vous avez lu `routes/web.php`
- [ ] Vous voyez la ligne : `$router->get( '/agent'` ...
- [ ] Vous voyez la ligne : `$router->post('/api/agent/ask'` ...
- [ ] Vous voyez la ligne : `$router->get('/test-agent'` ...

**Vérification** :
```bash
# Cherchez dans routes/web.php:
grep -n "'/agent'" routes/web.php
grep -n "'/api/agent/ask'" routes/web.php
grep -n "'/test-agent'" routes/web.php

# Vous devriez avoir des résultats pour chaque ligne
```

---

## ÉTAPE 4 — Interface web intégrée (5 min)

### Vérifier footer.php
- [ ] Vous avez lu `app/views/layouts/footer.php`
- [ ] Vous voyez le bouton flottant (🤖) dans le code
- [ ] Vous voyez le modal d'IA
- [ ] Vous voyez le JavaScript pour le chat

**Test** :
```bash
# Visitez n'importe quelle page
http://localhost:8000/dashboard

# Vous devriez voir 🤖 en bas à droite
```

---

## ÉTAPE 5 — Tests automatiques (5 min)

### Lancer les tests
- [ ] Vous visitez http://localhost:8000/test-agent
- [ ] Vous voyez la page de tests
- [ ] La page affiche 6 tests

**Résultats attendus** :

### Test 1 — Configuration ✅
```
✅ Test 1 — Fichier de configuration
✅ config/agent.php existe
Configuration chargée :
- Modèle : claude-3-5-sonnet-20241022
- Max tokens : 1024
```

### Test 2 — ApiClaude ✅
```
✅ Test 2 — Classe ApiClaude
✅ app/models/ApiClaude.php existe
Méthodes publiques :
- call()
- testConnection()
- makeRequest()
```

### Test 3 — Clé API ⚠️ IMPORTANT
```
✅ Test 3 — Clé API Anthropic
✅ Clé API trouvée (format valide)
Clé : sk-ant-abc123def456...
```

**SI CE TEST ÉCHOUE** :
```
❌ Test 3 — Clé API Anthropic
❌ Clé API non trouvée

Solution : Vérifiez .env
ANTHROPIC_API_KEY=sk-ant-xxxxxx
```

### Test 4 — Base de données ✅
```
✅ Test 4 — Connexion à la base de données
✅ Connexion réussie
Utilisateurs dans la base : 2
Tables nécessaires :
✅ service
✅ transaction
✅ solde_service
```

### Test 5 — Contrôleur ✅
```
✅ Test 5 — Contrôleur AgentIA
✅ app/controllers/AgentIAController.php trouvé
Méthodes publiques :
- index()
- ask()
- rapport()
```

### Test 6 — Routes ✅
```
✅ Test 6 — Routes
Routes configurées :
✅ GET /agent
✅ POST /api/agent/ask
```

---

## ÉTAPE 6 — Interface de chat (10 min)

### Tester l'interface
- [ ] Vous visitez http://localhost:8000/agent
- [ ] Vous voyez le titre "Assistant IA BK_Business"
- [ ] Vous voyez 5 boutons de mode (Chat, Analyser, etc.)
- [ ] Vous voyez une zone de saisie texte
- [ ] Vous voyez les raccourcis rapides

**Premiers tests** :

#### Test Chat
```
1. Mode : Chat (sélectionné par défaut)
2. Question : "Combien d'utilisateurs dans la base ?"
3. Cliquez "Envoyer"
4. Vous devriez voir une réponse IA
```

#### Test Analyse
```
1. Mode : Analyser
2. Question : "Fais une analyse rapide"
3. Envoyez
4. Vous devriez voir : ✅ Points positifs / ⚠️ Points attention / 💡 Recommandations
```

---

## ÉTAPE 7 — Bouton flottant (5 min)

### Tester sur une page quelconque
- [ ] Vous visitez http://localhost:8000/dashboard
- [ ] Vous voyez 🤖 en bas à droite
- [ ] Vous cliquez sur 🤖
- [ ] Une modal apparaît
- [ ] Vous posez une question
- [ ] Vous recevez une réponse

---

## ÉTAPE 8 — Tests API (optionnel)

### Appel direct via cURL
```bash
curl -X POST http://localhost:8000/api/agent/ask \
  -H "Content-Type: application/json" \
  -d '{
    "question": "Bonjour",
    "mode": "chat"
  }'

# Vous devriez recevoir:
# {"success":true,"reponse":"...","mode":"chat","timestamp":"..."}
```

---

## 📊 Résumé de validation

### Checklist complète

```
CONFIGURATION       [ ] [ ] [ ] [ ] [ ]
FICHIERS           [ ] [ ] [ ] [ ] [ ]
ROUTES             [ ] [ ] [ ] [ ]
INTERFACE          [ ] [ ] [ ] [ ]
TESTS              [ ] [ ] [ ] [ ] [ ] [ ]
CHAT               [ ] [ ] [ ]
FLOTTANT           [ ] [ ] [ ]
API                [ ] [ ]

Total: ___ / 36 points
```

### Scoring

- **36/36** : 🟢 Prêt pour production
- **30-35** : 🟡 Quelques ajustements
- **< 30** : 🔴 À corriger avant utilisation

---

## 🆘 Dépannage

### Erreur : "Clé API non configurée"

```
❌ Test 3 échoue

Solutions:
1. Vérifiez .env existe
2. ANTHROPIC_API_KEY n'est pas vide
3. Pas d'espaces avant/après
4. Commence par sk-ant-
5. Au moins 50 caractères
```

### Erreur : "Fichier introuvable"

```
❌ Tests 1, 2, 5 échouent

Solutions:
1. Les fichiers ont bien été créés ?
2. Chemins corrects ?
3. Permissions lecture OK ?
4. Redémarrez le serveur PHP
```

### Erreur : "Erreur de connexion BD"

```
❌ Test 4 échoue

Solutions:
1. MySQL en marche ?
2. Credentials correctes dans .env ?
3. Basde données bk_business existe ?
4. Tables créées (bk_business.sql) ?
```

### Agent ne répond pas

```
❌ Interface dépond rien

Solutions:
1. Clé API valide ?
2. Internet OK ?
3. API Anthropic disponible ?
4. Logs PHP : /storage/logs/
```

---

## 🎯 Après la validation

### Si tous les tests passent ✅
1. Célébrez ! 🎉
2. Formez votre équipe
3. Testez avec des vraies données
4. Affinez les seuils
5. Lancez en production

### Si certains tests échouent
1. Identifiez le test échoué
2. Consultez la section "Dépannage"
3. Corrigez le problème
4. Relancez les tests
5. Recommencez jusqu'à 36/36

---

## 📞 Points de contact

| Problème | Ressource |
|----------|-----------|
| Configuration | [QUICKSTART](QUICKSTART_AGENT_IA.md) |
| Utilisation | [DOCUMENTATION](AGENT_IA_DOCUMENTATION.md) |
| Architecture | [ARCHITECTURE](ARCHITECTURE_AGENT_IA.md) |
| Installation | [SETUP](SETUP_AGENT_IA.md) |
| API Claude | [Docs Anthropic](https://docs.anthropic.com/) |

---

## ✅ Sign-off

```
Date validation    : ___________________
Validateur         : ___________________
Commentaires       : ___________________

Status final       : 🟢 OK / 🟡 À revoir / 🔴 Erreurs

Autorisé GO LIVE   : OUI / NON
```

---

**Version** : 1.0.0  
**Dernière mise à jour** : 15/06/2026  
**Validité** : À jour
