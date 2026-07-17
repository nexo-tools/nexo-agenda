<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Agenda')); ?> <?php $__env->endSlot(); ?>

    <h1 class="text-2xl font-bold"><?php echo e($business->name); ?></h1>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        <?php echo e(__('Tu página pública')); ?>:
        <a href="<?php echo e(url('/'.$business->slug)); ?>" class="font-medium text-brand-700 hover:underline dark:text-brand-400">
            <?php echo e(url('/'.$business->slug)); ?>

        </a>
    </p>

    <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
        <?php echo e(__('Aquí vivirá tu agenda. Próximo paso: crea tus servicios.')); ?>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/app/dashboard.blade.php ENDPATH**/ ?>