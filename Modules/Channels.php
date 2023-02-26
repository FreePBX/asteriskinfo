<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Channels extends ModuleBase
{
	private $ariPassword  = "";
	private $ariUser 	  = "";
	private $httpprefix   = "";
	private $httpbindport = "";
	private $httpbindaddr = "";

	public function __construct()
	{
		parent::__construct();
		$this->name 	= _("Channels");
		$this->nameraw  = "channels";

		$this->ariPassword 	= $this->config->get('FPBX_ARI_PASSWORD');
		$this->ariUser 		= $this->config->get('FPBX_ARI_USER');
		$this->httpprefix 	= $this->config->get('HTTPPREFIX');
		$this->httpbindport = $this->config->get('HTTPBINDPORT');
		$this->httpbindaddr = $this->config->get('HTTPBINDADDRESS');
	}
	
	public function getDisplay($ajax = false)
	{
		$data_return = "";
		$data_ari	 = $this->getARIInfo();

		if ($data_ari['status'] == false)
		{
			$data_return = sprintf('<div class="alert alert-danger">%s</div>', $data_ari['error']);
		}
		else
		{
			$data_return = $this->buildDisplay($data_ari['data'], $ajax);
		}
		return $data_return;
	}

	public function buildDisplay($endpoints = [], $ajax = false)
	{
		if ($ajax == true)
		{
			$data_template = array(
				'table_id' 	  => $this->nameraw,
				'module_id'   => $this->nameraw,
				'class_extra' => 'table-asteriskinfo-channels',
				'row_style'	  => 'modChannelsRowStyle',
				'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw),
				'cols' => array(
					'state' => array(
						'text' 	    => _("Status"),
						'class'     => 'text-center col-status',
						'sortable'  => true,
						'formatter' => 'modChannelsStatusFormatter'
					),
					'technology' => array(
						'text' 	   => _("Tech"),
						'class'     => 'col-tecnology',
						'sortable' => true,
					),
					'resource' => array(
						'text' 	   => _("Resource"),
						'class'     => 'col-resource',
						'sortable' => true,
					),
					'channel_count' => array(
						'text' 	   => _("Channel Count"),
						'class'     => 'text-center col-channel_count',
						'sortable' => true,
					),
				),
				'toolbar' => array(
					array(
						'type' 	   => 'dropdown-menu',
						'icon' 	   => 'fa-filter',
						'text' 	   => _("Filter"),
						'id'   	   => 'filter-status-channels-btn',
						'subitems' => array(
							array(
								'text' 		 => _("Online"),
								'icon' 		 => 'fa-check',
								'extra-data' => array(
									'status' => 'online',
								),
							),
							array(
								'text' 		 => _("Offline"),
								'icon' 		 => 'fa-times',
								'extra-data' => array(
									'status' => 'offline',
								),
							),
							array(
								'text' 		 => _("Unknown"),
								'icon' 		 => 'fa-question',
								'extra-data' => array(
									'status' => 'unknown',
								),
							),
							array(
								'type' 		 => 'divider',
							),
							array(
								'text' 		 => _("Undefined"),
								'icon' 		 => 'fa-exclamation',
								'extra-data' => array(
									'status' => 'undefined',
								),
							)
						),
					),
					array(
						'type' => 'button',
						'icon' => 'fa-undo',
						'text' => _("Clean Filter"),
						'id'   => 'filter-reset-btn',
					)
				),
			);
			$out = load_view(__DIR__.'/../views/view.asteriskinfo.grid.php', $data_template);
		}
		else
		{
			
		}
		return $out;
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

	public function getARIInfo()
	{
		$data_return = array(
			'status' => true,
			'error'  => '',
			'data' 	 => array(),
		);

		$data = $this->getOutput('ari show status');
		if(preg_match('(No such command)', $data) === 1)
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
				$url	= sprintf('http://%s:%s@%s:%s%s/ari/endpoints', $this->ariUser, $this->ariPassword, $host, $this->httpbindport, $prefix);
		
				$channels = @file_get_contents($url);
				if($channels === false)
				{
					$data_return['status'] = false;
					$data_return['error']  = _('The Asterisk REST Interface is not able to connect please check configuration in advanced settings.');
				}
				else
				{
					$data_return['data'] = json_decode($channels, true);
				}
			}
		}
		return $data_return;
	}

	public function getByAjax()
	{
		return true;
	}

	public function getDataAjax()
	{
		$data_ari	 = $this->getARIInfo();
		$data_return = array(
			'rows' 	 => array(),
			'status' => true,
		);

		if ($data_ari['status'] == false)
		{
			$data_return['status'] 			= false;
			$data_return['rows'][]['error'] = $data_ari['error'];
		}
		else
		{
			foreach($data_ari['data'] as $row)
			{
				$row['channel_count']  = count($row['channel_ids']);
				$data_return['rows'][] = $row;
			}
		}
		return $data_return;
	}
}