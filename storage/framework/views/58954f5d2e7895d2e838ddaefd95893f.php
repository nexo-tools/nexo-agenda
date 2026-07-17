<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'autocomplete' => null]));

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

foreach (array_filter((['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'autocomplete' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <label for="<?php echo e($name); ?>" class="mb-1 block text-sm font-medium"><?php echo e($label); ?></label>
    <input id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" type="<?php echo e($type); ?>"
           value="<?php echo e($type !== 'password' ? old($name, $value) : ''); ?>"
           <?php if($required): ?> required <?php endif; ?>
           <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?>
           <?php echo e($attributes->class([
               'w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200',
           ])); ?>>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /var/www/html/resources/views/components/field.blade.php ENDPATH**/ ?>