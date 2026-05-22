<?php
class CommissionController extends Controller {

    public function index(): void {
        Auth::requireRole(['COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/CommissionTransaction.php';

        $commModel = new CommissionTransaction();
        $mois  = (int)($this->get('mois')  ?: date('m'));
        $annee = (int)($this->get('annee') ?: date('Y'));

        $this->render('commissions/index', [
            'benefices'  => $commModel->getBeneficesParService($mois, $annee),
            'total'      => $commModel->getTotalCommissions($mois, $annee),
            'mois'       => $mois,
            'annee'      => $annee,
        ], 'Commissions et bénéfices');
    }

    public function config(): void {
        Auth::requireRole(['COMPTABLE', 'DG']);
        require_once APP_PATH . '/models/CommissionConfig.php';
        require_once APP_PATH . '/models/Service.php';
        require_once APP_PATH . '/models/TypeOperation.php';

        $configModel = new CommissionConfig();
        $configs = $configModel->getAllWithDetails();
        foreach ($configs as &$config) {
            $config['tranches'] = $configModel->getTranchesByConfig((int)$config['id_config']);
        }
        unset($config);

        $serviceModel = new Service();
        $typeModel = new TypeOperation();

        $this->render('commissions/config', [
            'configs'   => $configs,
            'services'  => $serviceModel->getAllActifs(),
            'types'     => $typeModel->all('libelle'),
        ], 'Paramétrage des commissions');
    }

    public function saveConfig(): void {
        Auth::requireRole(['COMPTABLE', 'DG']);
        $this->verifyCsrf();

        $idConfig   = (int) $this->post('id_config');
        $modeCalcul = strtoupper($this->post('mode_calcul') ?? '');
        $source     = $this->post('source');
        $nom        = trim((string) $this->post('nom'));
        $valeur     = $modeCalcul === 'TRANCHE' ? 0.0 : (float) $this->post('valeur');
        $idService  = (int) $this->post('id_service');
        $idType     = (int) $this->post('id_type');

        $errors = $this->validate([
            'nom'         => $nom,
            'source'      => $source,
            'mode_calcul' => $modeCalcul,
            'id_service'  => $idService,
            'id_type'     => $idType,
            'valeur'      => $valeur,
        ], [
            'nom'         => 'required|max_length:120',
            'source'      => 'required|max_length:120',
            'mode_calcul' => 'required|in:TAUX,FIXE,TRANCHE',
            'id_service'  => 'required|integer|positive',
            'id_type'     => 'required|integer|positive',
            'valeur'      => $modeCalcul === 'TRANCHE' ? 'numeric' : 'required|numeric|positive',
        ]);

        if (!empty($errors)) {
            $this->abortValidation($errors, 'commissions/config');
        }

        $tranches = [];
        if ($modeCalcul === 'TRANCHE') {
            $tranchesData  = $this->post('tranches') ?: [];
            $montantMins  = $tranchesData['montant_min'] ?? [];
            $montantMaxs  = $tranchesData['montant_max'] ?? [];
            $montantFixes = $tranchesData['montant_fixe'] ?? [];

            $count = max(count($montantMins), count($montantFixes));
            for ($i = 0; $i < $count; $i++) {
                $min  = $montantMins[$i]  ?? '';
                $max  = $montantMaxs[$i]  ?? '';
                $fixe = $montantFixes[$i] ?? '';

                if ($min === '' && $fixe === '') {
                    continue;
                }

                if ($min === '' || $fixe === '') {
                    Session::flash('error', 'Chaque tranche doit contenir un montant min et un montant fixe.');
                    $this->redirect('commissions/config');
                }

                $tranches[] = [
                    'montant_min'  => (float)$min,
                    'montant_max'  => $max === '' ? null : (float)$max,
                    'montant_fixe' => (float)$fixe,
                ];
            }

            if (empty($tranches)) {
                Session::flash('error', 'Veuillez ajouter au moins une tranche de commission valide.');
                $this->redirect('commissions/config');
            }
        }

        $configModel = new CommissionConfig();

        if ($idConfig) {
            $configModel->update($idConfig, [
                'nom'         => $nom,
                'source'      => $source,
                'mode_calcul' => $modeCalcul,
                'valeur'      => $valeur,
            ]);

            if ($modeCalcul === 'TRANCHE') {
                if (!$configModel->saveTranches($idConfig, $tranches)) {
                    Session::flash('error', 'Impossible d’enregistrer les tranches de commission.');
                    $this->redirect('commissions/config');
                }
            } else {
                $configModel->clearTranches($idConfig);
            }
        } else {
            try {
                $idConfig = $configModel->create([
                    'id_service'  => $idService,
                    'id_type'     => $idType,
                    'nom'         => $nom,
                    'source'      => $source,
                    'mode_calcul' => $modeCalcul,
                    'valeur'      => $valeur,
                ]);
            } catch (Exception $e) {
                Session::flash('error', 'Impossible de créer la configuration. Vérifiez que cette combinaison n’existe pas déjà.');
                $this->redirect('commissions/config');
            }

            if ($modeCalcul === 'TRANCHE' && !$configModel->saveTranches($idConfig, $tranches)) {
                Session::flash('error', 'Impossible d’enregistrer les tranches de commission.');
                $this->redirect('commissions/config');
            }
        }

        Session::flash('success', 'Configuration enregistrée.');
        $this->redirect('commissions/config');
    }
}
