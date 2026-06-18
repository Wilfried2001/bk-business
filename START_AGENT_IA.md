# 🚀 START HERE — Agent IA BK_Business

**Vous êtes pressé ? Commencez ici en 3 minutes.**

---

## ⚡ Les 3 étapes du démarrage

### Étape 1️⃣ — Clé API (2 min)
```
Allez sur :  https://console.anthropic.com/
Créez compte : Gratuit, email + mot de passe
Générez clé  : API Keys → Create Key → Copier (sk-ant-...)
```

### Étape 2️⃣ — Configuration (1 min)
```
Ouvrez fichier : .env (à la racine du projet)
Trouvez ligne   : ANTHROPIC_API_KEY=
Collez clé      : ANTHROPIC_API_KEY=sk-ant-xxxxx
Sauvegardez     : Ctrl+S
```

### Étape 3️⃣ — Lancez ! (voilà !)
```
Visitez        : http://localhost:8000/test-agent
Vérifiez tests : Tous les tests doivent être ✅
Allez au chat  : http://localhost:8000/agent
Posez question : "Bonjour"
Recevez réponse: ✨
```

---

## 📊 C'est quoi ce truc ?

**Agent IA** = Une intelligence artificielle qui comprend vos données financières

**Ce qu'elle fait** :
- 💬 Répond à vos questions (Chat)
- 📊 Analyse vos tendances (Analyse)
- 📄 Génère vos rapports (Rapport)
- 🔮 Prédit vos besoins (Prédiction)
- 🚨 Gère vos alertes (Alerte)

**Comment** : Récupère vos vraies données MySQL, les analyse avec Claude (IA puissante), vous renvoi une réponse intelligente.

---

## 🎯 Utilisation immédiate

### Vous êtes Agent ?
```
Mode: Chat
Question: "Combien on a fait aujourd'hui ?"
Réponse: "47 transactions | 2 450 000 FCFA"
```

### Vous êtes Superviseur ?
```
Mode: Analyser
Question: "Fais une analyse"
Réponse: "✅ Points positifs / ⚠️ Attention / 💡 Actions"
```

### Vous êtes Comptable ?
```
Mode: Rapport
Question: "Rapport journalier"
Réponse: "Rapport complet et structuré"
```

### Vous êtes DG ?
```
Mode: Rapport
Question: "Rapport mensuel"
Réponse: "Synthèse complète avec KPI"
```

---

## 📚 Dokumentation

| Fichier | Durée | Quand |
|---------|-------|-------|
| [README_AGENT_IA.md](README_AGENT_IA.md) | 5 min | Maintenant |
| [QUICKSTART_AGENT_IA.md](QUICKSTART_AGENT_IA.md) | 5 min | Après démarrage |
| [AGENT_IA_DOCUMENTATION.md](AGENT_IA_DOCUMENTATION.md) | 30 min | Plus tard |
| [ARCHITECTURE_AGENT_IA.md](ARCHITECTURE_AGENT_IA.md) | 20 min | Si développeur |

---

## 🆘 Ça ne marche pas ?

### Test échoue : "Clé API non trouvée"
```
❌ Vérifiez:
✅ .env existe à la racine
✅ ANTHROPIC_API_KEY=sk-ant-xxxxx (pas vide)
✅ Pas d'espaces avant/après
✅ Commençe par sk-ant-
✅ Sauvegardé (Ctrl+S)
```

### Interface ne charge pas
```
❌ Vérifiez:
✅ Visitez http://localhost:8000/agent
✅ PHP en marche
✅ MySQL en marche
✅ Routes mises à jour
```

### Agent ne répond pas
```
❌ Vérifiez:
✅ Clé API valide (testez via /test-agent)
✅ Internet OK
✅ Question claire (ex: "Bonjour")
```

---

## ✅ Checklist 2 min

- [ ] Vous avez créé compte Anthropic
- [ ] Vous avez généré clé API (sk-ant-...)
- [ ] Vous avez édité .env
- [ ] Vous avez ajouté ANTHROPIC_API_KEY=sk-ant-...
- [ ] Vous avez sauvegardé .env
- [ ] Vous visitez http://localhost:8000/test-agent
- [ ] Tous les tests sont ✅
- [ ] Vous visitez http://localhost:8000/agent
- [ ] Interface charge
- [ ] Vous posez une question
- [ ] Vous recevez une réponse

**Quand tout est ✅ → Bravo ! 🎉**

---

## 🎯 Prochains pas

1. **Familiarisez-vous** (10 min)
   - Testez les 5 modes
   - Posez des questions variées

2. **Formez l'équipe** (30 min)
   - Montrez l'interface
   - Expliquez les 5 modes

3. **Affinez config** (1h)
   - Lisez config/agent.php
   - Ajustez les seuils

4. **Lancez en prod** (demain)
   - Déployer
   - Monitorer
   - Optimiser

---

## 💡 Tips et tricks

**Accès rapide**
```
Interface complète : /agent
Bouton flottant    : 🤖 en bas à droite
Tests             : /test-agent
```

**Utilisation avisée**
```
Chat rapide       : Mode Chat par défaut
Analyses profondes: Mode Analyse pour tendances
Rapports officiels: Mode Rapport pour documents
Anticiper crises  : Mode Prédiction pour alertes
Gérer émergences  : Mode Alerte pour problèmes
```

**Astuces**
```
Cliquez sur les raccourcis rapides
Utilisez les modes selon vos besoins
L'IA apprend de vos données réelles
Plus vous l'utilisez, plus c'est utile
```

---

## 📞 Questions ?

### Où je lis la documentation ?
→ [README_AGENT_IA.md](README_AGENT_IA.md)

### Comment ça fonctionne exactement ?
→ [ARCHITECTURE_AGENT_IA.md](ARCHITECTURE_AGENT_IA.md)

### Tous les détails
→ [AGENT_IA_DOCUMENTATION.md](AGENT_IA_DOCUMENTATION.md)

### J'ai un problème
→ [CHECKLIST_AGENT_IA.md](CHECKLIST_AGENT_IA.md)

### Je veux tout voir
→ [INDEX_AGENT_IA.md](INDEX_AGENT_IA.md)

---

## 🎉 Vous êtes prêt !

**Démarrage** : 3 minutes  
**Formation** : 30 minutes  
**Production** : Demain  

À vous ! 🚀

---

**START.md** — Dernière mise à jour : 15/06/2026
