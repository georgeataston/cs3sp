<?php
$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);
$loggedIn = isset($_SESSION['id']);
?>

<nav class="navbar is-primary" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item has-text-weight-bold has-text-white" href="/">XS-Q: <?php echo ($secure ? "Secure" : "Insecure"); ?></a>
    </div>
        <div class="navbar-end">
    <div class="navbar-item">
        <div class="buttons">
            <?php if (!$loggedIn): ?>
                <a class="button is-primary" href="/auth/register.php">
                    <strong>Sign up</strong>
                </a>
                <a class="button is-light" href="/auth/login.php">
                    Log in
                </a>
            <?php else: ?>
                <a class="button is-primary" href="/app/profile.php">
                    <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </a>
                <a class="button is-light" href="/auth/logout.php">
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>