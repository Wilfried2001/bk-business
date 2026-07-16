<?php
// ============================================================
//  app/views/agent/chat.php — Interface de chat IA
// ============================================================
?>

<div class="business-dashboard agent-workspace">
    <section class="dashboard-head">
        <div>
            <p class="dashboard-eyebrow">Assistant opérationnel</p>
            <h1>Agent IA BK Business</h1>
            <p>Interrogez les transactions, les soldes, les alertes et les commissions selon vos accès.</p>
        </div>
        <div class="dashboard-actions">
            <span class="date-pill"><i data-lucide="bot"></i> Mode <strong id="current-mode-label">Chat</strong></span>
        </div>
    </section>

    <section class="agent-layout">
        <article class="app-card agent-chat-card">
            <div class="app-card-header">
                <span><i data-lucide="messages-square"></i> Conversation</span>
                <small class="text-slate-500">Données BK Business en temps réel</small>
            </div>
            <div class="app-card-body agent-chat-body">
                <div class="chat-container">
                    <div id="welcome-container" class="agent-welcome">
                        <div class="service-avatar"><i data-lucide="bot-message-square"></i></div>
                        <div>
                            <strong>Bienvenue !</strong>
                            <span>Posez une question sur les stocks, transactions, alertes actives ou commissions autorisées.</span>
                        </div>
                    </div>
                    <div id="chat-messages" class="chat-messages" aria-live="polite"></div>
                </div>

                <div class="agent-input-row">
                    <label class="sr-only" for="question-input">Question</label>
                    <input type="text" id="question-input" class="app-field"
                        placeholder="Posez votre question... ex: Combien on a fait aujourd'hui ?" autocomplete="off">
                    <button id="send-button" class="app-btn app-btn-primary" type="button">
                        <i data-lucide="send-horizontal"></i>
                        <span id="send-text">Envoyer</span>
                        <span id="send-spinner" class="agent-spinner hidden" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </article>

        <aside class="agent-side-panel">
            <div class="app-card">
                <div class="app-card-header">
                    <span><i data-lucide="sliders-horizontal"></i> Mode</span>
                </div>
                <div class="app-card-body">
                    <div class="agent-mode-grid" role="group" aria-label="Modes de l'agent IA">
                        <button type="button" class="agent-mode-option mode-option" data-mode="chat">
                            <i data-lucide="message-circle"></i><span>Chat</span>
                        </button>
                        <button type="button" class="agent-mode-option mode-option" data-mode="analyse">
                            <i data-lucide="chart-no-axes-column"></i><span>Analyser</span>
                        </button>
                        <button type="button" class="agent-mode-option mode-option" data-mode="rapport">
                            <i data-lucide="file-text"></i><span>Rapport</span>
                        </button>
                        <button type="button" class="agent-mode-option mode-option" data-mode="prediction">
                            <i data-lucide="sparkles"></i><span>Prédire</span>
                        </button>
                        <button type="button" class="agent-mode-option mode-option" data-mode="alerte">
                            <i data-lucide="alert-triangle"></i><span>Alerte</span>
                        </button>
                        <button type="button" class="agent-mode-option mode-option" data-mode="guichet">
                            <i data-lucide="receipt-text"></i><span>Guichet</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="quick-prompts-container" class="app-card">
                <div class="app-card-header">
                    <span><i data-lucide="zap"></i> Raccourcis</span>
                </div>
                <div class="app-card-body">
                    <div class="quick-prompts" role="group" aria-label="Questions rapides">
                        <button type="button" class="quick-option" data-prompt="Quel est l'état des stocks ?">
                            <i data-lucide="package"></i><span>Stocks</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Combien de transactions aujourd'hui ?">
                            <i data-lucide="arrow-left-right"></i><span>Transactions</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Quelles sont les alertes actives ?">
                            <i data-lucide="alert-triangle"></i><span>Alertes</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Combien de commissions ce mois ?">
                            <i data-lucide="percent"></i><span>Commissions</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Fais une analyse des 30 derniers jours">
                            <i data-lucide="chart-line"></i><span>Analyse 30j</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Propose une action pour l'agent au guichet">
                            <i data-lucide="receipt-text"></i><span>Guichet</span>
                        </button>
                        <button type="button" class="quick-option" data-prompt="Quel service est en hausse ce mois ?">
                            <i data-lucide="trending-up"></i><span>Tendances</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </section>
</div>

<style>
.agent-workspace {
    max-width: 80rem;
}

.agent-layout {
    display: grid;
    gap: 1rem;
}

@media (min-width: 1280px) {
    .agent-layout {
        grid-template-columns: minmax(0, 1fr) 20rem;
        align-items: start;
    }
}

.agent-chat-card {
    min-width: 0;
}

.agent-chat-body {
    display: flex;
    min-height: 38rem;
    flex-direction: column;
    gap: 1rem;
}

.chat-container {
    display: flex;
    min-height: 0;
    flex: 1;
    flex-direction: column;
    overflow: hidden;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}

.agent-welcome {
    display: flex;
    gap: .75rem;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
    padding: 1rem;
}

.agent-welcome strong,
.agent-welcome span {
    display: block;
}

.agent-welcome strong {
    color: #020617;
    font-size: .875rem;
}

.agent-welcome span {
    margin-top: .25rem;
    color: #64748b;
    font-size: .875rem;
}

.chat-messages {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.chat-message {
    margin-bottom: .75rem;
    max-width: min(42rem, 88%);
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    padding: .75rem .875rem;
    color: #334155;
    font-size: .875rem;
    line-height: 1.55;
    animation: agentMessageIn .2s ease-out;
}

.chat-message.user {
    align-self: flex-end;
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #312e81;
}

.chat-message.ai {
    align-self: flex-start;
    border-left: 4px solid #4f46e5;
}

.chat-message.error {
    align-self: flex-start;
    border-color: #fecaca;
    border-left: 4px solid #d32f2f;
    background: #fef2f2;
    color: #7f1d1d;
}

.chat-message code {
    border-radius: .25rem;
    background: rgba(15, 23, 42, .08);
    padding: .125rem .25rem;
    font-size: .9em;
}

.agent-input-row {
    display: grid;
    gap: .75rem;
}

@media (min-width: 640px) {
    .agent-input-row {
        grid-template-columns: minmax(0, 1fr) auto;
    }
}

.agent-side-panel {
    display: grid;
    gap: 1rem;
}

.agent-mode-grid,
.quick-prompts {
    display: grid;
    gap: .5rem;
}

.agent-mode-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.agent-mode-option,
.quick-option {
    display: inline-flex;
    align-items: center;
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

.agent-mode-option {
    justify-content: center;
}

.quick-option {
    justify-content: flex-start;
}

.agent-mode-option:hover,
.quick-option:hover {
    border-color: #c7d2fe;
    background: #f8fafc;
    color: #3730a3;
}

.agent-mode-option.active {
    border-color: #4f46e5;
    background: #eef2ff;
    color: #3730a3;
}

.agent-spinner {
    height: 1rem;
    width: 1rem;
    border-radius: 9999px;
    border: 2px solid rgba(255, 255, 255, .45);
    border-top-color: #fff;
    animation: agentSpin .75s linear infinite;
}

@keyframes agentSpin {
    to { transform: rotate(360deg); }
}

@keyframes agentMessageIn {
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

<script nonce="<?= e(cspNonce()) ?>">
document.addEventListener('DOMContentLoaded', function() {
    const chatMessagesDiv = document.getElementById('chat-messages');
    const welcomeContainer = document.getElementById('welcome-container');
    const questionInput = document.getElementById('question-input');
    const sendButton = document.getElementById('send-button');
    const sendText = document.getElementById('send-text');
    const sendSpinner = document.getElementById('send-spinner');
    const modeBtns = document.querySelectorAll('.mode-option');
    const quickBtns = document.querySelectorAll('.quick-option');
    const modeLabel = document.getElementById('current-mode-label');
    const quickPromptsContainer = document.getElementById('quick-prompts-container');

    let currentMode = 'chat';
    let firstMessageSent = false;

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

    if (modeBtns[0]) {
        modeBtns[0].classList.add('active');
    }

    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            questionInput.value = this.dataset.prompt;
            questionInput.focus();
        });
    });

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

    function setLoading(isLoading) {
        sendButton.disabled = isLoading;
        sendText.classList.toggle('hidden', isLoading);
        sendSpinner.classList.toggle('hidden', !isLoading);
    }

    function sendMessage() {
        const question = questionInput.value.trim();
        if (!question) return;

        removeWelcome();
        addMessage(question, 'user');
        questionInput.value = '';
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
                setLoading(false);
                questionInput.focus();
            });
    }

    function addMessage(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message ' + type;

        if (type === 'user' || type === 'error' || type === 'ai') {
            const escaped = document.createElement('div');
            escaped.textContent = text;
            msgDiv.innerHTML = escaped.innerHTML.replace(/\n/g, '<br>');
        }

        chatMessagesDiv.appendChild(msgDiv);
        chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    }

    sendButton.addEventListener('click', sendMessage);
    questionInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});
</script>
