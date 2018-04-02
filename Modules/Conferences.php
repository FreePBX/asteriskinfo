<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

class Conferences{
  public function __construct(){
    $this->freepbx = \FreePBX::Create();
    $this->astman = $this->freepbx->astman;
  }
  public function getDisplay(){
    if(!$this->astman->connected()){
      return _("Can't connect to Asterisk. Is Asterisk running and started by the correct user?");
    }
    $meetme_check = $this->astman->send_request('Command', array('Command' => 'module show like meetme'));
    $confbridge_check = $this->astman->send_request('Command', array('Command' =>'module show like confbridge'));
    $meetme_module = preg_match('/[1-9] modules loaded/', $meetme_check['data']);
    $confbridge_module = preg_match('/[1-9] modules loaded/', $confbridge_check['data']);
    if ($meetme_module) {
    	$arr_conferences[$conf_meetme]="meetme list";
    }
    if ($confbridge_module) {
	    $arr_conferences[$conf_confbridge]="confbridge list";
    }
    $arr_conferences = !empty($arr_conferences)&&is_array($arr_conferences)?$arr_conferences:[];
    $output = '';
    foreach ($arr_conferences as $key => $value) {
      $data = $this->freepbx->Asteriskinfo->getOutput($value);
      $output .= '<div class="panel panel-default"><div class="panel-body"><pre>'.$data.'</pre></div></div>';
    }
    return $output;
  }
}
