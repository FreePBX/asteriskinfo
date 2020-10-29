<?php
namespace FreePBX\modules\Asteriskinfo\Modules;
class Dahdi{
  public function __construct(){
    $this->freepbx = \FreePBX::Create();
  }

  public function getDisplay(){
    $chan_dahdi = ast_with_dahdi();
    if($chan_dahdi){
      $arr_dahdi                    = array();
      $dahdidriverinfo              = _("Dahdi Channels");
      $dahdipriinfo                 = _("Dahdi PRI Spans");
      $arr_dahdi[$dahdidriverinfo]  = "dahdi show channels";
      $arr_dahdi[$dahdipriinfo]     = "pri show spans";
  
      foreach ($arr_dahdi as $key => $value) {
        $data = $this->freepbx->Asteriskinfo->getOutput($value);
        $output .= '<div class="panel panel-default"><div class="panel-heading">'.$key.'</div><div class="panel-body"><pre>'.$data.'</pre></div></div>';
      }
    } else {
      $output .= '<div class="panel-body" align="center"><pre> Dahdi is not loaded into asterisk, hence no channels / spans information to display.</pre></div>';
    }
    return $output;
  }
}