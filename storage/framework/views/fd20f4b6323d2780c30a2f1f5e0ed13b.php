<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => __('¿Cuándo?').' — '.$business->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('¿Cuándo?').' — '.$business->name)]); ?>
    <a href="<?php echo e(route('public.professional', [$business, $service])); ?>" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← <?php echo e(__('Cambiar profesional')); ?>

    </a>
    <p class="mt-3 text-sm text-slate-500"><?php echo e(__('Paso 3 de 4')); ?></p>
    <h1 class="mb-1 text-xl font-bold"><?php echo e(__('¿Cuándo?')); ?></h1>
    <p class="mb-5 text-sm text-slate-600 dark:text-slate-400">
        <?php echo e($service->name); ?> · <?php echo e($chosen?->name ?? __('Cualquier profesional')); ?>

    </p>

    <?php if(session('slot_taken')): ?>
        <p class="mb-4 rounded-lg bg-amber-100 px-4 py-3 text-sm text-amber-900" role="alert">
            <?php echo e(__('Ese horario acaba de ocuparse. Elige otro, por favor.')); ?>

        </p>
    <?php endif; ?>

    <div class="mb-4 flex items-center justify-between gap-2">
        <?php if($canGoBack): ?>
            <a href="<?php echo e(route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->subDay()->toDateString()])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día anterior')); ?>">‹</a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">‹</span>
        <?php endif; ?>

        <form method="GET" action="<?php echo e(route('public.times', [$business, $service])); ?>">
            <input type="hidden" name="professional" value="<?php echo e($chosen?->id ?? 'any'); ?>">
            <label for="date" class="sr-only"><?php echo e(__('Fecha')); ?></label>
            <input type="date" id="date" name="date" value="<?php echo e($day->toDateString()); ?>" onchange="this.form.submit()"
                   class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
        </form>

        <?php if($canGoForward): ?>
            <a href="<?php echo e(route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->addDay()->toDateString()])); ?>"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800"
               aria-label="<?php echo e(__('Día siguiente')); ?>">›</a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm text-slate-300 dark:text-slate-600" aria-hidden="true">›</span>
        <?php endif; ?>
    </div>

    <p class="mb-3 text-center font-medium capitalize"><?php echo e($day->isoFormat('dddd D [de] MMMM')); ?></p>

    <?php if(session('status')): ?>
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status"><?php echo e(session('status')); ?></p>
    <?php endif; ?>

    <?php if($slots->isEmpty()): ?>
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
            <?php echo e(__('No hay horarios disponibles este día.')); ?>

        </p>
    <?php else: ?>
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-4">
            <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time => $professionalId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(route('public.form', [$business, $service, 'professional' => $professionalId, 'start' => $day->toDateString().' '.$time])); ?>"
                       class="block rounded-lg bg-white py-2 text-center text-sm font-medium shadow-sm hover:ring-2 hover:ring-brand-500 dark:bg-slate-800">
                        <?php echo e($time); ?>

                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>

    <details class="mt-6 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800" <?php if($errors->any() || $slots->isEmpty()): ?> open <?php endif; ?>>
        <summary class="cursor-pointer text-sm font-medium text-brand-700 dark:text-brand-400">
            <?php echo e($slots->isEmpty() ? __('Avisarme si se libera un horario este día') : __('¿Prefieres otro horario? Súmate a la lista de espera')); ?>

        </summary>
        <form method="POST" action="<?php echo e(route('public.waitlist', [$business, $service])); ?>" class="mt-4 space-y-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="professional" value="<?php echo e($chosen?->id ?? 'any'); ?>">
            <input type="hidden" name="date" value="<?php echo e($day->toDateString()); ?>">

            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => __('Nombre'),'name' => 'client_name','required' => true,'autocomplete' => 'name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nombre')),'name' => 'client_name','required' => true,'autocomplete' => 'name']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['label' => __('Email'),'name' => 'client_email','type' => 'email','required' => true,'autocomplete' => 'email']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Email')),'name' => 'client_email','type' => 'email','required' => true,'autocomplete' => 'email']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
            <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e(__('Anotarme en la lista de espera')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <p class="text-xs text-slate-500">
                <?php echo e(__('Si alguien cancela ese día, te avisamos por email al instante.')); ?>

            </p>
        </form>
    </details>
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
<?php /**PATH /var/www/html/resources/views/public/booking/times.blade.php ENDPATH**/ ?>