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
        <button id="ai-assistant-btn" class="ai-floating-btn" title="Ouvrir l'assistant IA" aria-label="Ouvrir l'assistant IA">
            <i data-lucide="bot"></i>
        </button>

        <!-- Modal Agent IA -->
        <div class="app-modal" id="aiAssistantModal" tabindex="-1" aria-hidden="true">
            <div class="app-modal-dialog ai-assistant-dialog">
                    <div class="app-modal-header">
                        <div>
                            <h5 class="flex items-center gap-2 font-semibold"><i data-lucide="bot"></i> Agent IA BK Business</h5>
                            <small>Assistant opérationnel</small>
                        </div>
                        <button type="button" class="app-close app-close-white" data-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>
                    <div class="app-modal-body ai-assistant-body">
                        <div id="ai-modal-chat" class="ai-modal-chat" aria-live="polite">
                            <div id="ai-welcome" class="ai-welcome">
                                <div class="service-avatar"><i data-lucide="bot-message-square"></i></div>
                                <div>
                                    <strong>Bienvenue !</strong>
                                    <span>Posez une question sur les stocks, transactions, alertes actives ou commissions autorisées.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons raccourcis rapides -->
                        <div id="ai-shortcuts" class="ai-shortcuts">
                            <button type="button" class="ai-shortcut"
                                data-question="Quel est l'état actuel des soldes?">
                                <i data-lucide="wallet"></i><span>Soldes</span>
                            </button>
                            <button type="button" class="ai-shortcut"
                                data-question="Combien de transactions avons-nous aujourd'hui?">
                                <i data-lucide="arrow-left-right"></i><span>Transactions</span>
                            </button>
                            <button type="button" class="ai-shortcut"
                                data-question="Quelles sont les alertes actives?">
                                <i data-lucide="alert-triangle"></i><span>Alertes</span>
                            </button>
                            <button type="button" class="ai-shortcut"
                                data-question="Donne-moi une analyse rapide de la situation">
                                <i data-lucide="trending-up"></i><span>Analyse</span>
                            </button>
                        </div>

                        <div class="ai-input-row">
                            <label class="sr-only" for="ai-modal-input">Question rapide</label>
                            <input type="text" id="ai-modal-input" class="app-field" placeholder="Votre question..."
                                autocomplete="off">
                            <button class="app-btn app-btn-primary" id="ai-modal-send" type="button">
                                <i data-lucide="send-horizontal"></i>
                                <span id="ai-modal-send-text">Envoyer</span>
                                <span id="ai-modal-send-spinner" class="ai-modal-spinner hidden" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
            </div>
        </div>

        <style>
.ai-floating-btn {
    position: fixed;
    right: 1.5rem;
    bottom: 1.5rem;
    z-index: 999;
    display: inline-flex;
    height: 3.5rem;
    width: 3.5rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: #020617;
    color: #fff;
    box-shadow: 0 20px 25px -5px rgba(15, 23, 42, .18), 0 8px 10px -6px rgba(15, 23, 42, .18);
    transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
}

.ai-floating-btn:hover {
    transform: translateY(-2px);
    background: #1e293b;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, .32);
}

.ai-floating-btn svg {
    height: 1.35rem;
    width: 1.35rem;
}

.ai-assistant-dialog {
    max-width: 42rem;
}

.ai-assistant-body {
    display: grid;
    gap: 1rem;
}

.ai-modal-chat {
    display: flex;
    height: min(24rem, 52vh);
    flex-direction: column;
    overflow-y: auto;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 1rem;
}

.ai-welcome {
    display: flex;
    gap: .75rem;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    padding: .875rem;
}

.ai-welcome strong,
.ai-welcome span {
    display: block;
}

.ai-welcome strong {
    color: #020617;
    font-size: .875rem;
}

.ai-welcome span {
    margin-top: .25rem;
    color: #64748b;
    font-size: .875rem;
}

.ai-shortcuts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .5rem;
}

.ai-shortcut {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    border-radius: .375rem;
    border: 1px solid #cbd5e1;
    background: #fff;
    padding: .5rem .75rem;
    color: #475569;
    font-size: .875rem;
    font-weight: 600;
    transition: background-color .15s ease, border-color .15s ease, color .15s ease;
}

.ai-shortcut:hover {
    border-color: #c7d2fe;
    background: #f8fafc;
    color: #3730a3;
}

.ai-input-row {
    display: grid;
    gap: .75rem;
}

@media (min-width: 640px) {
    .ai-input-row {
        grid-template-columns: minmax(0, 1fr) auto;
    }
}

.ai-modal-message {
    margin-bottom: .75rem;
    max-width: min(34rem, 88%);
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    padding: .75rem .875rem;
    color: #334155;
    font-size: .875rem;
    line-height: 1.55;
    animation: aiModalMessageIn .2s ease-out;
}

.ai-modal-message.user {
    align-self: flex-end;
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #312e81;
}

.ai-modal-message.ai {
    align-self: flex-start;
    border-left: 4px solid #4f46e5;
}

.ai-modal-message.error {
    align-self: flex-start;
    border-color: #fecaca;
    border-left: 4px solid #d32f2f;
    background: #fef2f2;
    color: #7f1d1d;
}

.ai-modal-spinner {
    height: 1rem;
    width: 1rem;
    border-radius: 9999px;
    border: 2px solid rgba(255, 255, 255, .45);
    border-top-color: #fff;
    animation: aiModalSpin .75s linear infinite;
}

@keyframes aiModalSpin {
    to { transform: rotate(360deg); }
}

@keyframes aiModalMessageIn {
    from {
        opacity: 0;
        transform: translateY(.25rem);
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
    const sendText = document.getElementById('ai-modal-send-text');
    const sendSpinner = document.getElementById('ai-modal-send-spinner');
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

    function setLoading(isLoading) {
        sendButton.disabled = isLoading;
        sendText.classList.toggle('hidden', isLoading);
        sendSpinner.classList.toggle('hidden', !isLoading);
    }

    function sendMessage() {
        const question = inputField.value.trim();
        if (!question) return;

        removeWelcome();
        addMessage(question, 'user');
        inputField.value = '';
        setLoading(true);

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
                if (error.message !== 'Non-JSON response') {
                    addMessage('Erreur réseau : ' + error.message, 'error');
                }
            })
            .finally(() => {
                setLoading(false);
                inputField.focus();
            });
    }

    function addMessage(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'ai-modal-message ' + type;

        const escaped = document.createElement('div');
        escaped.textContent = text;
        msgDiv.innerHTML = escaped.innerHTML.replace(/\n/g, '<br>');

        chatDiv.appendChild(msgDiv);
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }

    sendButton.addEventListener('click', sendMessage);
    inputField.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
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
