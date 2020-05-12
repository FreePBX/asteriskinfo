<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Registries{
  public function __construct(){
    $this->freepbx = \FreePBX::Create();
    $this->astman = $this->freepbx->astman;
  }

  public function getDisplay(){
    $arr_registries = array();
    $pjsip_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_pjsip'));
	  $pjsip_module = preg_match('/[1-9] modules loaded/', $pjsip_mod_check['data']);
    if($pjsip_module){
      $arr_registries['PJSIP'] = "pjsip show registrations";
    }
    $sip_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_sip'));
    $sip_module = preg_match('/[1-9] modules loaded/', $sip_mod_check['data']);
    if ($sip_module) {
      	$arr_registries['SIP'] = "sip show registry";
    }
    $iax2_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_iax2'));
    $iax2_module = preg_match('/[1-9] modules loaded/', $iax2_mod_check['data']);
    if($iax2_module){
      $arr_registries[$iax2registry] = "iax2 show registry";
    }
    if (!empty($arr_registries)) {
	    foreach ($arr_registries as $key => $value) {
		    $data = $this->freepbx->Asteriskinfo->getOutput($value);
		    $output .= '<div class="panel panel-default"><div class="panel-heading">'.$key.'</div><div class="panel-body"><pre>'.$data.'</pre></div></div>';
	    }
    } else {
	    $output .= '<div class="panel-body" align="center"><pre> Drivers (ChanSIP or PJSIP or IAX) are not loaded into asterisk, hence no registration information to display.</pre></div>';
    }
    return $output;
  }
}
