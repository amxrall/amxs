<?php
require_once 'inc/db.php';
require_once 'inc/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // L2 usa SHA1 por padrão na maioria das revs
    $passHash = base64_encode(pack('H*', sha1($password))); 
    // OBS: Se sua rev usa apenas sha1($pass), mude a linha acima para: $passHash = sha1($password);

    $stmt = $pdo->prepare("SELECT login FROM accounts WHERE login = ? AND password = ?");
    $stmt->execute([$login, $passHash]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_login'] = $user['login'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login ou senha incorretos.";
    }
}

require_once 'inc/header.php';
?>

<div style="max-width: 400px; margin: 100px auto;">
    <div class="card">
        <h2 class="card-title text-center">Acesso ao Painel</h2>
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Usuário</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">ENTRAR</button>
        </form>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>