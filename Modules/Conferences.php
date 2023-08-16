<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Conferences extends ModuleBase
{
	
	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Conferences");
		$this->nameraw = "conferences";
	}

	public function getDisplay()
	{
		$output = '';
		if(!$this->astman->connected())
		{
			$output = _("Can't connect to Asterisk. Is Asterisk running and started by the correct user?");
		}
		else
		{
			$arr_cmds 	 = [];
			$arr_modules = [['module' => 'meetme', 'cmd' 	 => 'meetme list', 'title'  => _('MeetMe Conference Info')], ['module' => 'confbridge', 'cmd' 	 => 'confbridge list', 'title'  => _('Conference Bridge Info')]];
			foreach ($arr_modules as $key => $info)
			{
				if ($this->checkModuleLoad($info['module'])) 
				{
					$arr_cmds[] = $info;
				}
			}
			foreach ($arr_cmds as $key => $info)
			{
				$data 	 = $this->getOutput($info['cmd']);
				$output .= $this->getPanel($data, $info['title']);
			}
		}
		return $output;
	}
}