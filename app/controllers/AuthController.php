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

        $email     = $this->post('email', '');
        $password  = $this->post('password', '');
        $ipAddress = $this->getClientIp();
        $userAgent = $this->getUserAgent();

        require_once APP_PATH . '/models/LoginAttempt.php';
        $attemptModel = new LoginAttempt();

        if ($this->isLoginLocked($email, $ipAddress, $attemptModel)) {
            Session::flash('error', 'Trop de tentatives de connexion. Réessayez dans quelques minutes.');
            $this->redirect('auth/login');
        }

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
            $this->clearLoginAttempts($email, $ipAddress, $attemptModel);
            Auth::login($user);
            Session::flash('success', 'Bienvenue, ' . $user['nom'] . ' !');
            $this->redirect('dashboard');
        }

        $this->incrementLoginAttempts($email, $ipAddress, $userAgent, $attemptModel);
        Session::flash('error', 'Email ou mot de passe incorrect.');
        $this->redirect('auth/login');
    }

// Méthode logout : gère logout. 
    public function logout(): void {
        Auth::logout();
        $this->redirect('auth/login');
    }

    private function getClientIp(): string {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function getUserAgent(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }

    private function isLoginLocked(string $email, string $ipAddress, LoginAttempt $attemptModel): bool {
        $lock = $attemptModel->getActiveLock($email, $ipAddress);
        return !empty($lock['lock_until']) && strtotime($lock['lock_until']) > time();
    }

    private function incrementLoginAttempts(string $email, string $ipAddress, string $userAgent, LoginAttempt $attemptModel): void {
        $failedCount = $attemptModel->countRecentFailedAttempts($email, $ipAddress, 15);
        $lockDuration = 300;
        if ($failedCount >= 5) {
            $lockDuration = min(1800, 60 * pow(2, $failedCount - 4));
        }
        $lockUntil = $failedCount + 1 >= 5
            ? date('Y-m-d H:i:s', time() + $lockDuration)
            : null;

        $attemptModel->record(
            $email,
            $ipAddress,
            $userAgent,
            false,
            $lockUntil,
            'Echec de connexion'
        );
    }

    private function clearLoginAttempts(string $email, string $ipAddress, LoginAttempt $attemptModel): void {
        $attemptModel->record(
            $email,
            $ipAddress,
            $this->getUserAgent(),
            true,
            null,
            'Connexion réussie'
        );
        $attemptModel->clearAttempts($email, $ipAddress);
    }
}
