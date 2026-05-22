<?php
class UtilisateurController extends Controller {

    public function index(): void {
        Auth::requireRole(['DG']);
        require_once APP_PATH . '/models/Utilisateur.php';
        $userModel = new Utilisateur();
        $this->render('dashboard/index', [
            'utilisateurs' => $userModel->all('nom'),
        ], 'Gestion des utilisateurs');
    }

    public function create(): void {
        Auth::requireRole(['DG']);
        $this->render('dashboard/index', [
            'userRoles' => ['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'],
        ], 'Ajouter un utilisateur');
    }

    public function store(): void {
        Auth::requireRole(['DG']);
        $this->verifyCsrf();

        $data = [
            'nom'          => $this->post('nom'),
            'email'        => $this->post('email'),
            'mot_de_passe' => $this->post('mot_de_passe'),
            'role'         => $this->post('role'),
        ];

        $errors = $this->validate($data, [
            'nom'          => 'required|max_length:100',
            'email'        => 'required|email',
            'mot_de_passe' => 'required|min_length:8',
            'role'         => 'required|in:AGENT,SUPERVISEUR,COMPTABLE,DG',
        ]);

        if (!empty($errors)) {
            $this->abortValidation($errors, 'utilisateurs/create');
        }

        $userModel = new Utilisateur();
        if ($userModel->existsByEmail($data['email'])) {
            Session::flash('error', 'Cet email est déjà utilisé.');
            $this->redirect('utilisateurs/create');
        }

        $userModel->createUser($data);
        Session::flash('success', 'Utilisateur créé.');
        $this->redirect('utilisateurs');
    }

    public function edit(string $id): void {
        Auth::requireRole(['DG']);
        $userModel = new Utilisateur();
        $this->render('dashboard/index', [
            'utilisateur' => $userModel->find((int)$id),
            'userRoles'   => ['AGENT', 'SUPERVISEUR', 'COMPTABLE', 'DG'],
        ], 'Modifier utilisateur');
    }

    public function update(string $id): void {
        Auth::requireRole(['DG']);
        $this->verifyCsrf();

        $data = [
            'nom'   => $this->post('nom'),
            'role'  => $this->post('role'),
            'actif' => $this->post('actif', 1),
        ];

        $errors = $this->validate($data, [
            'nom'  => 'required|max_length:100',
            'role' => 'required|in:AGENT,SUPERVISEUR,COMPTABLE,DG',
        ]);

        if (!empty($errors)) {
            $this->abortValidation($errors, 'utilisateurs/' . $id . '/edit');
        }

        if ($this->post('mot_de_passe')) {
            $password = $this->post('mot_de_passe');
            $passwordErrors = $this->validate(['mot_de_passe' => $password], ['mot_de_passe' => 'min_length:8']);
            if (!empty($passwordErrors)) {
                $this->abortValidation($passwordErrors, 'utilisateurs/' . $id . '/edit');
            }
            $data['mot_de_passe'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel = new Utilisateur();
        $userModel->update((int)$id, $data);
        Session::flash('success', 'Utilisateur mis à jour.');
        $this->redirect('utilisateurs');
    }
}
