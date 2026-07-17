<?php ($local = $booking->starts_at->setTimezone($booking->business->timezone)); ?>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdfa;border-radius:12px;margin:16px 0;">
    <tr>
        <td style="padding:16px 20px;font-size:14px;color:#0f172a;line-height:1.7;">
            <strong><?php echo e($booking->service->name); ?></strong><br>
            <span style="text-transform:capitalize;"><?php echo e($local->isoFormat('dddd D [de] MMMM YYYY')); ?></span> · <?php echo e($local->format('H:i')); ?><br>
            <?php echo e(__('Con :name', ['name' => $booking->professional->name])); ?>

            <?php if($booking->service->price !== null): ?>
                <br>$<?php echo e(number_format((float) $booking->service->price, 0, ',', '.')); ?>

            <?php endif; ?>
            <?php if($booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link): ?>
                <br><a href="<?php echo e($booking->service->video_link); ?>" style="color:#0f766e;"><?php echo e(__('Link de la videollamada')); ?></a>
            <?php elseif($booking->business->address): ?>
                <br><?php echo e($booking->business->address); ?>, <?php echo e($booking->business->city); ?>

            <?php endif; ?>
        </td>
    </tr>
</table>
<?php /**PATH /app/resources/views/emails/partials/booking-details.blade.php ENDPATH**/ ?>