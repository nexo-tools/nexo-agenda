<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => __('¿Cuándo?').' — '.$business->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('¿Cuándo?').' — '.$business->name)]); ?>
    <a href="<?php echo e(route('public.professional', [$business, $service])); ?>" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← <?php echo e(__('Cambiar profesional')); ?>

    </a>
    <p class="mt-3 text-sm text-slate-500"><?php echo e(__('Paso 3 de 4')); ?></p>
    <h1 class="mb-1 text-xl font-bold"><?php echo e(__('¿Cuándo?')); ?></h1>
    <p class="mb-5 text-sm text-slate-600 dark:text-slate-400">
        <?php echo e($service->name); ?> · <?php echo e($chosen?->name ?? __('Cualquier profesional')); ?>

    </p>

    <?php if(session('slot_taken')): ?>
        <p class="mb-4 rounded-lg bg-amber-100 px-4 py-3 text-sm text-amber-900" role="alert">
            <?php echo e(__('Ese horario acaba de ocuparse. Elige otro, por favor.')); ?>

        </p>
    <?php endif; ?>

    <div class="mb-4 flex items-center justify-between gap-2">
        <?php if($canGoBack): ?>
            <a href="<?php echo e(route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->subDay()->toDateString()])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día anterior')); ?>">‹</a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">‹</span>
        <?php endif; ?>

        <form method="GET" action="<?php echo e(route('public.times', [$business, $service])); ?>">
            <input type="hidden" name="professional" value="<?php echo e($chosen?->id ?? 'any'); ?>">
            <label for="date" class="sr-only"><?php echo e(__('Fecha')); ?></label>
            <input type="date" id="date" name="date" value="<?php echo e($day->toDateString()); ?>" onchange="this.form.submit()"
                   class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
        </form>

        <?php if($canGoForward): ?>
            <a href="<?php echo e(route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->addDay()->toDateString()])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día siguiente')); ?>">›</a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">›</span>
        <?php endif; ?>
    </div>

    <p class="mb-3 text-center font-medium capitalize"><?php echo e($day->isoFormat('dddd D [de] MMMM')); ?></p>

    <?php if($slots->isEmpty()): ?>
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
            <?php echo e(__('No hay horarios disponibles este día.')); ?>

        </p>
    <?php else: ?>
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-4">
            <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time => $professionalId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(route('public.form', [$business, $service, 'professional' => $professionalId, 'start' => $day->toDateString().' '.$time])); ?>"
                       class="block rounded-lg bg-white py-2 text-center text-sm font-medium shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800">
                        <?php echo e($time); ?>

                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
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
<?php /**PATH /app/resources/views/public/booking/times.blade.php ENDPATH**/ ?>