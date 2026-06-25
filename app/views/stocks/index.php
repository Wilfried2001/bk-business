<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="package"></i> Stocks</h1>
        <p class="text-gray-500">Suivi des soldes FLOAT et CAISSE par service.</p>
    </div>
</div>
    <script>
        window.baseUrl = <?= json_encode(BASE_URL) ?>;
        window.canEditSeuil = <?= json_encode(Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])) ?>;
        window.csrfToken = <?= json_encode(generateCsrf()) ?>;
    </script>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header flex justify-between items-center">
        <div>
            <strong><i data-lucide="list"></i> Soldes par service</strong>
            <div class="text-gray-500 small">Vue consolidée des soldes par service.</div>
        </div>
        <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
            <div class="flex gap-2">
                <a href="<?= url('stocks/define') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs bg-green-600 text-white">
                    <i data-lucide="package-plus"></i> Définir les stocks
                </a>
                <a href="<?= url('stocks/seuils/all') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-info/40 bg-white text-info hover:bg-cyan-50">
                    <i data-lucide="sliders-horizontal"></i> Gérer les seuils
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="app-card-body">
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
            <div class="overflow-x-auto">
                <table class="app-table table-mobile-details">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th class="hidden md:table-cell">Catégorie</th>
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
                                <td class="hidden md:table-cell"><?= e($s['categorie']) ?></td>
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
                                    <span class="app-badge app-badge-<?= e($alerte ? 'danger' : 'success') ?>"><?= e($alerte ? 'Alerte' : 'Normal') ?></span>
                                </td>
                                <td class="text-right">
                                    <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                                        <a href="<?= url('stocks/' . $s['id_service']) ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                                            <i data-lucide="eye"></i> Détails
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= url('stocks/' . $s['id_service']) ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide px-2 py-1 text-xs border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50">
                                            <i data-lucide="eye"></i> Détails
                                        </a>
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
