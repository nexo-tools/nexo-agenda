<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', ['title' => __('Mostrador')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <meta http-equiv="refresh" content="60">
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
        <header class="flex items-center justify-between px-4 py-3 text-sm text-slate-500">
            <a href="<?php echo e(route('dashboard')); ?>" class="rounded-lg px-3 py-1.5 hover:bg-slate-800">← <?php echo e(__('Agenda')); ?></a>
            <span class="font-semibold text-slate-200"><?php echo e($business->name); ?></span>
            <span class="tabular-nums"><?php echo e($now->format('H:i')); ?> · <?php echo e(__('se actualiza solo')); ?></span>
        </header>

        <main class="grid gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            <?php $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($items = $bookings->where('professional_id', $professional->id)->filter(fn ($b) => $b->status !== \App\Enums\BookingStatus::Cancelled)); ?>
                <?php ($next = $items->first(fn ($b) => $b->status === \App\Enums\BookingStatus::Confirmed && $b->ends_at->gte($now))); ?>
                <section class="rounded-2xl bg-slate-900 p-4">
                    <h2 class="text-lg font-bold"><?php echo e($professional->name); ?></h2>

                    <?php if($items->isEmpty()): ?>
                        <p class="mt-3 text-slate-500"><?php echo e(__('Sin turnos hoy.')); ?></p>
                    <?php endif; ?>

                    <ul class="mt-3 space-y-3">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'rounded-xl p-4',
                                'bg-brand-900 ring-2 ring-brand-400' => $next && $booking->is($next),
                                'bg-slate-800' => ! $next || ! $booking->is($next),
                                'opacity-60' => $booking->status !== \App\Enums\BookingStatus::Confirmed,
                            ]); ?>">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-2xl font-bold tabular-nums"><?php echo e($booking->starts_at->setTimezone($tz)->format('H:i')); ?></span>
                                    <?php if($booking->status !== \App\Enums\BookingStatus::Confirmed): ?>
                                        <span class="rounded bg-slate-700 px-2 py-1 text-xs"><?php echo e($booking->status->label()); ?></span>
                                    <?php elseif($next && $booking->is($next)): ?>
                                        <span class="rounded bg-brand-400 px-2 py-1 text-xs font-bold text-slate-900"><?php echo e(__('Próximo')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-1 text-lg"><?php echo e($booking->client_name); ?></p>
                                <p class="text-sm text-slate-500"><?php echo e($booking->service->name); ?></p>

                                <?php if($booking->status === \App\Enums\BookingStatus::Confirmed): ?>
                                    <div class="mt-3 flex gap-2">
                                        <form method="POST" action="<?php echo e(route('bookings.status', $booking)); ?>" class="flex-1">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="attended">
                                            <button class="w-full rounded-lg bg-emerald-600 px-3 py-2.5 text-sm font-bold hover:bg-emerald-500">
                                                ✓ <?php echo e(__('Asistió')); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('bookings.status', $booking)); ?>" class="flex-1">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="no_show">
                                            <button class="w-full rounded-lg bg-red-600/80 px-3 py-2.5 text-sm font-bold hover:bg-red-500">
                                                ✗ <?php echo e(__('No vino')); ?>

                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </main>
    </body>
</html>
<?php /**PATH /app/resources/views/app/frontdesk.blade.php ENDPATH**/ ?>