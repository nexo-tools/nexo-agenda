<?php $__env->startComponent('emails.layout', ['businessName' => $booking->business->name]); ?>
    <h1 style="font-size:20px;margin:0 0 8px;"><?php echo e(__('¡Turno confirmado!')); ?></h1>
    <p style="margin:0;"><?php echo e(__('Hola :name, tu reserva quedó confirmada.', ['name' => $booking->client_name])); ?></p>

    <?php echo $__env->make('emails.partials.booking-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <a href="<?php echo e(route('booking.manage', $managementToken)); ?>"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">
        <?php echo e(__('Ver o gestionar mi turno')); ?>

    </a>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        <?php echo e(__('Guarda este email: desde el botón puedes reprogramar o cancelar (hasta :hours h antes). Adjuntamos el evento para tu calendario.', ['hours' => $booking->service->cancellation_hours])); ?>

    </p>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /app/resources/views/emails/booking-confirmed.blade.php ENDPATH**/ ?>