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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Clientes')); ?> <?php $__env->endSlot(); ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold"><?php echo e(__('Clientes')); ?></h1>
        <div class="flex gap-2">
            <a href="<?php echo e(route('clients.export')); ?>"
               class="rounded-lg border border-brand-700 px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                ↓ <?php echo e(__('Clientes CSV')); ?>

            </a>
            <a href="<?php echo e(route('bookings.export')); ?>"
               class="rounded-lg border border-brand-700 px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                ↓ <?php echo e(__('Turnos CSV')); ?>

            </a>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('clients.index')); ?>" class="mt-4 max-w-sm">
        <label for="q" class="sr-only"><?php echo e(__('Buscar')); ?></label>
        <input id="q" type="search" name="q" value="<?php echo e($search); ?>" placeholder="<?php echo e(__('Buscar por nombre, email o teléfono…')); ?>"
               class="w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
    </form>

    <?php if($clients->isEmpty()): ?>
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
            <?php echo e($search ? __('Sin resultados para tu búsqueda.') : __('Tus clientes aparecerán aquí con su historial cuando tengas reservas.')); ?>

        </div>
    <?php else: ?>
        <ul class="mt-6 space-y-2">
            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(route('clients.show', ['key' => $client->key])); ?>"
                       class="flex flex-wrap items-center justify-between gap-2 rounded-2xl bg-white p-4 shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800">
                        <div>
                            <p class="font-semibold">
                                <?php echo e($client->name); ?>

                                <?php if($client->no_shows >= 2): ?>
                                    <span class="ml-1 rounded bg-red-100 px-2 py-0.5 text-xs text-red-900" title="<?php echo e(__('No asistió :count veces', ['count' => $client->no_shows])); ?>">
                                        ⚠ <?php echo e($client->no_shows); ?> <?php echo e(__('no-shows')); ?>

                                    </span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <?php echo e($client->email ?? $client->phone ?? '—'); ?>

                            </p>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            <?php echo e(trans_choice(':count turno|:count turnos', $client->total)); ?>

                            · <?php echo e(__(':count asistidos', ['count' => $client->attended])); ?>

                        </p>
                    </a>
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
<?php /**PATH /app/resources/views/app/clients/index.blade.php ENDPATH**/ ?>