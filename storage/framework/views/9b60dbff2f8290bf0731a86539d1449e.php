<?php ($local = $booking->starts_at->setTimezone($booking->business->timezone)); ?>

<?php $__env->startComponent('emails.layout', ['businessName' => $booking->business->name]); ?>
    <h1 style="font-size:20px;margin:0 0 8px;"><?php echo e(__('¡Se liberó un horario!')); ?></h1>
    <p style="margin:0;">
        <?php echo e(__('Hola :name, estabas en la lista de espera de :service para el :date y se acaba de liberar un lugar (:time).', [
            'name' => $entry->client_name,
            'service' => $booking->service->name,
            'date' => $local->isoFormat('dddd D [de] MMMM'),
            'time' => $local->format('H:i'),
        ])); ?>

    </p>

    <a href="<?php echo e(route('public.times', [$booking->business, $booking->service, 'professional' => $entry->professional_id ?? 'any', 'date' => $local->toDateString()])); ?>"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;margin-top:16px;">
        <?php echo e(__('Reservar ahora')); ?>

    </a>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        <?php echo e(__('Los lugares se asignan por orden de llegada — si el horario ya no está, elige otro desde el mismo enlace.')); ?>

    </p>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/html/resources/views/emails/waitlist-slot-freed.blade.php ENDPATH**/ ?>