<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Channels {
  public function __construct() {
	$this->FreePBX = \FreePBX::Create();
	$this->ariPassword =  $this->FreePBX->Config->get('FPBX_ARI_PASSWORD');
	$this->ariUser = $this->FreePBX->Config->get('FPBX_ARI_USER');
	$this->httpprefix = $this->FreePBX->Config->get('HTTPPREFIX');
	$this->httpbindport = $this->FreePBX->Config->get('HTTPBINDPORT');
	$this->httpbindaddr = $this->FreePBX->Config->get('HTTPBINDADDRESS');
  }
  public function getDisplay() {
	$url = 'http://'.$this->ariUser.':'.$this->ariPassword.'@localhost:'.$this->httpbindport.'/ari/endpoints';
	if(!empty($this->httpbindaddr) && $this->httpbindaddr != '::') {
		$url = 'http://'.$this->ariUser.':'.$this->ariPassword.'@'.$this->httpbindaddr.':'.$this->httpbindport.'/ari/endpoints';
	}
	$data = $this->FreePBX->Asteriskinfo->getOutput('ari show status');
	if(preg_match('(No such command)', $data) === 1) {
		$info = '<div class="alert alert-danger">'. _('The Asterisk REST Interface Module is not loaded in asterisk').'</div>';
		return $info;
	}
	$status = $this->checkARIStatus();
	if(!$status){
		$info = '<div class="alert alert-danger">'. _('The Asterisk REST Interface is Currently Disabled.').'</div>';
		return $info;
	}
	if(isset($this->httpprefix) && !empty($this->httpprefix)) {
		$url = 'http://'.$this->ariUser.':'.$this->ariPassword.'@localhost:'.$this->httpbindport.'/'.$this->httpprefix.'/ari/endpoints';
	}
	$channels = @file_get_contents($url);
	if($channels === false) {
		$info = '<div class="alert alert-danger">'. _('The Asterisk REST Interface is not able to connect please check configuration in advanced settings.').'</div>';
		return $info;
	}
	$endpoints = json_decode($channels,true);
	return $this->buildDisplay($endpoints);
  }
  public function buildDisplay($endpoints = []) {
	$out = '<table class="table table-striped table-bordered">';
	$out .= sprintf('<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>',_("Tech"),_("Resource"),_("Status"),_("Channel Count"));
	foreach($endpoints as $row){
		$out .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',$row['technology'],$row['resource'],strtoupper($row['state']),count($row['channel_ids']));
	}
	$out .= '</table>';
	return $out;
  }
  public function checkARIStatus() {
	$status = false;
	$dir = $this->FreePBX->Config()->get('ASTETCDIR');
	if(file_exists($dir.'/ari_general_additional.conf')) {
		$contents = file_get_contents($dir.'/ari_general_additional.conf');
		$lines = parse_ini_string($contents,INI_SCANNER_RAW);
		if(isset($lines['enabled']) && $lines['enabled']) {
			$status = true;
		}
	}
	return $status;
  }
}
