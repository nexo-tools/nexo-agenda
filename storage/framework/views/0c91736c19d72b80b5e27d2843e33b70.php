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
     <?php $__env->slot('title', null, []); ?> <?php echo e($client->client_name); ?> <?php $__env->endSlot(); ?>

    <a href="<?php echo e(route('clients.index')); ?>" class="text-sm text-brand-700 hover:underline dark:text-brand-400">← <?php echo e(__('Clientes')); ?></a>
    <h1 class="mt-2 text-2xl font-bold"><?php echo e($client->client_name); ?></h1>
    <p class="text-sm text-slate-600 dark:text-slate-400">
        <?php echo e($client->client_email ?? ''); ?>

        <?php if($client->client_phone): ?>
            · <?php echo e($client->client_phone); ?>

            <a href="https://wa.me/<?php echo e(preg_replace('/\D/', '', $client->client_phone)); ?>"
               class="text-brand-700 hover:underline dark:text-brand-400" rel="noopener" target="_blank">✆ WhatsApp</a>
        <?php endif; ?>
    </p>

    <h2 class="mt-6 font-semibold"><?php echo e(__('Historial')); ?></h2>
    <ul class="mt-2 space-y-2">
        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-white px-4 py-3 text-sm shadow-sm dark:bg-slate-800">
                <span class="capitalize">
                    <?php echo e($booking->starts_at->setTimezone($tz)->isoFormat('ddd D MMM YYYY · HH:mm')); ?>

                    · <?php echo e($booking->service->name); ?> · <?php echo e($booking->professional->name); ?>

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
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
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
<?php /**PATH /app/resources/views/app/clients/show.blade.php ENDPATH**/ ?>