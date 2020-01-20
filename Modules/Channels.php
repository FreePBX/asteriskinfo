<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Channels {
  public function __construct() {
	$this->FreePBX = \FreePBX::Create();
	$this->ariPassword =  $this->FreePBX->Config->get('FPBX_ARI_PASSWORD');
	$this->ariUser = $this->FreePBX->Config->get('FPBX_ARI_USER');
	$this->httpprefix = $this->FreePBX->Config->get('HTTPPREFIX');
  }
  public function getDisplay() {
	$url = 'http://'.$this->ariUser.':'.$this->ariPassword.'@localhost:8088/ari/endpoints';
	$status = $this->checkARIStatus();
	if(!$status){
		$info = '<div class="alert alert-danger">'. _('The Asterisk REST Interface is Currently Disabled.').'</div>';
		return $info;
	}
	if(isset($this->httpprefix) && !empty($this->httpprefix)) {
		$url = 'http://'.$this->ariUser.':'.$this->ariPassword.'@localhost:8088/'.$this->httpprefix.'/ari/endpoints';
	}
	$channels = file_get_contents($url);
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
