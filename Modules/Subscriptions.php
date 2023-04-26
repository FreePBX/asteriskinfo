<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Subscriptions extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Subscriptions");
		$this->nameraw = "subscriptions";

		$this->cmd   	 = "core show hints";
		$this->cmd_title = _("Subscribe/Notify");
	}
}