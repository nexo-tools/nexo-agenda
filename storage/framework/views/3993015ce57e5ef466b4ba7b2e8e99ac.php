<?php $__env->startComponent('emails.layout', ['businessName' => $booking->business->name]); ?>
    <h1 style="font-size:20px;margin:0 0 8px;"><?php echo e(__('Nueva reserva')); ?></h1>
    <p style="margin:0;">
        <?php echo e(__(':client reservó un turno.', ['client' => $booking->client_name])); ?>

        <?php if($booking->client_phone): ?>
            · <?php echo e($booking->client_phone); ?>

        <?php endif; ?>
        <?php if($booking->client_email): ?>
            · <?php echo e($booking->client_email); ?>

        <?php endif; ?>
    </p>

    <?php echo $__env->make('emails.partials.booking-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($booking->note): ?>
        <p style="font-size:14px;"><strong><?php echo e(__('Nota')); ?>:</strong> <?php echo e($booking->note); ?></p>
    <?php endif; ?>

    <a href="<?php echo e(route('dashboard', ['date' => $booking->starts_at->setTimezone($booking->business->timezone)->toDateString()])); ?>"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">
        <?php echo e(__('Ver en mi agenda')); ?>

    </a>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/html/resources/views/emails/new-booking-received.blade.php ENDPATH**/ ?>