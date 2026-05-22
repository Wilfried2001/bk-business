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
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.6rem;
        display: block;
    }
    .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 0.9rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    .btn-login {
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
    .btn-login:hover {
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
    .icon-input .form-control {
        padding-left: 2.5rem;
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h1><i class="bi bi-briefcase-fill"></i> <?= e(APP_NAME) ?></h1>
        <p>Gestion d'entreprise intégrée</p>
    </div>
    <div class="login-form">
        <?php if ($error = Session::getFlash('error')): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success = Session::getFlash('success')): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle"></i> <?= e($success) ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= url('auth/login') ?>" method="post" id="loginForm">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope"></i> Adresse email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="votre@email.com"
                    required 
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Votre mot de passe"
                    required
                >
            </div>
            
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </form>
        
        <hr class="my-4" style="color: #e0e0e0;">
        <p class="text-center text-muted small">
            <i class="bi bi-shield-lock"></i> Connexion sécurisée
        </p>
    </div>
</div>