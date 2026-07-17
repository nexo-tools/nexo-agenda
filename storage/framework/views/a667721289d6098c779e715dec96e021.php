<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => $business->name,'business' => $business]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($business->name),'business' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($business)]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <meta name="description" content="<?php echo e(__('Reserva tu turno en :name', ['name' => $business->name])); ?>">
     <?php $__env->endSlot(); ?>

    <header class="mb-6">
        <?php if($business->logo_path): ?>
            <img src="<?php echo e(Storage::url($business->logo_path)); ?>" alt="" class="mb-3 h-16 w-16 rounded-2xl object-contain">
        <?php endif; ?>
        <h1 class="text-2xl font-bold"><?php echo e($business->name); ?></h1>
        <p class="text-sm text-slate-600 dark:text-slate-400">
            <?php echo e(__('nexo.categories.'.$business->category)); ?> · <?php echo e($business->city); ?>

            <?php if($ratingCount > 0): ?>
                · <span aria-hidden="true" class="text-amber-500">★</span>
                <?php echo e(number_format($ratingAverage, 1, ',')); ?>

                <span class="text-slate-500">(<?php echo e($ratingCount); ?>)</span>
            <?php endif; ?>
        </p>
        <?php if($business->description): ?>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300"><?php echo e($business->description); ?></p>
        <?php endif; ?>
    </header>

    <h2 class="mb-3 font-semibold"><?php echo e(__('Reserva tu turno')); ?></h2>

    <?php if($services->isEmpty()): ?>
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
            <?php echo e(__('Este negocio todavía no tiene servicios disponibles para reservar.')); ?>

        </p>
    <?php else: ?>
        <ul class="space-y-3">
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                <?php echo e($service->name); ?>

                                <?php if($service->mode === \App\Enums\ServiceMode::Virtual): ?>
                                    <span class="ml-1 rounded bg-brand-100 px-2 py-0.5 text-xs text-brand-900 dark:bg-brand-900 dark:text-brand-100"><?php echo e(__('Virtual')); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <?php echo e($service->duration_minutes); ?> min
                                <?php if($service->price !== null): ?>
                                    · $<?php echo e(number_format((float) $service->price, 0, ',', '.')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="<?php echo e(route('public.professional', [$business, $service])); ?>"
                           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                            <?php echo e(__('Reservar')); ?>

                        </a>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>

    <?php if($reviews->isNotEmpty()): ?>
        <section class="mt-8">
            <h2 class="mb-3 font-semibold"><?php echo e(__('Reseñas')); ?></h2>
            <ul class="space-y-3">
                <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="rounded-2xl bg-white p-4 text-sm shadow-sm dark:bg-slate-800">
                        <p>
                            <span aria-hidden="true" class="text-amber-500"><?php echo e(str_repeat('★', $review->rating)); ?></span>
                            <span class="sr-only"><?php echo e(trans_choice(':count estrella|:count estrellas', $review->rating)); ?></span>
                            <span class="ml-1 font-medium"><?php echo e($review->client_name); ?></span>
                        </p>
                        <p class="mt-1 text-slate-600 dark:text-slate-400"><?php echo e($review->comment); ?></p>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </section>
    <?php endif; ?>

    <div class="mt-8 space-y-1 text-sm text-slate-600 dark:text-slate-400">
        <?php if($business->address): ?>
            <p>⌂ <?php echo e($business->address); ?></p>
        <?php endif; ?>
        <?php if($business->whatsapp_phone): ?>
            <p>
                <a href="https://wa.me/<?php echo e(preg_replace('/\D/', '', $business->whatsapp_phone)); ?>"
                   class="text-brand-700 hover:underline dark:text-brand-400" rel="noopener">
                    ✆ WhatsApp
                </a>
            </p>
        <?php endif; ?>
    </div>
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
<?php /**PATH /var/www/html/resources/views/public/business.blade.php ENDPATH**/ ?>