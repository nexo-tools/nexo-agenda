<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(isset($meta)): ?><?php echo e($meta); ?><?php endif; ?>
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
            <?php echo e(__('Saltar al contenido')); ?>

        </a>

        <main id="contenido" class="mx-auto max-w-xl px-4 py-6">
            <?php echo e($slot); ?>

        </main>

        <footer class="mx-auto max-w-xl px-4 pb-8 text-center text-xs text-slate-400 dark:text-slate-500">
            <?php if(config('nexo.attribution_text')): ?>
                <a href="<?php echo e(config('nexo.attribution_url') ?: url('/')); ?>" class="hover:underline" rel="noopener">
                    <?php echo e(config('nexo.attribution_text')); ?>

                </a>
            <?php else: ?>
                <a href="<?php echo e(url('/')); ?>" class="hover:underline"><?php echo e(config('app.name')); ?></a>
            <?php endif; ?>
        </footer>
    </body>
</html>
<?php /**PATH /app/resources/views/components/public-layout.blade.php ENDPATH**/ ?>