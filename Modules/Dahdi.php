<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Dahdi extends ModuleBase
{

	public function __construct()
	{
		parent::__construct();
		$this->name    = _("Dahdi");
		$this->nameraw = "dahdi";
	}

	public function getDisplay()
	{
		$output = "";
		$chan_dahdi = ast_with_dahdi();
		if($chan_dahdi)
		{
			$arr_dahdi = [['title' => _("Dahdi Channels"), 'cmd' 	=> 'dahdi show channels'], ['title' => _("Dahdi PRI Spans"), 'cmd' 	=> 'pri show spans']];
			foreach ($arr_dahdi as $row)
			{
				$data 	 = $this->getOutput($row['cmd']);
				$output .= $this->getPanel($data, $row['title']);
			}
		}
		else
		{
			$output .= sprintf('<div class="panel-body" align="center"><pre>%s</pre></div>', _('Dahdi is not loaded into asterisk, hence no channels / spans information to display.'));
		}
		return $output;
	}
}