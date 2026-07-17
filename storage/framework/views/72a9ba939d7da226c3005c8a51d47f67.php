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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Estadísticas')); ?> <?php $__env->endSlot(); ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold"><?php echo e(__('Estadísticas')); ?></h1>

        <nav class="flex rounded-lg bg-slate-200 p-0.5 text-sm dark:bg-slate-700" aria-label="<?php echo e(__('Período')); ?>">
            <?php $__currentLoopData = ['30d' => __('30 días'), 'month' => __('Este mes'), 'last_month' => __('Mes pasado')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('stats', ['period' => $key])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses(['rounded-md px-3 py-1.5', 'bg-white font-medium shadow-sm dark:bg-slate-900' => $period === $key]); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </div>

    <p class="mt-1 text-sm text-slate-500"><?php echo e($periodLabel); ?> · <?php echo e($from->isoFormat('D MMM')); ?> – <?php echo e($to->isoFormat('D MMM')); ?></p>

    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <?php $__currentLoopData = [
            [__('Turnos'), $stats['total'], null],
            [__('Asistidos'), $stats['attended'], null],
            [__('No-shows'), $stats['no_shows'], $stats['no_show_rate'].'%'],
            [__('Ocupación'), $stats['occupancy'] !== null ? $stats['occupancy'].'%' : '—', null],
            [__('Visitas a tu página'), $stats['visits'], null],
            [__('Conversión visita → turno'), $stats['conversion'] !== null ? $stats['conversion'].'%' : '—', null],
            [__('Cancelados'), $stats['cancelled'], null],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $hint]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo e($label); ?></p>
                <p class="mt-1 text-3xl font-bold tabular-nums"><?php echo e($value); ?></p>
                <?php if($hint): ?>
                    <p class="text-xs text-slate-500"><?php echo e($hint); ?> <?php echo e(__('de los turnos')); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php ($max = max(1, max($stats['per_day']))); ?>
    <section class="mt-6 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800" aria-label="<?php echo e(__('Turnos por día')); ?>">
        <h2 class="font-semibold"><?php echo e(__('Turnos por día')); ?></h2>

        <div class="mt-4 flex h-36 items-end gap-0.5" role="img"
             aria-label="<?php echo e(__('Gráfico de barras: turnos por día, máximo :max', ['max' => $max])); ?>">
            <?php $__currentLoopData = $stats['per_day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group relative flex h-full flex-1 flex-col justify-end"
                     title="<?php echo e(\Carbon\CarbonImmutable::parse($date)->isoFormat('ddd D MMM')); ?>: <?php echo e(trans_choice(':count turno|:count turnos', $count)); ?>">
                    <div class="w-full rounded-t bg-brand-600 dark:bg-brand-400"
                         style="height: <?php echo e($count === 0 ? '2px' : round($count * 100 / $max) .'%'); ?>; <?php echo e($count === 0 ? 'opacity:.25' : ''); ?>"></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-1 flex justify-between text-xs text-slate-500">
            <span><?php echo e($from->isoFormat('D MMM')); ?></span>
            <span><?php echo e($to->isoFormat('D MMM')); ?></span>
        </div>

        <details class="mt-3">
            <summary class="cursor-pointer text-xs text-slate-500"><?php echo e(__('Ver como tabla')); ?></summary>
            <div class="mt-2 max-h-48 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead><tr><th class="py-1 pr-4 font-medium"><?php echo e(__('Fecha')); ?></th><th class="py-1 font-medium"><?php echo e(__('Turnos')); ?></th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $stats['per_day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td class="py-0.5 pr-4"><?php echo e($date); ?></td><td class="py-0.5 tabular-nums"><?php echo e($count); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </details>
    </section>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <?php $__currentLoopData = [[__('Servicios más reservados'), $stats['top_services']], [__('Profesionales más reservados'), $stats['top_professionals']]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $items]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                <h2 class="font-semibold"><?php echo e($title); ?></h2>
                <?php if($items->isEmpty()): ?>
                    <p class="mt-2 text-sm text-slate-500"><?php echo e(__('Sin datos en este período.')); ?></p>
                <?php else: ?>
                    <ul class="mt-2 space-y-2 text-sm">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-2">
                                <span class="w-32 truncate"><?php echo e($name); ?></span>
                                <span class="h-2 rounded-full bg-brand-600 dark:bg-brand-400"
                                      style="width: <?php echo e(round($count * 100 / max(1, $items->max()))); ?>%"></span>
                                <span class="tabular-nums text-slate-500"><?php echo e($count); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /app/resources/views/app/stats.blade.php ENDPATH**/ ?>