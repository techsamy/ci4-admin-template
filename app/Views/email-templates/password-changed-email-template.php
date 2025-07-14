<p>Dear <b><?= $mailData['user']->name ?></b>,</p>

<p>
    Your password on <b><?= htmlspecialchars(getenv('EMAIL_FROM_NAME')); ?></b> system was changed successfully. Below are your new login credentials:
    <br><br>
    <b>Login ID:</b> <?= $mailData['user']->username ?> or <?= $mailData['user']->email ?><br>
    <b>Password:</b> <?= $mailData['new_password'] ?>
</p>

<br><br>

<p>
    Please keep your credentials confidential. Your username and password are your responsibility and should never be shared with anyone.
</p>

<p>
    <?= htmlspecialchars(getenv('EMAIL_FROM_NAME')); ?> will not be liable for any misuse of your username or password.
</p>

<br>

<hr style="border: 0; border-top: 1px dashed #ccc;">

<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    Regards,<br>
    <strong><?= htmlspecialchars(getenv('EMAIL_FROM_NAME')); ?></strong>
</p>