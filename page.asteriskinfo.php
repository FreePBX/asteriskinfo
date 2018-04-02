<?php
namespace FreePBX\modules;
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
$version = \FreePBX::Config()->get('ASTVERSION');
$astinfo = \FreePBX::create()->Asteriskinfo;
$request = $_REQUEST;
$dispnum = 'asteriskinfo'; //used for switch on config.php
$astman = $astinfo->astman;

$mode = isset($_GET['module'])?$_GET['module']:'all';
?>
<div class="container-fluid">
	<h1><?php echo _("Asterisk Info")?></h1>
	<div class="alert alert-info">
		<?php echo _('This page supplies various information about Asterisk')?><br/>
		<b><?php echo _("Current Asterisk Version:")?></b> <?php echo $version ?>
	</div>
	<?php echo (!$astman->connected())?$amerror:'';?>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-9">
					<div class="fpbx-container">
						<div class="tab-content display full-border">
							<?php echo $astinfo->getDisplay($mode); ?>
						</div>
					</div>
				</div>
				<div class="col-sm-3 bootnav">
					<div class="list-group">
						<?php echo $astinfo->getRnav($mode) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
