<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
private $astman;
class Chansip{
  public function __construct($astman){
    $this->astman = $astman;
  }
  public function isEnabled(){
    $sip_mod_check = $astman->send_request('Command', array('Command' => 'module show like chan_sip'));
    $sip_module = preg_match('/[1-9] modules loaded/', $sip_mod_check['data']);
    if (!$sip_module) {
      return = false;
    }
    return true;
  }
  public function getActiveChannels(){

  }

  public function getRegistrations(){

  }

  public function getPeers(){

  }

}
