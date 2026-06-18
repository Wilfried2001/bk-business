        </main>
        </div>
        </div>
        <footer class="footer mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <small>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> - Tous droits réservés.</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>v<?= e(APP_VERSION) ?> | <a href="#" class="text-white-50">Aide</a></small>
                    </div>
                </div>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <!-- Bouton flottant Agent IA -->
        <button id="ai-assistant-btn" class="btn btn-primary rounded-circle" style="
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
" title="Ouvrir l'assistant IA">🤖</button>

        <!-- Modal Agent IA -->
        <div class="modal fade" id="aiAssistantModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">🤖 Assistant IA BK_Business</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="ai-modal-chat"
                            style="height: 400px; overflow-y: auto; background-color: #f8f9fa; border-radius: 0.25rem; padding: 1rem; margin-bottom: 1rem;">
                            <div id="ai-welcome" class="alert alert-info alert-permanent mb-0">
                                <strong>🤖 Bienvenue !</strong>
                                <p class="mb-2">Je suis votre assistant IA. Posez-moi vos questions sur :</p>
                                <ul class="mb-0 ps-3">
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
                            <button type="button" class="btn btn-sm btn-outline-primary ai-shortcut"
                                data-question="Quel est l'état actuel des soldes?">
                                <i class="bi bi-wallet2"></i> Soldes
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary ai-shortcut"
                                data-question="Combien de transactions avons-nous aujourd'hui?">
                                <i class="bi bi-arrow-left-right"></i> Transactions
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning ai-shortcut"
                                data-question="Quelles sont les alertes actives?">
                                <i class="bi bi-exclamation-triangle"></i> Alertes
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success ai-shortcut"
                                data-question="Donne-moi une analyse rapide de la situation">
                                <i class="bi bi-graph-up"></i> Analyse
                            </button>
                        </div>

                        <div class="input-group">
                            <input type="text" id="ai-modal-input" class="form-control" placeholder="Votre question..."
                                autocomplete="off">
                            <button class="btn btn-primary" id="ai-modal-send" type="button">Envoyer</button>
                        </div>
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
    const aiModal = new bootstrap.Modal(document.getElementById('aiAssistantModal'));
    const chatDiv = document.getElementById('ai-modal-chat');
    const inputField = document.getElementById('ai-modal-input');
    const sendBtn = document.getElementById('ai-modal-send');
    const welcomeDiv = document.getElementById('ai-welcome');
    const shortcutsDiv = document.getElementById('ai-shortcuts');
    let firstMessageSent = false;

    aiBtn.addEventListener('click', function() {
        aiModal.show();
        inputField.focus();
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
        sendBtn.disabled = true;

        fetch('<?= url("api/agent/ask") ?>', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question: question,
                    mode: 'chat'
                })
            })
            .then(response => {
                const ct = response.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response from /api/agent/ask:', text);
                        const loginUrl = '<?= url("auth/login") ?>';
                        addMessage(`❌ Erreur : vous semblez déconnecté. <a href="${loginUrl}">Se connecter</a>`, 'error');
                        // Do not throw to avoid duplicate generic network error
                        return Promise.reject(new Error('Non-JSON response'));
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    addMessage(data.reponse, 'ai');
                } else {
                    addMessage('❌ Erreur : ' + (data.error || 'Erreur inconnue'), 'error');
                }
            })
            .catch(error => {
                addMessage('❌ Erreur réseau : ' + error.message, 'error');
            })
            .finally(() => {
                sendBtn.disabled = false;
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

    sendBtn.addEventListener('click', sendMessage);
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
        <div class="modal fade" id="rowDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div id="rowDetailsContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="<?= url('js/script.js') ?>"></script>
        </body>

        </html>