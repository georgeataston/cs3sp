<?php

$secure = filter_var(getenv('SECURE'), FILTER_VALIDATE_BOOLEAN);

?>

<nav class="navbar is-primary" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item has-text-weight-bold has-text-white" href="/">XS-Q: <?php echo ($secure ? "Secure" : "Insecure"); ?></a>
    </div>
        <div class="navbar-end">
    <div class="navbar-item">
        <div class="buttons">
        <a class="button is-primary">
            <strong>Sign up</strong>
        </a>
        <a class="button is-light">
            Log in
        </a>
        </div>
    </div>
</nav>