<?php
require '../db.php';
require '../session.php';

if (!isset($_SESSION['id'])) {
    header("Location: /auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id == null) {
    $id = $_SESSION['id'];
}

if ($secure && $id != $_SESSION['id']) {
    $id = $_SESSION['id'];
}

$pdo = get_db();
$statement = $pdo->prepare("SELECT `username`, `email`, `profile_pic_url` FROM `users` WHERE `id` = ?");
$statement->execute([$id]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo('404 not found.');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old']);

$old['pfp'] ??= $row['profile_pic_url'] ?? '';
$previewUrl = trim($old['pfp']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action'] ?? '') {
        case "preview_ppic":
            $url = trim($_POST['pfp'] ?? '');
            $previewUrl = $url;
            $old['pfp'] = $url; 
            break;
        case "update_ppic":
            $statement = $pdo->prepare("UPDATE users SET profile_pic_url = ? WHERE id = ?");
            $url = trim($_POST['pfp'] ?? '');
            $result = $statement->execute([$url, $_SESSION['id']]);

            if ($result !== false) {
                $_SESSION['success']['general_pfp'] = "Profile picture updated!";
                header("Location: /app/profile.php");
            } else {
                $_SESSION['errors']['general_pfp'] = "Update failed. Please try again later.";
                header("Location: /app/profile.php");
            }
            break;
        case "update_password":
            if ($secure) {
                if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
                    $_SESSION['errors']['general_change'] = "Invalid CSRF. Please try logging out then back in again.";
                    header("Location: /app/profile.php");
                    exit;
                }
            }

            $new = trim($_POST['new'] ?? '');
            $confirm = trim($_POST['confirm'] ?? '');

            if ($new !== $confirm) {
                $_SESSION['errors']['confirm'] = "Password does not match.";
                header("Location: /app/profile.php");
                exit;
            }

            if (strlen($new) < 8) {
                $_SESSION['errors']['new'] = "Password must be greater than 7 characters.";
                header("Location: /app/profile.php");
                exit;
            }

            $hash = password_hash($new, PASSWORD_DEFAULT);
            $statement = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $statement->execute([$hash, $_SESSION['id']]);

            if ($result !== false) {
                $_SESSION['success']['general_change'] = "Password updated!";
                header("Location: /app/profile.php");
            } else {
                $_SESSION['errors']['general_change'] = "Update failed. Please try again later.";
                header("Location: /app/profile.php");
            }
            break;
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
                <input type="hidden" name="action" value="update_password">
                <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">

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
                <?php if (isset($success['general_change'])): ?>
                    <p class="help is-success"><?= htmlspecialchars($success['general_change']) ?></p>
                <?php endif; ?>
            </form>
            <br>
            <form method="post">
                <div class="field">
                    <label class="label">Profile Picture URL</label>
                    <div class="control">
                        <input class="input" type="text" name="pfp" value="<?= htmlspecialchars($old['pfp'] ?? '') ?>">
                        <?php if (isset($errors['pfp'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['pfp']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($previewUrl)): ?>
                    <figure class="image is-128x128 mb-4">
                        <img src="preview.php?url=<?= urlencode($previewUrl) ?>" style="height: 100%; object-fit: cover;">
                    </figure>
                <?php endif; ?>

                <div class="control">
                    <button class="button is-primary" name="action" value="preview_ppic">Preview</button>
                    <button class="button is-primary" name="action" value="update_ppic">Save</button>
                </div>
                <?php if (isset($errors['general_pfp'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general_pfp']) ?></p>
                <?php endif; ?>
                <?php if (isset($success['general_pfp'])): ?>
                    <p class="help is-success"><?= htmlspecialchars($success['general_pfp']) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>
  </body>
</html>