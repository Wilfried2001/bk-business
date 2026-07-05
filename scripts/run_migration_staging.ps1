<#
Script d'exécution des migrations et backfill en staging.
Usage: lancer depuis la racine du repo:
  powershell -ExecutionPolicy Bypass -File .\scripts\run_migration_staging.ps1

Le script :
 - Demande les paramètres DB (host, user, dbname) et confirme les étapes.
 - Fait un dump de sauvegarde via `mysqldump`.
 - Applique le SQL de migration avec `mysql`.
 - Exécute le backfill PHP idempotent.
 - Lance la suite d'intégration PHPUnit.

Prérequis : `mysqldump`, `mysql`, `php`, `vendor\bin\phpunit` disponibles dans PATH.
#>

param()

Write-Host "-- Migration multi‑agence: backup -> migration -> backfill -> tests --" -ForegroundColor Cyan

$dbHost = Read-Host "DB Host (ex: localhost)"
$dbUser = Read-Host "DB User"
$dbName = Read-Host "DB Name"
$confirm = Read-Host "Confirmez-vous l'opération sur la base '$dbName'@$dbHost ? Tapez O pour continuer"
if ($confirm -ne 'O' -and $confirm -ne 'o') {
    Write-Host "Opération annulée." -ForegroundColor Yellow
    exit 1
}

# Paths
$repoRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
$migrationSql = Join-Path $repoRoot 'database\migrations\2026_07_05_add_id_agence_columns.sql'
$backfillPhp = Join-Path $repoRoot 'database\migrations\backfill_assign_default_agence.php'
$phpunit = Join-Path $repoRoot 'vendor\bin\phpunit'
$dumpFile = Join-Path $repoRoot ("bk_business_pre_mig_{0}.sql" -f (Get-Date -Format 'yyyyMMdd_HHmmss'))

# Sanity checks
if (-not (Test-Path $migrationSql)) { Write-Error "Fichier de migration introuvable: $migrationSql"; exit 2 }
if (-not (Test-Path $backfillPhp)) { Write-Error "Script backfill introuvable: $backfillPhp"; exit 2 }

# 1) Backup
Write-Host "1) Sauvegarde de la base vers: $dumpFile" -ForegroundColor Green
Write-Host "Vous serez invité à entrer le mot de passe DB (mysqldump)." -ForegroundColor Yellow
$dumpCmd = "mysqldump -h $dbHost -u $dbUser -p $dbName > `"$dumpFile`""
Write-Host "Exécution: $dumpCmd"
cmd.exe /c $dumpCmd
if ($LASTEXITCODE -ne 0) { Write-Error "Erreur lors du dump (exit $LASTEXITCODE)."; exit 3 }

# 2) Appliquer migration SQL
Write-Host "2) Application de la migration SQL: $migrationSql" -ForegroundColor Green
Write-Host "Vous serez invité à entrer le mot de passe DB (mysql)." -ForegroundColor Yellow
$applyCmd = "mysql -h $dbHost -u $dbUser -p $dbName < `"$migrationSql`""
Write-Host "Exécution: $applyCmd"
cmd.exe /c $applyCmd
if ($LASTEXITCODE -ne 0) { Write-Error "Erreur lors de l'application SQL (exit $LASTEXITCODE)."; exit 4 }

# 3) Backfill PHP
Write-Host "3) Lancement du backfill PHP: $backfillPhp" -ForegroundColor Green
Write-Host "Exécution: php `"$backfillPhp`""
php "$backfillPhp"
if ($LASTEXITCODE -ne 0) { Write-Error "Erreur lors du backfill PHP (exit $LASTEXITCODE)."; exit 5 }

# 4) Lancer tests d'intégration
Write-Host "4) Lancement de la suite d'intégration PHPUnit" -ForegroundColor Green
if (-not (Test-Path $phpunit)) { Write-Warning "phpunit introuvable: $phpunit. Essayez 'composer install' ou ajustez le chemin." }
else {
    Write-Host "Exécution: $phpunit --configuration phpunit.xml --testsuite Integration"
    & $phpunit --configuration phpunit.xml --testsuite Integration
    if ($LASTEXITCODE -ne 0) { Write-Warning "Les tests d'intégration ont échoué (exit $LASTEXITCODE)." }
}

Write-Host "Opération terminée." -ForegroundColor Cyan
