<?php
// ============================================================
//  app/views/agent/chat.php — Interface de chat IA
// ============================================================
?>

<div class="w-full my-8">
    <div class="flex flex-wrap -mx-3">
        <div class="lg:w-2/3 lg:mx-auto">
            <!-- En-tête -->
            <div class="app-card mb-4 border-primary">
                <div class="app-card-header bg-primary text-white">
                    <h3 class="mb-0 flex items-center gap-2"><i data-lucide="bot"></i> Assistant IA BK_Business</h3>
                    <small>Analysez vos données en temps réel</small>
                </div>
            </div>

            <!-- Zone de conversation -->
            <div class="chat-container app-card" style="height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                <!-- Message d'accueil (sera caché au premier message) -->
                <div id="welcome-container" class="p-3" style="border-bottom: 1px solid #dee2e6;">
                    <div class="app-alert app-alert-info alert-permanent mb-0" role="alert">
                        <strong class="flex items-center gap-2"><i data-lucide="bot-message-square"></i> Bienvenue !</strong>
                        <p class="mb-2">Je suis votre assistant IA. Posez-moi vos questions sur :</p>
                        <ul class="mb-0">
                            <li>Les stocks et soldes</li>
                            <li>Les transactions</li>
                            <li>Les alertes actives</li>
                            <li>Les commissions (si autorisé)</li>
                        </ul>
                    </div>
                </div>
                <!-- Messages du chat -->
                <div id="chat-messages" class="p-3">
                </div>
            </div>

            <!-- Contrôles de mode -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mt-3 w-full" role="group">
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="chat"><i data-lucide="message-circle"></i>
                    Chat</button>
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="analyse"><i data-lucide="chart-no-axes-column"></i>
                    Analyser</button>
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="rapport"><i data-lucide="file-text"></i>
                    Rapport</button>
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="prediction"><i data-lucide="sparkles"></i>
                    Prédire</button>
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="alerte"><i data-lucide="alert-triangle"></i>
                    Alerte</button>
                <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 mode-option" data-mode="guichet"><i data-lucide="receipt-text"></i>
                    Guichet</button>
            </div>
            <div class="mt-2">
                <small class="text-gray-500">Mode actif :</small>
                <span id="current-mode-label" class="inline-flex items-center px-2 py-1 app-badge app-badge-secondary">Chat</span>
            </div>

            <!-- Zone de saisie -->
            <div class="app-input-group mt-3">
                <input type="text" id="question-input" class="border rounded-md px-3 py-2 text-sm w-full"
                    placeholder="Posez votre question... (ex: Combien on a fait aujourd'hui ?)" autocomplete="off">
                <button id="send-button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white" type="button">
                    <i data-lucide="send-horizontal"></i>
                    <span id="send-text">Envoyer</span>
                    <span id="send-spinner" class="spinner-border spinner-border-sm hidden ml-2" role="status"
                        aria-hidden="true"></span>
                </button>
            </div>

            <!-- Quick prompts -->
            <div id="quick-prompts-container" class="mt-3">
                <small class="text-gray-500">Raccourcis :</small>
                <div class="flex flex-wrap gap-2 w-full mt-2" role="group">
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Quel est l'état des stocks ?"><i data-lucide="package"></i> Stocks</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Combien de transactions aujourd'hui ?"><i data-lucide="arrow-left-right"></i> Transactions</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Quelles sont les alertes actives ?"><i data-lucide="alert-triangle"></i> Alertes</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Combien de commissions ce mois ?"><i data-lucide="percent"></i> Commissions</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Fais une analyse des 30 derniers jours"><i data-lucide="chart-line"></i> Analyse 30j</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Propose une action pour l'agent au guichet"><i data-lucide="receipt-text"></i> Guichet</button>
                    <button type="button" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 quick-option"
                        data-prompt="Quel service est en hausse ce mois ?"><i data-lucide="trending-up"></i> Tendances</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.chat-message {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 0.25rem;
    animation: slideIn 0.3s ease-in-out;
}

.chat-message.user {
    background-color: #e7f3ff;
    border-left: 4px solid #007bff;
    text-align: right;
}

.chat-message.ai {
    background-color: #f0f0f0;
    border-left: 4px solid #6c757d;
    text-align: left;
}

.chat-message.error {
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
}

.chat-message code {
    background-color: rgba(0, 0, 0, 0.1);
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.9em;
}

.mode-option.active {
    background-color: #007bff;
    color: white;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#send-button:disabled {
    opacity: 0.6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessagesDiv = document.getElementById('chat-messages');
    const welcomeContainer = document.getElementById('welcome-container');
    const questionInput = document.getElementById('question-input');
    const sendButton = document.getElementById('send-button');
    const modeBtns = document.querySelectorAll('.mode-option');
    const quickBtns = document.querySelectorAll('.quick-option');

    let currentMode = 'chat';
    let firstMessageSent = false;

    const modeLabel = document.getElementById('current-mode-label');
    const quickPromptsContainer = document.getElementById('quick-prompts-container');

    // Gestion du mode
    modeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentMode = this.dataset.mode;
            if (modeLabel) {
                modeLabel.textContent = this.textContent.trim();
            }
        });
    });

    // Premier bouton actif par défaut
    modeBtns[0].classList.add('active');
    if (modeLabel) {
        modeLabel.textContent = modeBtns[0].textContent.trim();
    }

    // Quick prompts
    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            questionInput.value = this.dataset.prompt;
            questionInput.focus();
        });
    });

    // Supprimer le message d'accueil au premier message
    function removeWelcome() {
        if (!firstMessageSent) {
            if (welcomeContainer) {
                welcomeContainer.style.display = 'none';
            }
            if (quickPromptsContainer) {
                quickPromptsContainer.style.display = 'none';
            }
            firstMessageSent = true;
        }
    }

    // Envoi du message
    function sendMessage() {
        const question = questionInput.value.trim();
        if (!question) return;

        removeWelcome();
        // Afficher le message de l'utilisateur
        addMessage(question, 'user');
        questionInput.value = '';

        // Désactiver le bouton
        sendButton.disabled = true;
        document.getElementById('send-text').classList.add('d-none');
        document.getElementById('send-spinner').classList.remove('d-none');

        // Appeler l'API
        fetch('/api/agent/ask', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    question: question,
                    mode: currentMode
                })
            })
            .then(response => {
                return response.text().then(text => {
                    const ct = response.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        console.error('Non-JSON response from /api/agent/ask:', text);
                        const loginUrl = '<?= url("auth/login") ?>';
                        addMessage(
                            `Erreur : vous semblez déconnecté. <a href="${loginUrl}">Se connecter</a>`,
                            'error');
                        return Promise.reject(new Error('Non-JSON response'));
                    }
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        console.error('Invalid JSON response from /api/agent/ask:', text);
                        return Promise.reject(new Error('Réponse JSON invalide du serveur'));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    addMessage(data.reponse, 'ai');
                } else {
                    addMessage('Erreur : ' + (data.error || 'Erreur inconnue'), 'error');
                }
            })
            .catch(error => {
                addMessage('Erreur réseau : ' + error.message, 'error');
            })
            .finally(() => {
                sendButton.disabled = false;
                document.getElementById('send-text').classList.remove('d-none');
                document.getElementById('send-spinner').classList.add('d-none');
                questionInput.focus();
            });
    }

    // Ajouter un message au chat avec sécurité XSS
    function addMessage(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message ' + type;

        // Échapper le texte pour éviter les injections XSS
        if (type === 'user') {
            msgDiv.textContent = text;
        } else if (type === 'error') {
            // Allow minimal HTML in error messages (e.g., login link)
            msgDiv.innerHTML = text;
        } else if (type === 'ai') {
            // Pour les réponses IA, transformer les retours à la ligne
            const escaped = document.createElement('div');
            escaped.textContent = text;
            msgDiv.innerHTML = escaped.innerHTML.replace(/\n/g, '<br>');
        }

        chatMessagesDiv.appendChild(msgDiv);

        // Scroll vers le bas
        chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    }

    // Écouteurs
    sendButton.addEventListener('click', sendMessage);
    questionInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});
</script>
