<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['active' => false]));

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

foreach (array_filter((['active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<a <?php echo e($attributes->class([
    'rounded-lg px-3 py-2 text-sm',
    'bg-brand-100 font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100' => $active,
    'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700' => ! $active,
])); ?> <?php if($active): ?> aria-current="page" <?php endif; ?>>
    <?php echo e($slot); ?>

</a>
<?php /**PATH /var/www/html/resources/views/components/nav-link.blade.php ENDPATH**/ ?>