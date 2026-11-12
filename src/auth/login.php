<?php
session_start();
require '../db.php';
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = get_db();

    if ($secure) {
        $username = trim($_POST['username']) ?? '';
        $password = $_POST['password'] ?? '';

        $statement = $pdo->prepare("SELECT `id`, `password` FROM `users` WHERE `username` = ?");
        $statement->execute([$username]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT `id`, `password` FROM `users` WHERE `username` = '$username'";
        $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['id'] = $row['id'];
        header("Location: /home");
    } else {
        $_SESSION['errors']['general'] = "Invalid username or password.";
        header("Location: /auth/login.php");
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Cross-Site Quizzing</title>
    <link rel="stylesheet" href="/app.css">
  </head>
  <body>
    <?php include('../nav.php') ?>
    <section class="section">
        <div class="container">
        <h1 class="title">
            Login
        </h1>
        <p class="subtitle">
            Ready to test your knowledge?
        </p>
            <form method="post">
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control">
                        <input class="input" type="text" name="username" placeholder="your username...">
                        <?php if (isset($errors['username'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['username']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input" type="password" name="password">
                        <?php if (isset($errors['password'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary">Login</button>
                </div>
                <?php if (isset($errors['general'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general']) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>
  </body>
</html>