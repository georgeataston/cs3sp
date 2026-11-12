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
            <form>
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="your username...">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input" type="password">
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary">Login</button>
                </div>
            </form>
        </div>
    </section>
  </body>
</html>