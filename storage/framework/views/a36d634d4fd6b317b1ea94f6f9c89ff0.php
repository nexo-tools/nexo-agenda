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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Equipo')); ?> <?php $__env->endSlot(); ?>

    <h1 class="text-2xl font-bold"><?php echo e(__('Equipo')); ?></h1>

    <form method="POST" action="<?php echo e(route('professionals.store')); ?>" class="mt-4 flex max-w-md gap-2">
        <?php echo csrf_field(); ?>
        <div class="flex-1">
            <label for="name" class="sr-only"><?php echo e(__('Nombre del profesional')); ?></label>
            <input id="name" name="name" required placeholder="<?php echo e(__('Nombre del profesional')); ?>"
                   class="w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
            <?php $__errorArgs = ['name'];
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
        <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            <?php echo e(__('Agregar')); ?>

        </button>
    </form>

    <?php if($professionals->isEmpty()): ?>
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
            <?php echo e(__('Agrega a las personas que atienden turnos. Si trabajas en solitario, agrégate a ti.')); ?>

        </div>
    <?php else: ?>
        <ul class="mt-6 space-y-3">
            <?php $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div>
                        <p class="font-semibold">
                            <?php echo e($professional->name); ?>

                            <?php if (! ($professional->is_active)): ?>
                                <span class="ml-1 rounded bg-slate-200 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-300"><?php echo e(__('Inactivo')); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            <?php if($professional->schedule_blocks_count > 0): ?>
                                <?php echo e(trans_choice(':count franja horaria|:count franjas horarias', $professional->schedule_blocks_count)); ?>

                            <?php else: ?>
                                <?php echo e(__('Sin horarios definidos aún')); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('professionals.edit', $professional)); ?>"
                           class="rounded-lg px-3 py-1.5 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                            <?php echo e(__('Horarios y datos')); ?>

                        </a>
                        <form method="POST" action="<?php echo e(route('professionals.destroy', $professional)); ?>"
                              onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from(__('¿Eliminar este profesional?'))->toHtml() ?>)">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                <?php echo e(__('Eliminar')); ?>

                            </button>
                        </form>
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
<?php /**PATH /var/www/html/resources/views/app/professionals/index.blade.php ENDPATH**/ ?>