<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Queues extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Queues");
		$this->nameraw = "queues";

		$this->cmd   	 = "queue show";
		$this->cmd_title = _("Queues Info");
	}
}