<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i data-lucide="receipt"></i> Transaction #<?= e($transaction['id_transaction']) ?></h1>
        <p class="text-gray-500">Détail de la transaction.</p>
    </div>
    <?php if ($transaction['statut'] !== 'ANNULEE' && Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
        <div class="flex gap-2">
            <a href="<?= url('transactions/' . $transaction['id_transaction'] . '/edit') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff]">
                <i data-lucide="edit-2"></i> Modifier
            </a>
            <form action="<?= url('transactions/' . $transaction['id_transaction'] . '/cancel') ?>" method="post">
                <?= csrfField() ?>
                <button type="submit" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-red-600 text-white">
                    <i data-lucide="x-circle"></i> Annuler
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
<div class="flex flex-wrap -mx-3 gy-4">
    <div class="lg:w-1/2 px-3">
        <div class="app-card shadow-sm mb-4">
            <div class="app-card-header">
                <i data-lucide="info"></i> Informations
            </div>
            <div class="app-card-body">
                <dl class="flex flex-wrap -mx-3 mb-0">
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Service</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['nom_service']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Type</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['libelle_type']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Montant</dt>
                    <dd class="sm:w-7/12 px-3"><?= e(formatMontant((float)$transaction['montant'])) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Agent</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['nom_agent']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Agence</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['nom_agence'] ?? $transaction['agence'] ?? '-') ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Date</dt>
                    <dd class="sm:w-7/12 px-3"><?= e(formatDate($transaction['date_heure'])) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Statut</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['statut']) ?></dd>
                    <dt class="sm:w-5/12 px-3 font-semibold text-slate-600">Référence</dt>
                    <dd class="sm:w-7/12 px-3"><?= e($transaction['reference'] ?: '-') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="lg:w-1/2 px-3">
        <div class="app-card shadow-sm mb-4">
            <div class="app-card-header">
                <i data-lucide="message-square"></i> Note
            </div>
            <div class="app-card-body">
                <p class="mb-0"><?= nl2br(e($transaction['note'] ?: 'Aucune note.')) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="app-card shadow-sm mb-4">
    <div class="app-card-header">
        <i data-lucide="activity"></i> Mouvements de solde
    </div>
    <div class="app-card-body">
        <?php if (empty($mouvements)): ?>
            <p class="mb-0">Aucun mouvement enregistré.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Type solde</th>
                            <th>Nature</th>
                            <th>Montant</th>
                            <th>Solde avant</th>
                            <th>Solde après</th>
                            <th>Date</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mouvements as $mvt): ?>
                            <tr>
                                <td><?= e($mvt['type_solde']) ?></td>
                                <td><?= e($mvt['nature']) ?></td>
                                <td><?= e(formatMontant((float)$mvt['montant'])) ?></td>
                                <td><?= e(formatMontant((float)$mvt['solde_avant'])) ?></td>
                                <td><?= e(formatMontant((float)$mvt['solde_apres'])) ?></td>
                                <td><?= e(formatDate($mvt['date_heure'])) ?></td>
                                <td><?= e($mvt['motif'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
