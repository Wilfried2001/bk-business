<?php
// ============================================================
//  app/controllers/AuthController.php — Fichier commenté
// ============================================================

// Classe AuthController : implémente la logique métier pour cette partie de l’application
class AuthController extends Controller {

// Méthode loginForm : gère loginForm. 
    public function loginForm(): void {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login', ['pageTitle' => 'Connexion — ' . APP_NAME]);
    }

// Méthode login : gère login. 
    public function login(): void {
        $this->verifyCsrf();

        if ($this->isLoginLocked()) {
            Session::flash('error', 'Trop de tentatives de connexion. Réessayez dans quelques minutes.');
            $this->redirect('auth/login');
        }

        $email    = $this->post('email', '');
        $password = $this->post('password', '');

        $errors = $this->validate([
            'email'    => $email,
            'password' => $password,
        ], [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            $this->abortValidation($errors, 'auth/login');
        }

        $userModel = new Utilisateur();
        $user      = $userModel->authenticate($email, $password);

        if ($user) {
            $this->clearLoginAttempts();
            Auth::login($user);
            Session::flash('success', 'Bienvenue, ' . $user['nom'] . ' !');
            $this->redirect('dashboard');
        }

        $this->incrementLoginAttempts();
        Session::flash('error', 'Email ou mot de passe incorrect.');
        $this->redirect('auth/login');
    }

// Méthode logout : gère logout. 
    public function logout(): void {
        Auth::logout();
        $this->redirect('auth/login');
    }

    private function isLoginLocked(): bool {
        return (int) Session::get('login_lock_until', 0) > time();
    }

    private function incrementLoginAttempts(): void {
        $attempts = (int) Session::get('login_attempts', 0) + 1;
        Session::set('login_attempts', $attempts);
        if ($attempts >= 5) {
            Session::set('login_lock_until', time() + 300);
        }
    }

    private function clearLoginAttempts(): void {
        Session::remove('login_attempts');
        Session::remove('login_lock_until');
    }
}
