<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div <?php echo HTML::attributes($attributes); ?>>
    <?php echo $field->form_label(); ?>
    <div class="form-field-wrap">
    <?php echo $field->form_field(); ?>
    </div>
</div>

