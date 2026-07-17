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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Servicios')); ?> <?php $__env->endSlot(); ?>

    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold"><?php echo e(__('Servicios')); ?></h1>
        <a href="<?php echo e(route('services.create')); ?>"
           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            <?php echo e(__('Nuevo servicio')); ?>

        </a>
    </div>

    <?php if($services->isEmpty()): ?>
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
            <?php echo e(__('Todavía no tienes servicios. Crea el primero para empezar a recibir reservas.')); ?>

        </div>
    <?php else: ?>
        <ul class="mt-6 space-y-3">
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                <?php echo e($service->name); ?>

                                <?php if (! ($service->is_active)): ?>
                                    <span class="ml-1 rounded bg-slate-200 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-300"><?php echo e(__('Inactivo')); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <?php echo e($service->duration_minutes); ?> min
                                · <?php echo e($service->mode->label()); ?>

                                <?php if($service->price !== null): ?>
                                    · $<?php echo e(number_format((float) $service->price, 0, ',', '.')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('services.edit', $service)); ?>"
                               class="rounded-lg px-3 py-1.5 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                                <?php echo e(__('Editar')); ?>

                            </a>
                            <form method="POST" action="<?php echo e(route('services.destroy', $service)); ?>"
                                  onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from(__('¿Eliminar este servicio?'))->toHtml() ?>)">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                    <?php echo e(__('Eliminar')); ?>

                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
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
<?php /**PATH /app/resources/views/app/services/index.blade.php ENDPATH**/ ?>