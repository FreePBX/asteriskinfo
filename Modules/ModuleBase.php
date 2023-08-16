<?php
namespace FreePBX\modules\Asteriskinfo\Modules;


class ModuleBase {

	public $name 	  = "";
	public $nameraw	  = "";
	public $cmd   	  = "";
	public $cmd_title = "";

	protected $freepbx;
	protected $config;
	protected $astman;
	protected $asteriskinfo;

	protected $ariPassword  = "";
	protected $ariUser 	  = "";
	protected $httpprefix   = "";
	protected $httpbindport = "";
	protected $httpbindaddr = "";

	public function __construct()
	{
		$this->freepbx 		= \FreePBX::Create();
		$this->config 		= $this->freepbx->Config;
		$this->astman  		= $this->freepbx->astman;
		$this->asteriskinfo = $this->freepbx->Asteriskinfo;

		$this->ariPassword 	= $this->config->get('FPBX_ARI_PASSWORD');
		$this->ariUser 		= $this->config->get('FPBX_ARI_USER');
		$this->httpprefix 	= $this->config->get('HTTPPREFIX');
		$this->httpbindport = $this->config->get('HTTPBINDPORT');
		$this->httpbindaddr = $this->config->get('HTTPBINDADDRESS');
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
			$this_class = static::class;
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
		$mod_check = $this->astman->send_request('Command', ['Command' => $cmd_check]);
		$mod_load  = preg_match('/[1-9] modules loaded/', (string) $mod_check['data']);
		return (bool) $mod_load;
	}

	public function getByAjax()
	{
		return false;
	}

	public function getDataAjax()
	{
		return [];
	}

	public function checkARIStatus()
	{
		$status 	= false;
		$dir 		= $this->config->get('ASTETCDIR');
		$file_conf 	= sprintf('%s/ari_general_additional.conf', $dir);

		if(file_exists($file_conf))
		{
			$contents = file_get_contents($file_conf);
			$lines 	  = parse_ini_string($contents, INI_SCANNER_RAW);
			if(isset($lines['enabled']) && $lines['enabled'])
			{
				$status = true;
			}
		}
		return $status;
	}

	protected function getARIInfoApi($api)
	{
		$data_return = ['status' => true, 'error'  => '', 'data' 	 => []];

		$data = $this->getOutput('ari show status');
		if(preg_match('(No such command)', (string) $data) === 1)
		{
			$data_return['status'] = false;
			$data_return['error']  = _('The Asterisk REST Interface Module is not loaded in asterisk');
		}
		else
		{
			$status = $this->checkARIStatus();
			if(!$status)
			{
				$data_return['status'] = false;
				$data_return['error']  = _('The Asterisk REST Interface is Currently Disabled.');
			}
			else
			{
				$prefix = (!empty($this->httpprefix)) 									? "/".$this->httpprefix : '';
				$host	= (!empty($this->httpbindaddr) && $this->httpbindaddr != '::') 	? $this->httpbindaddr : "localhost";
				$url	= sprintf('http://%s:%s@%s:%s%s/%s', $this->ariUser, $this->ariPassword, $host, $this->httpbindport, $prefix, $api);

				$result = @file_get_contents($url);
				if($result === false)
				{
					$data_return['status'] = false;
					$data_return['error']  = _('The Asterisk REST Interface is not able to connect please check configuration in advanced settings.');
				}
				else
				{
					$data_return['data'] = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
				}
			}
		}
		return $data_return;
	}
}