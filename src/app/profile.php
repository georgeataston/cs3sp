<?php
session_start();
require '../db.php';
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if (!isset($_SESSION['id'])) {
    header("Location: /auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    if ($id == null) {
        header("Location: /app/profile.php?id=" . $_SESSION['id']);
        exit;
    }

    if ($secure && $id != $_SESSION['id']) {
        header("Location: /app/profile.php?id=" . $_SESSION['id']);
        exit;
    }
    $pdo = get_db();
    $statement = $pdo->prepare("SELECT `username`, `email`, `profile_pic_url` FROM `users` WHERE `id` = ?");
    $statement->execute([$id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo('404 not found.');
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Cross-Site Quizzing</title>
    <link rel="stylesheet" href="/app.css">
  </head>
  <body>
    <?php include('../nav.php') ?>
    <section class="section">
        <div class="container">
            <h1 class="title">
                <?= htmlspecialchars($row['username']) ?>'s Profie
            </h1>
            <p class="subtitle">
                Update your profile picture or password
            </p>
            <p>Email: <?= htmlspecialchars($row['email']) ?></p>
            <br>
            <form method="post">
                <div class="field">
                    <label class="label">Old Password</label>
                    <div class="control">
                        <input class="input" type="password" name="old">
                        <?php if (isset($errors['old'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['old']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field">
                    <label class="label">New Password</label>
                    <div class="control">
                        <input class="input" type="password" name="new">
                        <?php if (isset($errors['new'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['new']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Confirm New Password</label>
                    <div class="control">
                        <input class="input" name="confirm" type="password">
                        <?php if (isset($errors['confirm'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['confirm']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="control">
                    <button class="button is-primary">Change Password</button>
                </div>
                <?php if (isset($errors['general_change'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general_change']) ?></p>
                <?php endif; ?>
            </form>
            <br>
            <form method="post">
                <div class="field">
                    <label class="label">Profile Picture URL</label>
                    <div class="control">
                        <input class="input" type="text" name="pfp">
                        <?php if (isset($errors['pfp'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['pfp']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary">Preview</button>
                    <button class="button is-primary">Save</button>
                </div>
                <?php if (isset($errors['general_pfp'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general_pfp']) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>
  </body>
</html>