<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
private $astman;
class Chanpjsip{
  public function __construct($astman){
    $this->astman = $astman;
  }
  public function isEnabled(){
    $pjsip_mod_check = $this->astman->send_request('Command', array('Command' => 'module show like chan_pjsip'));
    $pjsip_module = preg_match('/[1-9] modules loaded/', $pjsip_mod_check['data']);
    if ($pjsip_module) {
      return true;
    }
    return false
  }
  public function getActiveChannels(){

  }

  public function getRegistrations(){

  }

  public function getPeers(){

  }
}
