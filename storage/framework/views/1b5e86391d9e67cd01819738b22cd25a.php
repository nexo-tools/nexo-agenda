<?php if (isset($component)) { $__componentOriginalae5c3ca666306b3b2dcb109e55417bc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae5c3ca666306b3b2dcb109e55417bc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.error-layout','data' => ['code' => 503,'title' => __('En mantenimiento'),'message' => __('Estamos haciendo mejoras. Volvemos en unos minutos.')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('error-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['code' => 503,'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('En mantenimiento')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Estamos haciendo mejoras. Volvemos en unos minutos.'))]); ?>
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
<?php /**PATH /app/resources/views/errors/503.blade.php ENDPATH**/ ?>