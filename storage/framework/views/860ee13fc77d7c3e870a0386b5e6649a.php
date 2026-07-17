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
            <p class="text-sm text-slate-500 dark:text-slate-500"><?php echo e(__('Muy pronto.')); ?></p>
        </main>
    </body>
</html>
<?php /**PATH /app/resources/views/welcome.blade.php ENDPATH**/ ?>