<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME) ?> - Gestion d'entreprise</title>
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('css/business-views.css') ?>">
    <link rel="stylesheet" href="<?= url('css/dark-fix.css') ?>">
    <script>
        (function () {
            const storedTheme = localStorage.getItem('bk-theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.toggle('theme-dark', theme === 'dark');
            document.documentElement.classList.toggle('theme-light', theme === 'light');
        })();
    </script>
    <script src="<?= url('js/icons.js') ?>"></script>
    <style>
    html {
        scroll-behavior: smooth;
    }
    </style>
</head>

<body class="fade-in">
