<?php $__env->startComponent('mail::message'); ?>
# Hello!

We received a request to reset your password for your account.

<?php $__env->startComponent('mail::button', ['url' => $url]); ?>
Reset Password
<?php echo $__env->renderComponent(); ?>

If you did not request a password reset, please ignore this email.

Thank you for using our application!

Best regards,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\Ferdinand\Desktop\opticalcare\resources\views/emails/reset_password.blade.php ENDPATH**/ ?>