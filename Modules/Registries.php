<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Registries extends ModuleBase
{

	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Registries");
		$this->nameraw = "registries";
	}

	public function getDisplay()
	{
		$output = "";

		$arr_cmds 	 = [];
		$arr_modules = ['PJSIP' => ['module' => 'chan_pjsip', 'cmd' 	 => 'pjsip show registrations'], 'SIP' => ['module' => 'chan_sip', 'cmd' 	 => 'sip show registry'], 'IAX2' => ['module' => 'chan_iax2', 'cmd' 	 => 'iax2 show registry']];
		foreach ($arr_modules as $key => $info)
		{
			if ($this->checkModuleLoad($info['module'])) 
			{
				$arr_cmds[$key] = $info['cmd'];
			}
		}
		if (! empty($arr_cmds))
		{
			foreach ($arr_cmds as $key => $cmd)
			{
				$data 	 = $this->getOutput($cmd);
				$output .= $this->getPanel($data, $key);
			}
		}
		else
		{
			$output .= sprintf('<div class="panel-body" align="center"><pre>%s</pre></div>', _('Drivers (ChanSIP or PJSIP or IAX) are not loaded into asterisk, hence no registration information to display.'));
		}
		return $output;
	}
}