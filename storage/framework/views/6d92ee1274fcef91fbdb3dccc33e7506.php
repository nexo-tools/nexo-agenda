<?php $__env->startComponent('emails.layout', ['businessName' => $booking->business->name]); ?>
    <h1 style="font-size:20px;margin:0 0 8px;"><?php echo e(__('Turno reprogramado')); ?></h1>
    <p style="margin:0;"><?php echo e(__('Hola :name, tu turno tiene nueva fecha y hora.', ['name' => $booking->client_name])); ?></p>

    <?php echo $__env->make('emails.partials.booking-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        <?php echo e(__('El enlace de gestión de tu email de confirmación sigue siendo válido. Adjuntamos el evento actualizado para tu calendario.')); ?>

    </p>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /app/resources/views/emails/booking-rescheduled.blade.php ENDPATH**/ ?>