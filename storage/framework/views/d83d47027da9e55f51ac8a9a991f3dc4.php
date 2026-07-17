<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php $__currentLoopData = $urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e($url['loc']); ?></loc>
        <?php if(isset($url['lastmod'])): ?><lastmod><?php echo e($url['lastmod']); ?></lastmod><?php endif; ?>
        <priority><?php echo e($url['priority']); ?></priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset>
<?php /**PATH /app/resources/views/sitemap.blade.php ENDPATH**/ ?>