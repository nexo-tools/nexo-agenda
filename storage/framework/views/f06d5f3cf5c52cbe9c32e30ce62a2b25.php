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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('Check-in')); ?> <?php $__env->endSlot(); ?>

    <?php ($local = $booking->starts_at->setTimezone($booking->business->timezone)); ?>

    <div class="mx-auto max-w-md rounded-2xl bg-white p-6 text-center shadow-sm dark:bg-slate-800">
        <h1 class="text-xl font-bold"><?php echo e(__('Check-in')); ?></h1>

        <p class="mt-4 text-2xl font-bold"><?php echo e($booking->client_name); ?></p>
        <p class="mt-1 text-slate-600 dark:text-slate-400">
            <?php echo e($booking->service->name); ?> · <?php echo e($booking->professional->name); ?>

        </p>
        <p class="capitalize text-slate-600 dark:text-slate-400">
            <?php echo e($local->isoFormat('dddd D MMM')); ?> · <?php echo e($local->format('H:i')); ?>

        </p>
        <p class="mt-2">
            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'rounded px-2 py-1 text-sm',
                'bg-brand-100 text-brand-900' => $booking->status === \App\Enums\BookingStatus::Confirmed,
                'bg-emerald-100 text-emerald-900' => $booking->status === \App\Enums\BookingStatus::Attended,
                'bg-red-100 text-red-900' => $booking->status === \App\Enums\BookingStatus::NoShow,
                'bg-slate-200 text-slate-600' => $booking->status === \App\Enums\BookingStatus::Cancelled,
            ]); ?>">
                <?php echo e($booking->status->label()); ?>

            </span>
        </p>

        <?php if($booking->status === \App\Enums\BookingStatus::Confirmed): ?>
            <form method="POST" action="<?php echo e(route('checkin.store', $token)); ?>" class="mt-6">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>✓ <?php echo e(__('Marcar como Asistió')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </form>
        <?php endif; ?>

        <a href="<?php echo e(route('dashboard', ['date' => $local->toDateString()])); ?>"
           class="mt-4 inline-block text-sm text-brand-700 hover:underline dark:text-brand-400">
            <?php echo e(__('Ir a la agenda')); ?>

        </a>
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
<?php /**PATH /app/resources/views/app/checkin.blade.php ENDPATH**/ ?>