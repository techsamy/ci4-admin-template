<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    Dear <?= htmlspecialchars($mailData['user']->name); ?>,
</p>

<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333; line-height: 1.5;">
    We received a request to reset your password for the account associated with the email address 
    <strong><?= htmlspecialchars($mailData['user']->email); ?></strong>.
</p>

<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333; line-height: 1.5;">
    Click the button below to reset your password:
</p>

<p style="text-align: center; margin: 30px 0;">
    <a href="<?= htmlspecialchars($mailData['actionLink']); ?>" target="_blank" style="color: #fff; background-color: #22bc66; border: none; padding: 12px 24px; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); display: inline-block;">
        Reset Password
    </a>
</p>

<p style="font-family: Arial, sans-serif; font-size: 14px; color: #ff0000; font-weight: bold;">
    Note: This link will expire in 60 minutes.
</p>

<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333; line-height: 1.5;">
    If you did not request this password reset, you can safely ignore this email.
</p>

<p style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    Regards,<br>
    <strong><?= htmlspecialchars(getenv('EMAIL_FROM_NAME')); ?></strong>
</p>
