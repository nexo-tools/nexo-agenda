<nav <?php echo e($attributes->class(['flex items-center gap-1 text-xs'])); ?> aria-label="<?php echo e(__('Idioma')); ?>">
    <?php $__currentLoopData = ['es' => 'Español', 'en' => 'English', 'pt' => 'Português']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['lang' => $locale])); ?>"
           class="<?php echo \Illuminate\Support\Arr::toCssClasses([
               'rounded px-2 py-1 uppercase',
               'bg-brand-100 font-semibold text-brand-900 dark:bg-brand-900 dark:text-brand-100' => app()->getLocale() === $locale,
               'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' => app()->getLocale() !== $locale,
           ]); ?>"
           lang="<?php echo e($locale); ?>" hreflang="<?php echo e($locale); ?>" aria-label="<?php echo e($label); ?>"
           <?php if(app()->getLocale() === $locale): ?> aria-current="true" <?php endif; ?>>
            <?php echo e($locale); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH /app/resources/views/components/locale-switcher.blade.php ENDPATH**/ ?>