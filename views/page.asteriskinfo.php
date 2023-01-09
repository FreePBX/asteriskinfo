<?php
	if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
?>
<div class="container-fluid">
	<h1><?php echo _("Asterisk Info")?></h1>
	<?php if (! $asteriskinfo->astman->connected()): ?>
		<div class="alert alert-danger alert-asterisk-stop" role="alert">
			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo _("Asterisk doesn't appear to be running!"); ?>
		</div>
	<?php else: ?>
		<div class="alert alert-info">
			<p><?php echo _('This page supplies various information about Asterisk')?></p>
			<p><b><?php echo _("Current Asterisk Version:")?></b> <?php echo $asteriskinfo->getVersion(); ?></p>
		</div>
		<div class = "display full-border">
			<div class="row">
				<div class="col-sm-12">
					<div class="fpbx-container">
						<div class="tab-content display full-border">
							<?php echo $asteriskinfo->getDisplay($module); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>