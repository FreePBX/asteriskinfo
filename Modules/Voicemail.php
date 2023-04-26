<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Voicemail extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Voicemail");
		$this->nameraw = "voicemail";

		$this->cmd 	 	 = "voicemail show users";
		$this->cmd_title = _("Voicemail Users");
	}
}