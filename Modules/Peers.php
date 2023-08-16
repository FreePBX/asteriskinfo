<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Peers extends ModuleBase
{

	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Peers");
		$this->nameraw = "peers";
	}

	public function getDisplay()
	{
		$output 	= "";
		$arr_cmds 	= [];

		$arr_modules = ['PJSIP' => ['module' => 'chan_pjsip', 'cmd' 	 => 'pjsip show endpoints'], 'CHANSIP' => ['module' => 'chan_sip', 'cmd' 	 => 'sip show peers'], 'IAX2' => ['module' => 'chan_iax2', 'cmd' 	 => 'iax2 show peers'], 'SCCP' => ['module' => 'chan_sccp', 'cmd' 	 => 'sccp show devices']];
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
			$output .= sprintf('<div class="panel-body" align="center"><pre>%s</pre></div>', _('Drivers (ChanSIP or PJSIP or IAX or SCCP) are not loaded into asterisk, hence no Peers information to display.'));
		}
		
		return $output;
	}
}
