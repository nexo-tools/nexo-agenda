<?php if (isset($component)) { $__componentOriginalae5c3ca666306b3b2dcb109e55417bc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae5c3ca666306b3b2dcb109e55417bc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.error-layout','data' => ['code' => 419,'title' => __('La página expiró'),'message' => __('Por seguridad, recarga la página e inténtalo de nuevo.')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('error-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['code' => 419,'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('La página expiró')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Por seguridad, recarga la página e inténtalo de nuevo.'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae5c3ca666306b3b2dcb109e55417bc9)): ?>
<?php $attributes = $__attributesOriginalae5c3ca666306b3b2dcb109e55417bc9; ?>
<?php unset($__attributesOriginalae5c3ca666306b3b2dcb109e55417bc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae5c3ca666306b3b2dcb109e55417bc9)): ?>
<?php $component = $__componentOriginalae5c3ca666306b3b2dcb109e55417bc9; ?>
<?php unset($__componentOriginalae5c3ca666306b3b2dcb109e55417bc9); ?>
<?php endif; ?>
<?php /**PATH /app/resources/views/errors/419.blade.php ENDPATH**/ ?>