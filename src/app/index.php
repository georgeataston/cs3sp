<?php
session_start();
require '../db.php';
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

if (!isset($_SESSION['id'])) {
    header("Location: /auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $q1 = trim($_POST['q1']) ?? "Didn't answer.";
    $q2 = trim($_POST['q2']) ?? "Didn't answer.";
    $q3 = trim($_POST['q3']) ?? "Didn't answer.";
    $q4 = trim($_POST['q4']) ?? "Didn't answer.";
    $q5 = trim($_POST['q5']) ?? "Didn't answer.";

    if ($secure) {
        if (strlen($q1) < 1 || strlen($q1) > 255) {
            $errors['q1'] = "Your answer must be between 1 and 255 characters.";
        }

        if (strlen($q2) < 1 || strlen($q2) > 255) {
            $errors['q2'] = "Your answer must be between 1 and 255 characters.";
        }

        if (strlen($q3) < 1 || strlen($q3) > 255) {
            $errors['q3'] = "Your answer must be between 1 and 255 characters.";
        }

        if (strlen($q4) < 1 || strlen($q4) > 255) {
            $errors['q4'] = "Your answer must be between 1 and 255 characters.";
        }

        if (strlen($q5) < 1 || strlen($q5) > 255) {
            $errors['q5'] = "Your answer must be between 1 and 255 characters.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: /app");
            exit;
        }

        $pdo = get_db();
        $statement = $pdo->prepare("INSERT INTO `scores` (`date`, `user_id`, `q1_answer`, `q2_answer`, `q3_answer`, `q4_answer`, `q5_answer`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $statement->execute([date('Y-m-d'), $_SESSION['id'], $q1, $q2, $q3, $q4, $q5]);

        if ($result !== false) {
            header("Location: /app/results.php");
            exit;
        } else {
            $_SESSION['errors']['general'] = "Submission failed. Please try again later.";
            header("Location: /app");
        }
    } else {
        $pdo = get_db();
        $date = date('Y-m-d');
        $userId = $_SESSION['id'];
        $sql = $pdo->prepare("INSERT INTO `scores` (`date`, `user_id`, `q1_answer`, `q2_answer`, `q3_answer`, `q4_answer`, `q5_answer`) VALUES '$date', '$userId', '$q1', '$q2', '$q3', '$q4', '$q5')");
        $result = $pdo->exec($sql);

        if ($result !== false) {
            header("Location: /app/results.php");
            exit;
        } else {
            $_SESSION['errors']['general'] = "Submission failed. Please try again later.";
            header("Location: /app");
        }
    }

} else {
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);

    $pdo = get_db();
    $statement = $pdo->prepare("SELECT * FROM `scores` WHERE `user_id` = ? AND `date` = CURDATE()");
    $statement->execute([$_SESSION['id']]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        header("Location: /app/results.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | Cross-Site Quizzing</title>
    <link rel="stylesheet" href="/app.css">
  </head>
  <body>
    <?php include('../nav.php') ?>
    <section class="section">
        <div class="container">
            <h1 class="title">
                Today's Questions
            </h1>
            <p class="subtitle">
                Once you have answered, you can view everyone else's!
            </p>
            <form method="post">
                <div class="field">
                    <label class="label">Question 1: What is the capital of France?</label>
                    <div class="control">
                        <input class="input" type="text" name="q1" value="<?= htmlspecialchars($old['q1'] ?? '') ?>">
                        <?php if (isset($errors['q1'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['q1']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Question 2: What does the cow say?</label>
                    <div class="control">
                        <input class="input" type="text" name="q2" value="<?= htmlspecialchars($old['q2'] ?? '') ?>">
                        <?php if (isset($errors['q2'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['q2']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Question 3: What does GGEZ stand for?</label>
                    <div class="control">
                        <input class="input" type="text" name="q3" value="<?= htmlspecialchars($old['q3'] ?? '') ?>">
                        <?php if (isset($errors['q3'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['q3']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Question 4: In the song 'Thick Of It', what is KSI reaching for?</label>
                    <div class="control">
                        <input class="input" type="text" name="q4" value="<?= htmlspecialchars($old['q4'] ?? '') ?>">
                        <?php if (isset($errors['q4'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['q4']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Question 5: In the viral tribute song, who spoke the truth when the cost was high?</label>
                    <div class="control">
                        <input class="input" type="text" name="q5" value="<?= htmlspecialchars($old['q5'] ?? '') ?>">
                        <?php if (isset($errors['q5'])): ?>
                            <p class="help is-danger"><?= htmlspecialchars($errors['q5']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="control">
                    <button class="button is-primary">Submit</button>
                </div>
                <?php if (isset($errors['general'])): ?>
                    <p class="help is-danger"><?= htmlspecialchars($errors['general']) ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>
  </body>
</html>