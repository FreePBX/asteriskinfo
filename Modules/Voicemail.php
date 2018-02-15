<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
private $astman;
class Voicemail{
  public function __construct($astman){
    $this->astman = $astman;
  }
}
