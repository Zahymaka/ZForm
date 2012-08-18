<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="control-group <?php echo $error ? 'error ' : ''; ?>field-<?php echo $field->id; ?>" id="field_<?php echo $field->id; ?>">
    <?php echo $field->form_label(array('class' => 'control-label')); ?>

    <div class="controls">
		<?php echo $field->form_field(); ?>
		<?php echo sprintf('<p class="help-block">%s</p>', $error ? $error : $help_text); ?>
    </div>
</div>