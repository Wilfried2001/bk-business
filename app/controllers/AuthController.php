<?php
class AuthController extends Controller {

    public function loginForm(): void {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login', ['pageTitle' => 'Connexion — ' . APP_NAME]);
    }

    public function login(): void {
        $this->verifyCsrf();
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
            Auth::login($user);
            Session::flash('success', 'Bienvenue, ' . $user['nom'] . ' !');
            $this->redirect('dashboard');
        }

        Session::flash('error', 'Email ou mot de passe incorrect.');
        $this->redirect('auth/login');
    }

    public function logout(): void {
        Auth::logout();
        $this->redirect('auth/login');
    }
}
