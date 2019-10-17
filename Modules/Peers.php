<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Peers{
  public function __construct(){
    $this->freepbx = \FreePBX::Create();
    $this->astman = $this->freepbx->astman;
  }

  public function getDisplay(){
    $pjsip_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_pjsip'));
    $pjsip_module = preg_match('/[1-9] modules loaded/', $pjsip_mod_check['data']);
    if($pjsip_module){
      $arr_peers['PJSIP'] = "pjsip show endpoints";
    }
    $sip_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_sip'));
    $sip_module = preg_match('/[1-9] modules loaded/', $sip_mod_check['data']);
    if($sip_module){
      $arr_peers['CHANSIP'] = "sip show peers";
    }
    $iax2_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_iax2'));
    $iax2_module = preg_match('/[1-9] modules loaded/', $iax2_mod_check['data']);
    if($iax2_module){
      $arr_peers['IAX2'] = "iax2 show peers";
    }
    $sccp_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_sccp'));
    $sccp_module = preg_match('/[1-9] modules loaded/', $sccp_mod_check['data']);
    if($sccp_module){
      $arr_peers['SCCP'] = "sccp show devices";
    }

    $arr_peers = !empty($arr_peers)&&is_array($arr_peers)?$arr_peers:array();
    foreach ($arr_peers as $key => $value) {
      $data = $this->freepbx->Asteriskinfo->getOutput($value);
      $output .= '<div class="panel panel-default"><div class="panel-heading">'.$key.'</div><div class="panel-body"><pre>'.$data.'</pre></div></div>';
    }
    return $output;
  }
}
