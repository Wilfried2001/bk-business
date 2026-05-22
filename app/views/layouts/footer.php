        </main>
    </div>
</div>
<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <small>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> - Tous droits réservés.</small>
            </div>
            <div class="col-md-6 text-end">
                <small>v<?= e(APP_VERSION) ?> | <a href="#" class="text-white-50">Aide</a></small>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Modal global pour afficher les détails d'une ligne (mobile) -->
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
        </div>
    </div>
</div>

<script src="<?= url('js/script.js') ?>"></script>
</body>
</html>
