<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
            <?php echo e(__('Saltar al contenido')); ?>

        </a>
        <main id="contenido" class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="<?php echo e(route('home')); ?>" class="mb-6 flex items-center gap-3">
                <img src="/favicon.svg" alt="" width="44" height="44">
                <span class="text-2xl font-bold tracking-tight"><?php echo e(config('app.name')); ?></span>
            </a>
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8 dark:bg-slate-800">
                <?php echo e($slot); ?>

            </div>
            <?php if (isset($component)) { $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.locale-switcher','data' => ['class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('locale-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $attributes = $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $component = $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
        </main>
    </body>
</html>
<?php /**PATH /app/resources/views/components/guest-layout.blade.php ENDPATH**/ ?>