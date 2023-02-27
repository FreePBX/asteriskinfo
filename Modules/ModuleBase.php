<?php
namespace FreePBX\modules\Asteriskinfo\Modules;


class ModuleBase {

	public $name 	  = "";
	public $nameraw	  = "";
	public $cmd   	  = "";
	public $cmd_title = "";

	public function __construct()
	{
		$this->freepbx 		= \FreePBX::Create();
		$this->config 		= $this->freepbx->Config;
		$this->astman  		= $this->freepbx->astman;
		$this->asteriskinfo = $this->freepbx->Asteriskinfo;
	}

	public function getName()
	{
		$data_return = "";
		if (! empty($this->name))
		{
			$data_return = $this->name;
		}
		else
		{
			$this_class = get_class($this);
			$data_return = substr($this_class, (strrpos($this_class, '\\') ?: -1) + 1) ;
		}
		return $data_return;
	}

	public function getOutput($cmd)
	{
		return $this->asteriskinfo->getOutput($cmd);
	}

	public function getPanel($data, $title = "")
	{
		if (empty($title))
		{
			return sprintf('<div class="panel panel-default"><div class="panel-body"><pre>%s</pre></div></div>', $data);
		}
		else
		{
			return sprintf('<div class="panel panel-default"><div class="panel-heading">%s</div><div class="panel-body"><pre>%s</pre></div></div>', $title, $data);
		}
	}

	public function getDisplay()
	{
		$output = "";
		if (! empty($this->cmd))
		{
			$data 	= $this->getOutput($this->cmd);
			$output = $this->getPanel($data, $this->cmd_title);
		}
		return $output;
	}

	public function checkModuleLoad($module)
	{
		$cmd_check = sprintf('module show like %s', $module);
		$mod_check = $this->astman->send_request('Command', array('Command' => $cmd_check));
		$mod_load  = preg_match('/[1-9] modules loaded/', $mod_check['data']);
		return (bool) $mod_load;
	}

	public function getByAjax()
	{
		return false;
	}

	public function getDataAjax()
	{
		return array();
	}
}