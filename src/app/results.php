<?php
require '../db.php';
require '../session.php';

if (!isset($_SESSION['id'])) {
    header("Location: /auth/login.php");
    exit;
}

$pdo = get_db();

$statementCheck = $pdo->prepare("SELECT * FROM `scores` WHERE `user_id` = ? AND `date` = CURDATE()");
$statementCheck->execute([$_SESSION['id']]);
$row = $statementCheck->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header("Location: /app");
    exit;
}

$statement = $pdo->prepare("SELECT scores.*, users.username FROM `scores` JOIN `users` ON users.id = scores.user_id WHERE `date` = CURDATE()");
$statement->execute();
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Results | Cross-Site Quizzing</title>
    <link rel="stylesheet" href="/app.css">
  </head>
  <body>
    <?php include('../nav.php') ?>
    <section class="section">
        <div class="container">
            <h1 class="title">
                Other's Answers
            </h1>
            <p class="subtitle">
                See what everyone has answered for today's questions!
            </p>
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Q1: What is the capital of France?</th>
                        <th>Q2: What does the cow say?</th>
                        <th>Q3: What does GGEZ stand for?</th>
                        <th>Q4: In the song 'Thick Of It', what is KSI reaching for?</th>
                        <th>Q5: In the viral tribute song, who spoke the truth when the cost was high?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php if ($secure): ?>
                            <th><?= htmlspecialchars($row['username'] ?? '') ?></th>
                            <td><?= htmlspecialchars($row['q1_answer'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['q2_answer'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['q3_answer'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['q4_answer'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['q5_answer'] ?? '') ?></td>
                        <?php else: ?>
                            <th><?= $row['username'] ?></th>
                            <td><?= $row['q1_answer'] ?></td>
                            <td><?= $row['q2_answer'] ?></td>
                            <td><?= $row['q3_answer'] ?></td>
                            <td><?= $row['q4_answer'] ?></td>
                            <td><?= $row['q5_answer'] ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
  </body>
</html>