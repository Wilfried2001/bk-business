        </main>
        </div>
        <footer class="app-footer">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <small>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> - Tous droits réservés.</small>
                    </div>
                    <div class="sm:text-right">
                        <small>v<?= e(APP_VERSION) ?> | <a href="#" class="text-white/70 hover:text-white">Aide</a></small>
                    </div>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <!-- Bouton flottant Agent IA -->
        <button id="ai-assistant-btn" class="app-btn app-btn-primary rounded-full" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 999;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
" title="Ouvrir l'assistant IA"><i data-lucide="bot"></i></button>

        <!-- Modal Agent IA -->
        <div class="app-modal" id="aiAssistantModal" tabindex="-1" aria-hidden="true">
            <div class="app-modal-dialog">
                    <div class="app-modal-header">
                        <h5 class="flex items-center gap-2 font-semibold"><i data-lucide="bot"></i> Assistant IA BK_Business</h5>
                        <button type="button" class="app-close app-close-white" data-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>
                    <div class="app-modal-body">
                        <div id="ai-modal-chat"
                            class="mb-4 h-96 overflow-y-auto rounded-md bg-slate-50 p-4">
                            <div id="ai-welcome" class="app-alert app-alert-info alert-permanent">
                                <strong class="flex items-center gap-2"><i data-lucide="bot-message-square"></i> Bienvenue !</strong>
                                <p class="mb-2">Je suis votre assistant IA. Posez-moi vos questions sur :</p>
                                <ul class="list-disc pl-5">
                                    <li>Les stocks et soldes</li>
                                    <li>Les transactions</li>
                                    <li>Les alertes actives</li>
                                    <li>Les commissions (si autorisé)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Boutons raccourcis rapides -->
                        <div id="ai-shortcuts"
                            style="margin-bottom: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            <button type="button" class="app-btn app-btn-sm app-btn-outline-primary ai-shortcut"
                                data-question="Quel est l'état actuel des soldes?">
                                <i data-lucide="wallet"></i> Soldes
                            </button>
                            <button type="button" class="app-btn app-btn-sm app-btn-outline-primary ai-shortcut"
                                data-question="Combien de transactions avons-nous aujourd'hui?">
                                <i data-lucide="arrow-left-right"></i> Transactions
                            </button>
                            <button type="button" class="app-btn app-btn-sm border border-warning/40 bg-white text-warning hover:bg-orange-50 ai-shortcut"
                                data-question="Quelles sont les alertes actives?">
                                <i data-lucide="alert-triangle"></i> Alertes
                            </button>
                            <button type="button" class="app-btn app-btn-sm border border-success/40 bg-white text-success hover:bg-green-50 ai-shortcut"
                                data-question="Donne-moi une analyse rapide de la situation">
                                <i data-lucide="trending-up"></i> Analyse
                            </button>
                        </div>

                        <div class="app-input-group">
                            <input type="text" id="ai-modal-input" class="app-field" placeholder="Votre question..."
                                autocomplete="off">
                            <button class="app-btn app-btn-primary" id="ai-modal-send" type="button">Envoyer</button>
                        </div>
                    </div>
            </div>
        </div>

        <style>
#ai-assistant-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.ai-modal-message {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 0.25rem;
    animation: slideIn 0.3s ease-in-out;
}

.ai-modal-message.user {
    background-color: #e7f3ff;
    border-left: 4px solid #007bff;
    text-align: right;
}

.ai-modal-message.ai {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-left: 4px solid #6c757d;
}

.ai-modal-message.error {
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
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
        </style>

        <script>
document.addEventListener('DOMContentLoaded', function() {
    const aiBtn = document.getElementById('ai-assistant-btn');
    const aiModal = document.getElementById('aiAssistantModal');
    const chatDiv = document.getElementById('ai-modal-chat');
    const inputField = document.getElementById('ai-modal-input');
    const sendButton = document.getElementById('ai-modal-send');
    const welcomeDiv = document.getElementById('ai-welcome');
    const shortcutsDiv = document.getElementById('ai-shortcuts');
    let firstMessageSent = false;

    aiBtn.addEventListener('click', function() {
        openModal(aiModal);
        inputField.focus();
    });

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    aiModal.querySelectorAll('[data-dismiss="modal"]').forEach((button) => {
        button.addEventListener('click', function() {
            closeModal(aiModal);
        });
    });

    function removeWelcome() {
        if (!firstMessageSent && welcomeDiv) {
            welcomeDiv.style.display = 'none';
            shortcutsDiv.style.display = 'none';
            firstMessageSent = true;
        }
    }

    function sendMessage() {
        const question = inputField.value.trim();
        if (!question) return;

        removeWelcome();
        addMessage(question, 'user');
        inputField.value = '';
        sendButton.disabled = true;

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
                    mode: 'chat'
                })
            })
            .then(response => {
                return response.text().then(text => {
                    const ct = response.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        console.error('Non-JSON response from /api/agent/ask:', text);
                        const loginUrl = '<?= url("auth/login") ?>';
                        addMessage(`Erreur : vous semblez déconnecté. <a href="${loginUrl}">Se connecter</a>`, 'error');
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
                inputField.focus();
            });
    }

    function addMessage(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'ai-modal-message ' + type;

        // Échapper le texte pour éviter les injections XSS
        if (type === 'user') {
            msgDiv.textContent = text;
        } else if (type === 'error') {
            // Allow minimal HTML in error messages (e.g., login link)
            msgDiv.innerHTML = text;
        } else if (type === 'ai') {
            // Pour les réponses IA, transformer les retours à la ligne et maintenir la lisibilité
            const escaped = document.createElement('div');
            escaped.textContent = text;
            msgDiv.innerHTML = escaped.innerHTML.replace(/\n/g, '<br>');
        }

        chatDiv.appendChild(msgDiv);
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }

    sendButton.addEventListener('click', sendMessage);
    inputField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Événements sur les boutons raccourcis
    document.querySelectorAll('.ai-shortcut').forEach(btn => {
        btn.addEventListener('click', function() {
            inputField.value = this.getAttribute('data-question');
            inputField.focus();
            sendMessage();
        });
    });
});
        </script>

        <!-- Modal global pour afficher les détails d'une ligne (mobile) -->
        <div class="app-modal" id="rowDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="app-modal-dialog app-modal-dialog-sm">
                    <div class="app-modal-header">
                        <h5 class="flex items-center gap-2 font-semibold"><i data-lucide="list"></i> Détails</h5>
                        <button type="button" class="app-close app-close-white" data-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="app-modal-body">
                        <div id="rowDetailsContent"></div>
                    </div>
            </div>
        </div>

        <script src="<?= url('js/script.js') ?>"></script>
        </body>

        </html>
