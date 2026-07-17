<?php if (isset($component)) { $__componentOriginal58c831a7c3cbf004f2e66a23aed50e5b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58c831a7c3cbf004f2e66a23aed50e5b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-layout','data' => ['title' => __('Centro de ayuda')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Centro de ayuda'))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <meta name="description" content="<?php echo e(__('Preguntas frecuentes sobre reservas, cancelaciones y cómo registrar tu negocio.')); ?>">
     <?php $__env->endSlot(); ?>

    <header class="mb-6">
        <h1 class="text-2xl font-bold"><?php echo e(__('Centro de ayuda')); ?></h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            <?php echo e(__('Encuentra respuestas rápidas o escríbenos.')); ?>

        </p>
    </header>

    <?php
        $faqs = [
            [__('¿Necesito una cuenta para reservar?'), __('No. Reservas con tu nombre, email y teléfono; te enviamos un enlace para gestionar tu turno.')],
            [__('¿Cómo cancelo o reprogramo mi turno?'), __('Abre el enlace que te enviamos por email y usa los botones para cancelar o reprogramar, dentro del plazo que fija el negocio.')],
            [__('No me llegó el email de confirmación.'), __('Revisa las carpetas de spam o promociones. Si no aparece, contacta al negocio directamente.')],
            [__('¿Cómo registro mi negocio?'), __('Crea una cuenta gratis, agrega tus servicios y tu equipo, y comparte tu página pública de reservas.')],
            [__('¿Tiene costo?'), __('Nexo Agenda es open source y self-hosted: sin comisiones ni cuotas por cliente.')],
            [__('¿Cómo aparezco en el directorio?'), __('Actívalo desde los ajustes de tu negocio para aparecer en la página de exploración.')],
        ];
    ?>

    <div class="space-y-3">
        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$question, $answer]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <details class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                <summary class="cursor-pointer font-semibold"><?php echo e($question); ?></summary>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400"><?php echo e($answer); ?></p>
            </details>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <section class="mt-8 rounded-2xl bg-brand-50 p-5 text-center dark:bg-slate-800">
        <p class="font-semibold"><?php echo e(__('¿No encontraste lo que buscabas?')); ?></p>
        <a href="<?php echo e(route('contact')); ?>"
           class="mt-3 inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
            <?php echo e(__('Escríbenos')); ?>

        </a>
    </section>
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
<?php /**PATH /var/www/html/resources/views/help/index.blade.php ENDPATH**/ ?>