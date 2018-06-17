<?php
include_once __DIR__.'/../../core.php';

?><form action="" method="post" id="add-form">
	<input type="hidden" name="op" value="add">
	<input type="hidden" name="backto" value="record-edit">

	<div class="row">
		<div class="col-md-6">
			{[ "type": "text", "label": "<?php echo tr('Nome'); ?>", "name": "nome", "required": 1, "value": "" ]}
		</div>

		<div class="col-md-6">
			{[ "type": "text", "label": "<?php echo tr('Targa'); ?>", "name": "targa", "required": 1, "maxlength": 10, "class": "alphanumeric-mask", "value": "" ]}
		</div>

	</div>

	<!-- PULSANTI -->
	<div class="row">
		<div class="col-md-12 text-right">
			<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo tr('Aggiungi'); ?></button>
		</div>
	</div>
</form>
