<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Fax extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Fax");
		$this->nameraw = "fax";

		$this->cmd 	 	 = "fax show stats";
		$this->cmd_title = _("Fax");
	}
}