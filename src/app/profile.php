<?php
session_start();
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
                <?= htmlspecialchars($_SESSION['username']) ?>'s Profie
            </h1>
            <p class="subtitle">
                Update your profile picture or password
            </p>
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