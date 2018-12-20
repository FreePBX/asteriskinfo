<?php
namespace FreePBX\modules;
use FreePBX\modules\Asteriskinfo\Modules;
class Asteriskinfo implements \BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new Exception("Not given a FreePBX Object");
		}
		$this->FreePBX = $freepbx;
		$this->db = $freepbx->Database;
		$this->astman = $this->FreePBX->astman;
		$this->output = array();
	}
	public function install() {
		if(!$this->FreePBX->Config->get('HTTPENABLED')){
			$this->FreePBX->Config->update('HTTPENABLED', true);
		}
		if(!$this->FreePBX->Config->get('ENABLE_ARI')){
			$this->FreePBX->Config->update('ENABLE_ARI', true);
		}
	}
	public function uninstall() {}
	public function backup() {}
	public function restore($backup) {}
	public function doConfigPageInit($page) {}

	public function getOutput($command){
		$response = $this->astman->send_request('Command',array('Command'=>$command));
		$new_value = htmlentities($response['data'],ENT_COMPAT | ENT_HTML401, "UTF-8");
		return ltrim($new_value,'Privilege: Command');
	}

	public function listModules(){
		$modules = [];
		foreach(glob(__DIR__.'/Modules/*.php') as $file){
			$modules[] = basename($file,'.php');
		}
		return $modules;
	}
	public function getModuleDisplay($module){
		$output = '';
		$classname = sprintf('\\FreePBX\\modules\\Asteriskinfo\\Modules\\%s',$module);
		$o = new $classname();
		if(method_exists($o,'getDisplay')){
			$output =  $o->getDisplay();
			$output = load_view(__DIR__.'/views/panel.php', array('title' => $module, 'body' => $output));
		}
		return $output;
	}

	public function getRnav($module = 'all'){
		$rnav =	 sprintf('<a href="?display=asteriskinfo"  class="list-group-item %s">%s</a>',(($module == 'all')?"active":""),_("All"));
		foreach($this->listModules() as $mod){
			$rnav .=	 sprintf('<a href="?display=asteriskinfo&module=%s"  class="list-group-item %s">%s</a>',$mod,(($module == $mod)?"active":""),_($mod));
		}
		return $rnav;
	}

	public function getDisplay($module = 'all'){
		$out = '';
		if($module != 'all'){
			return $this->getModuleDisplay($module);
		}
		foreach($this->listModules() as $mod){
			$out .= $this->getModuleDisplay($mod);
		}
		return $out;
	}
}
