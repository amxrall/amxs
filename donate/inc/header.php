<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php if(isLoggedIn()): ?>
    <header>
        <div class="container nav-flex">
            <div class="logo">L2 Stone PVP</div>
<nav>
    <a href="dashboard.php" class="btn btn-outline">Painel</a>
    <a href="buy.php" class="btn btn-outline">Doar</a>
    <a href="transfer.php" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);">Resgatar</a> <a href="logout.php" class="btn btn-outline">Sair</a>
</nav>
        </div>
    </header>
    <?php endif; ?>
    <div class="container main-content"></div>