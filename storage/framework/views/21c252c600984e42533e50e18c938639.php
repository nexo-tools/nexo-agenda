<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => $category ? __('nexo.categories.'.$category).' — '.__('Explorar') : __('Explorar negocios')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category ? __('nexo.categories.'.$category).' — '.__('Explorar') : __('Explorar negocios'))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <meta name="description" content="<?php echo e($category
            ? __('Encuentra dónde reservar en :category', ['category' => __('nexo.categories.'.$category)])
            : __('Encuentra dónde reservar tu próximo turno')); ?>">
     <?php $__env->endSlot(); ?>

    <header class="mb-5">
        <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 text-sm font-semibold">
            <img src="/favicon.svg" alt="" width="24" height="24"> <?php echo e(config('app.name')); ?>

        </a>
        <h1 class="mt-3 text-2xl font-bold">
            <?php echo e($category ? __('nexo.categories.'.$category) : __('Explorar negocios')); ?>

        </h1>
        <p class="text-sm text-slate-600 dark:text-slate-400"><?php echo e(__('Encuentra dónde reservar tu próximo turno')); ?></p>
    </header>

    <form method="GET" action="<?php echo e(route('directory')); ?>" class="mb-6 space-y-2">
        <label for="q" class="sr-only"><?php echo e(__('Buscar')); ?></label>
        <input id="q" type="search" name="q" value="<?php echo e($search); ?>" placeholder="<?php echo e(__('Buscar por nombre…')); ?>"
               class="w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">

        <div class="flex gap-2">
            <label for="categoria" class="sr-only"><?php echo e(__('Rubro')); ?></label>
            <select id="categoria" name="categoria" onchange="this.form.submit()"
                    class="flex-1 rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                <option value=""><?php echo e(__('Todos los rubros')); ?></option>
                <?php $__currentLoopData = config('nexo.categories'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($option); ?>" <?php if($category === $option): echo 'selected'; endif; ?>><?php echo e(__('nexo.categories.'.$option)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <label for="ciudad" class="sr-only"><?php echo e(__('Ciudad')); ?></label>
            <select id="ciudad" name="ciudad" onchange="this.form.submit()"
                    class="flex-1 rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                <option value=""><?php echo e(__('Todas las ciudades')); ?></option>
                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($option); ?>" <?php if($city === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <noscript><button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white"><?php echo e(__('Filtrar')); ?></button></noscript>
    </form>

    <?php if($businesses->isEmpty()): ?>
        <p class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700">
            <?php echo e(__('No encontramos negocios con esos filtros.')); ?>

        </p>
    <?php else: ?>
        <ul class="space-y-3">
            <?php $__currentLoopData = $businesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $business): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(route('public.business', $business)); ?>"
                       class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800">
                        <?php if($business->logo_path): ?>
                            <img src="<?php echo e(Storage::url($business->logo_path)); ?>" alt="" class="h-12 w-12 rounded-xl object-contain">
                        <?php else: ?>
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-lg font-bold text-brand-900 dark:bg-brand-900 dark:text-brand-100">
                                <?php echo e(mb_substr($business->name, 0, 1)); ?>

                            </span>
                        <?php endif; ?>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold"><?php echo e($business->name); ?></span>
                            <span class="block text-sm text-slate-600 dark:text-slate-400">
                                <?php echo e(__('nexo.categories.'.$business->category)); ?> · <?php echo e($business->city); ?>

                            </span>
                        </span>
                        <?php if($business->rating_count > 0): ?>
                            <span class="text-sm">
                                <span aria-hidden="true" class="text-amber-500">★</span>
                                <?php echo e(number_format((float) $business->rating, 1, ',')); ?>

                                <span class="text-slate-500">(<?php echo e($business->rating_count); ?>)</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <div class="mt-6"><?php echo e($businesses->links()); ?></div>
    <?php endif; ?>
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
<?php /**PATH /var/www/html/resources/views/public/directory.blade.php ENDPATH**/ ?>