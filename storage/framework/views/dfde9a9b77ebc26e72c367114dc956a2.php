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

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold"><?php echo e(__('Agenda')); ?></h1>
            <a href="<?php echo e(route('public.business', $business)); ?>" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
                <?php echo e(url('/'.$business->slug)); ?>

            </a>
        </div>
        <a href="<?php echo e(route('bookings.create', ['date' => $day->toDateString()])); ?>"
           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            ⊕ <?php echo e(__('Turno')); ?>

        </a>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1">
            <a href="<?php echo e(route('dashboard', ['date' => $day->subDay()->toDateString(), 'view' => $view])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día anterior')); ?>">‹</a>

            <form method="GET" action="<?php echo e(route('dashboard')); ?>">
                <input type="hidden" name="view" value="<?php echo e($view); ?>">
                <label for="date" class="sr-only"><?php echo e(__('Fecha')); ?></label>
                <input type="date" id="date" name="date" value="<?php echo e($day->toDateString()); ?>" onchange="this.form.submit()"
                       class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
            </form>

            <a href="<?php echo e(route('dashboard', ['date' => $day->addDay()->toDateString(), 'view' => $view])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día siguiente')); ?>">›</a>

            <a href="<?php echo e(route('dashboard', ['view' => $view])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                <?php echo e(__('Hoy')); ?>

            </a>
        </div>

        <div class="flex rounded-lg bg-slate-200 p-0.5 text-sm dark:bg-slate-700" role="group">
            <a href="<?php echo e(route('dashboard', ['date' => $day->toDateString(), 'view' => 'day'])); ?>"
               class="<?php echo \Illuminate\Support\Arr::toCssClasses(['rounded-md px-3 py-1.5', 'bg-white font-medium shadow-sm dark:bg-slate-900' => $view === 'day']); ?>">
                <?php echo e(__('Día')); ?>

            </a>
            <a href="<?php echo e(route('dashboard', ['date' => $day->toDateString(), 'view' => 'week'])); ?>"
               class="<?php echo \Illuminate\Support\Arr::toCssClasses(['rounded-md px-3 py-1.5', 'bg-white font-medium shadow-sm dark:bg-slate-900' => $view === 'week']); ?>">
                <?php echo e(__('Semana')); ?>

            </a>
        </div>
    </div>

    <?php if($view === 'day'): ?>
        <p class="mt-4 font-medium capitalize"><?php echo e($day->isoFormat('dddd D [de] MMMM YYYY')); ?></p>

        <?php if($professionals->isEmpty()): ?>
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
                <?php echo e(__('Agrega profesionales y servicios para empezar a recibir reservas.')); ?>

            </div>
        <?php endif; ?>

        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <?php $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($items = $bookings->where('professional_id', $professional->id)); ?>
                <section class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <h2 class="font-semibold"><?php echo e($professional->name); ?></h2>

                    <?php if($items->isEmpty()): ?>
                        <p class="mt-2 text-sm text-slate-400"><?php echo e(__('Sin turnos este día.')); ?></p>
                    <?php endif; ?>

                    <ul class="mt-2 space-y-2">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'rounded-xl border p-3 text-sm',
                                'border-slate-200 dark:border-slate-700' => $booking->status !== \App\Enums\BookingStatus::Cancelled,
                                'border-slate-100 opacity-50 dark:border-slate-700' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                            ]); ?>">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold">
                                        <?php echo e($booking->starts_at->setTimezone($tz)->format('H:i')); ?>–<?php echo e($booking->ends_at->setTimezone($tz)->format('H:i')); ?>

                                    </span>
                                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'rounded px-2 py-0.5 text-xs',
                                        'bg-brand-100 text-brand-900' => $booking->status === \App\Enums\BookingStatus::Confirmed,
                                        'bg-emerald-100 text-emerald-900' => $booking->status === \App\Enums\BookingStatus::Attended,
                                        'bg-red-100 text-red-900' => $booking->status === \App\Enums\BookingStatus::NoShow,
                                        'bg-slate-200 text-slate-600' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                                    ]); ?>">
                                        <?php echo e($booking->status->label()); ?>

                                    </span>
                                </div>
                                <p class="mt-1"><?php echo e($booking->client_name); ?> · <?php echo e($booking->service->name); ?></p>
                                <?php if($booking->note): ?>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo e($booking->note); ?></p>
                                <?php endif; ?>

                                <?php if($booking->status === \App\Enums\BookingStatus::Confirmed): ?>
                                    <div class="mt-2 flex gap-1">
                                        <form method="POST" action="<?php echo e(route('bookings.status', $booking)); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="attended">
                                            <button class="rounded-lg px-2 py-1 text-xs text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-slate-700">
                                                ✓ <?php echo e(__('Asistió')); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('bookings.status', $booking)); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="no_show">
                                            <button class="rounded-lg px-2 py-1 text-xs text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                                ✗ <?php echo e(__('No vino')); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('bookings.status', $booking)); ?>"
                                              onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from(__('¿Cancelar este turno?'))->toHtml() ?>)">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="cancelled">
                                            <button class="rounded-lg px-2 py-1 text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <?php echo e(__('Cancelar')); ?>

                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <?php for($i = 0; $i < 7; $i++): ?>
                <?php ($weekDay = $weekStart->addDays($i)); ?>
                <?php ($items = $bookings->filter(fn ($b) => $b->starts_at->setTimezone($tz)->isSameDay($weekDay))); ?>
                <a href="<?php echo e(route('dashboard', ['date' => $weekDay->toDateString(), 'view' => 'day'])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'rounded-2xl bg-white p-4 shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800',
                       'ring-2 ring-brand-300' => $weekDay->isSameDay($day),
                   ]); ?>">
                    <p class="text-sm font-semibold capitalize"><?php echo e($weekDay->isoFormat('dddd D')); ?></p>
                    <p class="text-xs text-slate-500">
                        <?php echo e(trans_choice('{0}Sin turnos|{1}:count turno|[2,*]:count turnos', $items->where('status', '!=', \App\Enums\BookingStatus::Cancelled)->count())); ?>

                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-slate-600 dark:text-slate-400">
                        <?php $__currentLoopData = $items->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($booking->starts_at->setTimezone($tz)->format('H:i')); ?> <?php echo e($booking->client_name); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </a>
            <?php endfor; ?>
        </div>
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
<?php /**PATH /app/resources/views/app/dashboard.blade.php ENDPATH**/ ?>