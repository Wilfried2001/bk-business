# 🚀 Guide de démarrage rapide — Agent IA BK_Business

## ⚡ 3 étapes pour démarrer

### Étape 1 — Obtenir la clé API Claude

1. Allez sur [console.anthropic.com](https://console.anthropic.com/)
2. Créez un compte (gratuit)
3. Générez une **clé API** (onglet "API Keys")
4. **Copiez-la** complètement

### Étape 2 — Configurer l'application

1. Ouvrez le fichier `.env` à la racine du projet
2. Trouvez la ligne `ANTHROPIC_API_KEY`
3. **Collez votre clé** :

```bash
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxxxxxx
```

4. **Sauvegardez le fichier**

### Étape 3 — Utiliser l'Agent IA

**Option A** — Interface complète

```
http://localhost:8000/agent
```

**Option B** — Bouton flottant (depuis n'importe quelle page)

Cliquez sur 🤖 en bas à droite

---

## 💬 Premiers tests

### Test 1 — Chat simple

1. Ouvrez l'interface chat
2. Mode : **Chat** (par défaut)
3. Question : "Combien on a fait aujourd'hui ?"
4. Envoyez

**Attendu** : Réponse avec les chiffres du jour

### Test 2 — Analyser les données

1. Changez le mode en **Analyser**
2. Question : "Fais une analyse de la situation actuelle"
3. Envoyez

**Attendu** : Points positifs / d'attention / recommandations

### Test 3 — Vérifier les alertes

1. Mode : **Chat**
2. Question : "Quelles sont les alertes actives ?"
3. Envoyez

**Attendu** : Liste des alertes (ou "Aucune alerte")

---

## 🆘 Si ça ne fonctionne pas

### Erreur "Clé API non configurée"

```bash
❌ Clé API Anthropic non configurée
```

**Vérifiez** :
- ✅ Fichier `.env` existe
- ✅ Ligne `ANTHROPIC_API_KEY=sk-ant-...` présente
- ✅ Pas de symboles cachés (espaces avant/après)

### Erreur "Erreur réseau"

```bash
❌ Erreur réseau : ...
```

**Causes probables** :
- Pas de connexion Internet
- Clé API invalide
- API Anthropic indisponible

**Solution** : Attendez 1-2 minutes et réessayez

### Claude ne répond pas aux questions

**Cause** : Les données ne sont pas dans la base

**Solutions** :
- Ajoutez des données de test (transactions, soldes)
- Utilisez des questions génériques
- Consultez le mode **Analyse** pour voir les données récoltées

---

## 📚 Fichiers créés / modifiés

### Fichiers **CRÉÉS** 

```
app/controllers/AgentIAController.php   ← Cerveau du système
app/models/ApiClaude.php                ← Intégration API
app/views/agent/chat.php                ← Interface de chat
config/agent.php                        ← Configuration
```

### Fichiers **MODIFIÉS**

```
routes/web.php                          ← Ajout des routes agent
app/views/layouts/footer.php            ← Ajout bouton flottant
.env.example                            ← Ajout ANTHROPIC_API_KEY
```

### Fichiers **DOCUMENTATION**

```
AGENT_IA_DOCUMENTATION.md               ← Doc complète
QUICKSTART_AGENT_IA.md                  ← Ce fichier
```

---

## 📞 Questions fréquentes

**Q : Où voit-on les données ?**  
R : Dans le prompt envoyé à Claude. Vous ne les voyez pas directement, mais Claude les utilise.

**Q : Est-ce que ça coûte cher ?**  
R : Claude facture à l'usage (par token). Les réponses courtes coûtent quelques centimes.

**Q : Peut-on utiliser un autre modèle IA ?**  
R : Oui, en modifiant `config/agent.php` → `'model'`.

**Q : Les données sont envoyées à Anthropic ?**  
R : Oui, le prompt avec les données est envoyé à l'API Anthropic (chiffré en HTTPS).

**Q : Comment enlever le bouton flottant ?**  
R : Commentez le code dans `footer.php` (section "Bouton flottant Agent IA").

---

## 🎯 Prochaines étapes

1. ✅ Configuration terminée
2. ✅ Tests passés
3. 📊 Enrichir la base de données avec des vraies transactions
4. ⚙️ Affiner les seuils d'alerte dans `config/agent.php`
5. 🎨 Personnaliser le prompt système
6. 📱 Intégrer à d'autres services (SMS, Email)

---

## 🔗 Ressources

- 📖 [Documentation complète](AGENT_IA_DOCUMENTATION.md)
- 🔗 [API Anthropic Claude](https://docs.anthropic.com/)
- 💬 [Forum Anthropic](https://community.anthropic.com/)

---

**Dernière mise à jour** : 15/06/2026  
**Status** : ✅ Prêt pour production
