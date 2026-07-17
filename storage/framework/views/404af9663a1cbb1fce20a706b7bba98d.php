<?php ($local = $booking->starts_at->setTimezone($booking->business->timezone)); ?>

<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => __('Tu turno').' — '.$booking->business->name,'business' => $booking->business]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Tu turno').' — '.$booking->business->name),'business' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking->business)]); ?>
    <?php if(session('status')): ?>
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status"><?php echo e(session('status')); ?></p>
    <?php endif; ?>

    <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-slate-800">
        <p class="text-sm text-slate-500"><?php echo e(__('Tu turno en')); ?></p>
        <h1 class="text-xl font-bold"><?php echo e($booking->business->name); ?></h1>

        <dl class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Servicio')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->service->name); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Fecha')); ?></dt>
                <dd class="font-medium capitalize"><?php echo e($local->isoFormat('dddd D [de] MMMM YYYY')); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Hora')); ?></dt>
                <dd class="font-medium"><?php echo e($local->format('H:i')); ?></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Con')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->professional->name); ?></dd>
            </div>
            <?php if($booking->service->price !== null): ?>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500"><?php echo e(__('Precio')); ?></dt>
                    <dd class="font-medium">$<?php echo e(number_format((float) $booking->service->price, 0, ',', '.')); ?></dd>
                </div>
            <?php endif; ?>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500"><?php echo e(__('Estado')); ?></dt>
                <dd class="font-medium"><?php echo e($booking->status->label()); ?></dd>
            </div>
        </dl>

        <?php if($booking->status === \App\Enums\BookingStatus::Confirmed && $booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link): ?>
            <a href="<?php echo e($booking->service->video_link); ?>" rel="noopener"
               class="mt-4 block rounded-lg bg-brand-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-brand-800">
                <?php echo e(__('Unirse a la videollamada')); ?>

            </a>
        <?php endif; ?>

        <?php if($booking->business->address): ?>
            <p class="mt-4 text-sm text-slate-600 dark:text-slate-400">⌂ <?php echo e($booking->business->address); ?></p>
        <?php endif; ?>

        <?php if($booking->status === \App\Enums\BookingStatus::Confirmed): ?>
            <div class="mt-5 border-t border-slate-200 pt-5 text-center dark:border-slate-700">
                <div class="mx-auto inline-block rounded-xl bg-white p-2">
                    <?php echo app(\App\Services\QrSvg::class)->forUrl(route('checkin', $token), 180); ?>

                </div>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Muestra este código al llegar para hacer el check-in.')); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($booking->clientCanManage()): ?>
        <div class="mt-4 flex gap-3">
            <a href="<?php echo e(route('booking.reschedule', $token)); ?>"
               class="flex-1 rounded-lg border border-brand-700 px-4 py-2 text-center text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                <?php echo e(__('Reprogramar')); ?>

            </a>
            <form method="POST" action="<?php echo e(route('booking.cancel', $token)); ?>" class="flex-1"
                  onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from(__('¿Cancelar tu turno?'))->toHtml() ?>)">
                <?php echo csrf_field(); ?>
                <button class="w-full rounded-lg border border-red-600 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-slate-800">
                    <?php echo e(__('Cancelar turno')); ?>

                </button>
            </form>
        </div>
        <p class="mt-2 text-center text-xs text-slate-500">
            <?php echo e(__('Puedes cancelar o reprogramar hasta :hours h antes.', ['hours' => $booking->service->cancellation_hours])); ?>

        </p>
    <?php elseif($booking->status === \App\Enums\BookingStatus::Confirmed): ?>
        <p class="mt-4 text-center text-xs text-slate-500">
            <?php echo e(__('El plazo para cancelar o reprogramar en línea ya pasó. Contacta al negocio.')); ?>

        </p>
    <?php endif; ?>

    <?php if($booking->canBeReviewed()): ?>
        <section class="mt-6 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-800">
            <h2 class="font-semibold"><?php echo e(__('¿Cómo estuvo tu experiencia?')); ?></h2>
            <form method="POST" action="<?php echo e(route('booking.review', $token)); ?>" class="mt-3 space-y-3">
                <?php echo csrf_field(); ?>
                <fieldset>
                    <legend class="sr-only"><?php echo e(__('Calificación')); ?></legend>
                    <div class="rating gap-1 text-3xl">
                        <?php $__currentLoopData = [5, 4, 3, 2, 1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stars): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label>
                                <input type="radio" name="rating" value="<?php echo e($stars); ?>" class="sr-only" <?php if(old('rating') == $stars): echo 'checked'; endif; ?> required>
                                <span aria-hidden="true">★</span>
                                <span class="sr-only"><?php echo e(trans_choice(':count estrella|:count estrellas', $stars)); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </fieldset>

                <div>
                    <label for="comment" class="mb-1 block text-sm font-medium"><?php echo e(__('Comentario (opcional)')); ?></label>
                    <textarea id="comment" name="comment" rows="3" maxlength="500"
                              class="w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200"><?php echo e(old('comment')); ?></textarea>
                    <?php $__errorArgs = ['comment'];
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

                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e(__('Enviar reseña')); ?> <?php echo $__env->renderComponent(); ?>
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
        </section>
    <?php elseif($booking->review): ?>
        <p class="mt-4 rounded-2xl bg-white p-4 text-center text-sm text-slate-600 shadow-sm dark:bg-slate-800 dark:text-slate-400">
            <?php echo e(__('Calificaste esta visita con :rating de 5. ¡Gracias!', ['rating' => $booking->review->rating])); ?>

        </p>
    <?php endif; ?>

    <p class="mt-4 text-center text-xs text-slate-500">
        <?php echo e(__('Guarda este enlace: es tu comprobante y desde aquí gestionas tu turno.')); ?>

    </p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $attributes = $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b)): ?>
<?php $component = $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b; ?>
<?php unset($__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b); ?>
<?php endif; ?>
<?php /**PATH /app/resources/views/public/booking/manage.blade.php ENDPATH**/ ?>