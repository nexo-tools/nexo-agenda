<?php $__env->startComponent('emails.layout', ['businessName' => $booking->business->name]); ?>
    <h1 style="font-size:20px;margin:0 0 8px;"><?php echo e(__('Turno cancelado')); ?></h1>
    <p style="margin:0;"><?php echo e(__('Hola :name, tu turno fue cancelado.', ['name' => $booking->client_name])); ?></p>

    <?php echo $__env->make('emails.partials.booking-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        <?php echo e(__('Si quieres, puedes reservar un nuevo turno cuando lo necesites:')); ?>

        <a href="<?php echo e(route('public.business', $booking->business)); ?>" style="color:#0f766e;"><?php echo e(url('/'.$booking->business->slug)); ?></a>
    </p>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /app/resources/views/emails/booking-cancelled.blade.php ENDPATH**/ ?>