<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-boxes"></i> Stocks</h1>
        <p class="text-muted">Suivi des soldes FLOAT et CAISSE par service.</p>
    </div>
</div>
    <!-- Modale mobile pour détails / édition rapide -->
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.baseUrl = <?= json_encode(BASE_URL) ?>;
        window.canEditSeuil = <?= json_encode(Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])) ?>;
        window.csrfToken = <?= json_encode(generateCsrf()) ?>;
    </script>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-list-ul"></i> Soldes par service</strong>
            <div class="text-muted small">Vue consolidée des soldes par service.</div>
        </div>
        <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
            <div class="d-flex gap-2">
                <a href="<?= url('stocks/define') ?>" class="btn btn-sm btn-success">Définir les stocks</a>
                <a href="<?= url('stocks/seuils/all') ?>" class="btn btn-sm btn-info">Gérer les seuils</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($soldes)): ?>
            <p class="mb-0">Aucun solde disponible.</p>
        <?php else: ?>
            <?php
                // Regrouper par service pour afficher une ligne par service (Float + Caisse)
                $services = [];
                foreach ($soldes as $row) {
                    $sid = $row['id_service'];
                    if (!isset($services[$sid])) {
                        $services[$sid] = [
                            'id_service' => $sid,
                            'nom_service' => $row['nom_service'],
                            'categorie' => $row['categorie'],
                            'float' => null,
                            'caisse' => null,
                        ];
                    }
                    if ($row['type_solde'] === 'FLOAT') {
                        $services[$sid]['float'] = $row;
                    } else {
                        $services[$sid]['caisse'] = $row;
                    }
                }
            ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th class="d-none d-md-table-cell">Catégorie</th>
                            <th>Float</th>
                            <th>Caisse</th>
                            <th>Seuils</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $s): ?>
                            <tr>
                                <td><?= e($s['nom_service']) ?></td>
                                <td class="d-none d-md-table-cell"><?= e($s['categorie']) ?></td>
                                <td>
                                    <?php if ($s['float']): ?>
                                        <?= e(formatMontant((float)$s['float']['montant_actuel'])) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($s['caisse']): ?>
                                        <?= e(formatMontant((float)$s['caisse']['montant_actuel'])) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $seuils = [];
                                        if ($s['float'] && $s['float']['valeur_seuil'] !== null) $seuils[] = 'F: '.formatMontant((float)$s['float']['valeur_seuil']);
                                        if ($s['caisse'] && $s['caisse']['valeur_seuil'] !== null) $seuils[] = 'C: '.formatMontant((float)$s['caisse']['valeur_seuil']);
                                        echo !empty($seuils) ? e(implode(' — ', $seuils)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $alerte = false;
                                        if ($s['float'] && $s['float']['en_alerte']) $alerte = true;
                                        if ($s['caisse'] && $s['caisse']['en_alerte']) $alerte = true;
                                    ?>
                                    <span class="badge bg-<?= e($alerte ? 'danger' : 'success') ?>"><?= e($alerte ? 'Alerte' : 'Normal') ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                                        <a href="<?= url('stocks/' . $s['id_service']) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
                                    <?php else: ?>
                                        <a href="<?= url('stocks/' . $s['id_service']) ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
