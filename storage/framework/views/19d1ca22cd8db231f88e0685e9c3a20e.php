<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo e(config('app.name')); ?> — <?php echo e(__('Reservas online para tu negocio')); ?></title>
        <meta name="description" content="<?php echo e(__('Agenda, servicios, profesionales y reservas online. Open source y self-hosted.')); ?>">

        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#0d9488">

        <meta property="og:title" content="<?php echo e(config('app.name')); ?>">
        <meta property="og:description" content="<?php echo e(__('Reservas online para tu negocio')); ?>">
        <meta property="og:image" content="<?php echo e(url('/og/og-default.png')); ?>">
        <meta property="og:type" content="website">

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <main class="flex min-h-screen flex-col items-center justify-center gap-6 px-6 text-center">
            <img src="/favicon.svg" alt="" width="88" height="88">
            <h1 class="text-4xl font-bold tracking-tight"><?php echo e(config('app.name')); ?></h1>
            <p class="max-w-md text-lg text-slate-600 dark:text-slate-400">
                <?php echo e(__('Reservas online para tu negocio. Open source, sin comisiones.')); ?>

            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="<?php echo e(route('register')); ?>"
                   class="rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                    <?php echo e(__('Crear cuenta gratis')); ?>

                </a>
                <a href="<?php echo e(route('directory')); ?>"
                   class="rounded-lg border border-brand-700 px-5 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                    <?php echo e(__('Explorar negocios')); ?>

                </a>
            </div>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="text-sm text-brand-700 hover:underline dark:text-brand-400"><?php echo e(__('Ir a mi agenda')); ?></a>
            <?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.locale-switcher','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('locale-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $attributes = $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $component = $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
        </main>
    </body>
</html>
<?php /**PATH /var/www/html/resources/views/welcome.blade.php ENDPATH**/ ?>