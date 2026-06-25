<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }
    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    .login-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .login-header p {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .login-form {
        padding: 2.5rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .app-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.6rem;
        display: block;
    }
    .app-field {
        border: 2px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 0.9rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .app-field:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    .login-button {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }
    .alert {
        border-radius: 0.5rem;
        border: none;
        margin-bottom: 1.5rem;
    }
    .icon-input {
        position: relative;
    }
    .icon-input i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }
    .icon-input .app-field {
        padding-left: 2.5rem;
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h1><i data-lucide="briefcase"></i> <?= e(APP_NAME) ?></h1>
        <p>Gestion d'entreprise intégrée</p>
    </div>
    <div class="login-form">
        <?php if ($error = Session::getFlash('error')): ?>
            <div class="app-alert app-alert-danger" role="alert">
                <i data-lucide="alert-circle"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success = Session::getFlash('success')): ?>
            <div class="app-alert app-alert-success" role="alert">
                <i data-lucide="check-circle"></i> <?= e($success) ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= url('auth/login') ?>" method="post" id="loginForm">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label for="email" class="app-label">
                    <i data-lucide="mail"></i> Adresse email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="border rounded-md px-3 py-2 text-sm w-full" 
                    placeholder="votre@email.com"
                    required 
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password" class="app-label">
                    <i data-lucide="lock"></i> Mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="border rounded-md px-3 py-2 text-sm w-full" 
                    placeholder="Votre mot de passe"
                    required
                >
            </div>
            
            <button type="submit" class="login-button">
                <i data-lucide="log-in"></i> Se connecter
            </button>
        </form>
        
        <hr class="my-4" style="color: #e0e0e0;">
        <p class="text-center text-gray-500 small">
            <i data-lucide="shield"></i> Connexion sécurisée
        </p>
    </div>
</div>