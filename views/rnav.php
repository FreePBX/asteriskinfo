<?php
	if (!defined('FREEPBX_IS_AUTH')) { exit('No direct script access allowed'); }
?>
<div class="list-group list-group-rnav">
	<?php
	foreach((array) $modules as $mod_id => $mod)
	{
		echo sprintf('<a href="?display=asteriskinfo&module=%s" class="list-group-item %s">%s</a>', $mod_id, (($module == $mod_id) ? "active" : ""), $mod['name']);
	}
	?>
</div>