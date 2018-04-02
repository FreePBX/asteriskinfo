<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Voicemail{
 public function __construct(){
        $this->freepbx = \FreePBX::Create();
    $this->astman = $this->freepbx->astman;
  }
  public function getDisplay(){
    $data = $this->freepbx->Asteriskinfo->getOutput('voicemail show users');
    return '<div class="panel panel-default"><div class="panel-body"><pre>'.$data.'</pre></div></div>';
  }
}
