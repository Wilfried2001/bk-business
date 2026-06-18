<?php
// ============================================================
//  config/agent.php — Configuration de l'Agent IA
// ============================================================

return [
    // Clé API Anthropic Claude
    'api_key' => Config::get('ANTHROPIC_API_KEY', ''),

    // Modèle Claude
    'model' => 'claude-3-5-sonnet-20241022',

    // Nombre max de tokens par réponse
    'max_tokens' => 1024,
    // Délai d'attente maximal pour les requêtes IA (en secondes)
    'request_timeout' => 15,
    // Fournisseur d'IA : 'anthropic' (Claude) ou 'groq'
    'provider' => Config::get('AI_PROVIDER', 'anthropic'),

    // Paramètres Groq (si utilisé)
    'groq_api_key' => Config::get('GROQ_API_KEY', ''),
    'groq_api_url' => Config::get('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
    'groq_model' => Config::get('GROQ_MODEL', 'groq-1'),

    // Rôles et accès
    'roles' => [
        'AGENT' => [
            'data_access' => ['transactions', 'soldes'],
            'excludes' => ['commissions'],
        ],
        'SUPERVISEUR' => [
            'data_access' => ['transactions', 'soldes', 'alertes'],
            'excludes' => [],
        ],
        'COMPTABLE' => [
            'data_access' => ['transactions', 'soldes', 'alertes', 'commissions'],
            'excludes' => [],
        ],
        'DG' => [
            'data_access' => ['all'],
            'excludes' => [],
        ],
    ],

    // Modes disponibles
    'modes' => [
        'chat'       => 'Chat simple',
        'analyse'    => 'Analyse des données',
        'rapport'    => 'Génération de rapport',
        'prediction' => 'Prédiction',
        'alerte'     => 'Gestion des alertes',
        'guichet'    => 'Assistant guichet',
    ],

    // Seuils et limites
    'thresholds' => [
        'transaction_anomaly_multiplier' => 3, // 3x la moyenne = anomalie
        'alert_high_priority' => 10000,         // Montant critique
        'forecast_horizon_days' => 7,           // Prédictions sur 7 jours
    ],

    // Templates des réponses
    'response_templates' => [
        'chat' => 'Réponse très courte et simple, sans détails non sollicités.',
        'analyse' => 'Format: ✅ / ⚠️ / 💡',
        'rapport' => 'Rapport structuré complet',
        'prediction' => 'Prédictions avec actions',
        'alerte' => 'Format: Situation / Analyse / Actions',
    ],
];
?>