<?php extract($arg); ?>
<?php if ($page == 'apology'): ?>
    <p class="lead text-error">
        Sorry!
    </p>
    <p class="text-error">
        <?= htmlspecialchars($message) ?>
    </p>

    <a href="javascript:history.go(-1);">Back</a>

<? elseif($page == 'forgot'): ?>

    <p class="lead text-error">
        Done!
    </p>
    <p class="text-error">
        Password has been reset. Email sent to <?= $email ?>
    </p>

    Go to <a href="/login.php">Login</a>
<? endif ?>