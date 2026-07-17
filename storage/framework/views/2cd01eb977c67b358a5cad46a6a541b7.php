<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['code', 'title', 'message']));

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

foreach (array_filter((['code', 'title', 'message']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10 text-center">
            <a href="<?php echo e(url('/')); ?>" class="mb-8 flex items-center gap-3">
                <img src="/favicon.svg" alt="" width="40" height="40">
                <span class="text-xl font-bold tracking-tight"><?php echo e(config('app.name')); ?></span>
            </a>

            <p class="text-6xl font-bold tabular-nums text-brand-700 dark:text-brand-400"><?php echo e($code); ?></p>
            <h1 class="mt-4 text-2xl font-semibold"><?php echo e($title); ?></h1>
            <p class="mt-2 max-w-sm text-slate-600 dark:text-slate-400"><?php echo e($message); ?></p>

            <a href="<?php echo e(url('/')); ?>"
               class="mt-8 inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                <?php echo e(__('Volver al inicio')); ?>

            </a>
        </main>
    </body>
</html>
<?php /**PATH /app/resources/views/components/error-layout.blade.php ENDPATH**/ ?>