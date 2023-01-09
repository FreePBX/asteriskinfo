<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Channels extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name = _("Channels");

		$this->ariPassword 	= $this->config->get('FPBX_ARI_PASSWORD');
		$this->ariUser 		= $this->config->get('FPBX_ARI_USER');
		$this->httpprefix 	= $this->config->get('HTTPPREFIX');
		$this->httpbindport = $this->config->get('HTTPBINDPORT');
		$this->httpbindaddr = $this->config->get('HTTPBINDADDRESS');
	}
	
	public function getDisplay()
	{
		$data = $this->getOutput('ari show status');
		if(preg_match('(No such command)', $data) === 1)
		{
			$info = sprintf('<div class="alert alert-danger">%s</div>', _('The Asterisk REST Interface Module is not loaded in asterisk'));
			return $info;
		}

		$status = $this->checkARIStatus();
		if(!$status)
		{
			$info = sprintf('<div class="alert alert-danger">%s</div>', _('The Asterisk REST Interface is Currently Disabled.'));
			return $info;
		}
		
		$prefix = (!empty($this->httpprefix)) 									? "/".$this->httpprefix : '';
		$host	= (!empty($this->httpbindaddr) && $this->httpbindaddr != '::') 	? $this->httpbindaddr : "localhost";
		$url	= sprintf('http://%s:%s@%s:%s%s/ari/endpoints', $this->ariUser, $this->ariPassword, $host, $this->httpbindport, $prefix);

		$channels = @file_get_contents($url);
		if($channels === false)
		{
			$info = sprintf('<div class="alert alert-danger">%s</div>', _('The Asterisk REST Interface is not able to connect please check configuration in advanced settings.'));
			return $info;
		}
		$endpoints = json_decode($channels, true);
		return $this->buildDisplay($endpoints);
	}

	public function buildDisplay($endpoints = [])
	{
		$out = '<table class="table  table-bordered table-asteriskinfo-channels">';
		$out .= sprintf('<tr><th class="col-status">%s</th><th>%s</th><th>%s</th><th>%s</th></tr>',_("Status"), _("Tech"), _("Resource"), _("Channel Count"));
		foreach($endpoints as $row)
		{
			$status_row = strtoupper($row['state']);
			$status_icon = "";
			$status_color = "";

			switch($status_row)
			{
				case "ONLINE":
					$status_icon = "fa-toggle-on";
					$status_color = "channel-status-online";
					break;

				case "OFFLINE":
					$status_icon = "fa-toggle-off";
					$status_color = "channel-status-offline";
					break;

				case "UNKNOWN":
					$status_icon = "fa-question";
					$status_color = "channel-status-unknown";
					break;

				default:
					$status_icon = "fa-exclamation";
					$status_color = "channel-status-default";
					break;
			}

			$out .= sprintf('<tr class="%s"><td class="col-status"><i class="fa  fa-lg %s" aria-hidden="true" title="%s"></td><td>%s</td><td>%s</i></td><td>%s</td></tr>',$status_color, $status_icon, $status_row, $row['technology'], $row['resource'], count($row['channel_ids']));
		}
		$out .= '</table>';
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
}