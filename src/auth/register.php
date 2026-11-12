<?php
session_start();
require '../db.php';
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']) ?? '';
    $email = trim($_POST['email']) ?? '';
    $password = $_POST['password'] ?? '';

    $pdo = get_db();

    if ($secure) {
        // Do validation checks
        $errors = [];

        if (strlen($username) < 3 || strlen($username) > 16) {
            $errors['username'] = "Username must be between 3 and 16 characters.";
        }

        if (strlen($email) > 100 || !preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $email)) {
            $errors['email'] = "Email must be valid and be less than 100 characters.";
        }

        if (strlen($password) < 8) {
            $errors['password'] = "Password must be greater than 7 characters.";
        }

        if (empty($errors)) {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $statement = $pdo->prepare("INSERT INTO `users` (`username`, `email`, `password`) VALUES (?, ?, ?)");
                $result = $statement->execute([$username, $email, $hash]);

                if ($result !== false) {
                    $_SESSION['id'] = $pdo->lastInsertId();
                    header("Location: /home");
                } else {
                    $_SESSION['errors']['general'] = "Registration failed. Please try again later.";
                    header("Location: /auth/register.php");
                }
            } catch (PDOException $pdoException) {
                $_SESSION['errors']['general'] = "Registration failed. Email or username may already be taken.";
                header("Location: /auth/register.php");
            } catch (Exception $exception) {
                $_SESSION['errors']['general'] = "Registration failed. Please try again later.";
                header("Location: /auth/register.php");
            }
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: /auth/register.php");
            exit;
        }
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (`username`, `email`, `password`) VALUES ('$username', '$email', '$hash')";
        $result = $pdo->exec($sql);
        if ($result !== false) {
            $_SESSION['id'] = $pdo->lastInsertId();
            header("Location: /home");
        } else {
            $_SESSION['errors']['general'] = "Registration failed. Please try again later.";
            header("Location: /auth/register.php");
        }
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
    <title>Register | Cross-Site Quizzing</title>
    <link rel="stylesheet" href="/app.css">
  </head>
  <body>
    <?php include('../nav.php') ?>
    <section class="section">
        <div class="container">
        <h1 class="title">
            Register
        </h1>
        <p class="subtitle">
            Register to test your knowledge.
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
                    <label class="label">Email</label>
                    <div class="control">
                        <input class="input" type="email" name="email" placeholder="you@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input" name="password" type="password">
                        <?php if (isset($errors['password'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="control">
                    <button class="button is-primary">Register</button>
                </div>
                <?php if (isset($errors['general'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general']) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>
  </body>
</html>