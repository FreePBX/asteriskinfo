<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Subscriptions{
 public function __construct(){
    $this->freepbx = \FreePBX::Create();
    $this->astman = $this->freepbx->astman;
  }
  public function getDisplay(){
    $data = $this->freepbx->Asteriskinfo->getOutput('core show hints');
    return '<div class="panel panel-default"><div class="panel-body"><pre>'.$data.'</pre></div></div>';
  }
}
