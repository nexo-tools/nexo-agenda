<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['business' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['business' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(isset($meta)): ?><?php echo e($meta); ?><?php endif; ?>

        <?php if($business?->brand_color): ?>
            <style>
                .bg-brand-700 { background-color: <?php echo e($business->brand_color); ?> !important; color: <?php echo e($business->accentTextColor()); ?> !important; }
                .hover\:bg-brand-800:hover { background-color: <?php echo e($business->brand_color); ?> !important; filter: brightness(0.9); }
                .text-brand-700 { color: <?php echo e($business->brand_color); ?> !important; }
                .dark .dark\:text-brand-400 { color: <?php echo e($business->brand_color); ?> !important; }
            </style>
        <?php endif; ?>
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
<?php /**PATH /var/www/html/resources/views/components/public-layout.blade.php ENDPATH**/ ?>