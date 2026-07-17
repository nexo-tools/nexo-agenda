<?php ($local = $booking->starts_at->setTimezone($booking->business->timezone)); ?>

<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => __('Tu turno').' — '.$booking->business->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Tu turno').' — '.$booking->business->name)]); ?>
    <?php if(session('status')): ?>
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status"><?php echo e(session('status')); ?></p>
    <?php endif; ?>

    <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-slate-800">
        <p class="text-sm text-slate-500"><?php echo e(__('Tu turno en')); ?></p>
        <h1 class="text-xl font-bold"><?php echo e($booking->business->name); ?></h1>

        <dl class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Servicio')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->service->name); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Fecha')); ?></dt>
                <dd class="font-medium capitalize"><?php echo e($local->isoFormat('dddd D [de] MMMM YYYY')); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Hora')); ?></dt>
                <dd class="font-medium"><?php echo e($local->format('H:i')); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Con')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->professional->name); ?></dd>
            </div>
            <?php if($booking->service->price !== null): ?>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500"><?php echo e(__('Precio')); ?></dt>
                    <dd class="font-medium">$<?php echo e(number_format((float) $booking->service->price, 0, ',', '.')); ?></dd>
                </div>
            <?php endif; ?>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Estado')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->status->label()); ?></dd>
            </div>
        </dl>

        <?php if($booking->status === \App\Enums\BookingStatus::Confirmed && $booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link): ?>
            <a href="<?php echo e($booking->service->video_link); ?>" rel="noopener"
               class="mt-4 block rounded-lg bg-brand-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-brand-800">
                <?php echo e(__('Unirse a la videollamada')); ?>

            </a>
        <?php endif; ?>

        <?php if($booking->business->address): ?>
            <p class="mt-4 text-sm text-slate-600 dark:text-slate-400">⌂ <?php echo e($booking->business->address); ?></p>
        <?php endif; ?>
    </div>

    <p class="mt-4 text-center text-xs text-slate-500">
        <?php echo e(__('Guarda este enlace: es tu comprobante y desde aquí gestionas tu turno.')); ?>

    </p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $attributes = $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $component = $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php /**PATH /app/resources/views/public/booking/manage.blade.php ENDPATH**/ ?>