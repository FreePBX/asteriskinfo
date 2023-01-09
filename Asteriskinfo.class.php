<?php
namespace FreePBX\modules;
use FreePBX\modules\Asteriskinfo\Modules;

class Asteriskinfo implements \BMO
{
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		$this->FreePBX 	= $freepbx;
		$this->db 		= $freepbx->Database;
		$this->astman 	= $this->FreePBX->astman;
		$this->config	= $this->FreePBX->Config;
		$this->output 	= array();
	}
	public function install()
	{
		$configs = array(
			'HTTPENABLED' => true,
			'ENABLE_ARI'  => true,
		);
		foreach ( $configs as $key => $value )
		{
			if(!$this->config->get($key))
			{
				$this->config->update($key, $value);
			}
		}
	}
	public function uninstall() {}
	public function backup() {}
	public function restore($backup) {}
	public function doConfigPageInit($page) {}

	public function getRightNav($request, $params = array())
	{
		$modules = $this->listModules(true);
		$data 	 = array(
			'modules' => $modules,
			'module'  => empty($_REQUEST['module']) ? 'all' : $_REQUEST['module'],
		);
		$data_return = $this->showPage("rnav", $data);
		return $data_return;
	}

	public function showPage($page, $params = array())
	{
		$data = array(
			"asteriskinfo"	=> $this,
			'request'	 	=> $_REQUEST,
			'page' 		 	=> $page,
		);
		$data = array_merge($data, $params);
		switch ($page) 
		{
			case "rnav":
				$data_return = load_view(__DIR__."/views/rnav.php", $data);
			break;

			case "asteriskinfo":
				$data['astman'] = $this->astman;
				$data['module'] = empty($_REQUEST['module']) ? 'all' : $_REQUEST['module'];
				$data_return = load_view(__DIR__."/views/page.asteriskinfo.php", $data);
			break;

				
			default:
				$data_return = sprintf(_("Page Not Found (%s)!!!!"), $page);
		}
		return $data_return;
	}

	public function getVersion() {
		return $this->config->get('ASTVERSION');
	}

	private function getPathModules()
	{
		return __DIR__.'/Modules';
	}

	private function getPathModule($moduleName)
	{
		return sprintf('%s/%s.php', $this->getPathModules(), $moduleName);
	}

	public function getOutput($command){
		$response = $this->astman->send_request('Command',array('Command'=>$command));
		$new_value = htmlentities($response['data'],ENT_COMPAT | ENT_HTML401, "UTF-8");
		return ltrim($new_value,'Privilege: Command');
	}

	public function listModules($includeAll = false)
	{
		$modules = [];

		if ($includeAll === true)
		{
			$modules['all'] = array(
				'class' => 'all',
				'name'  => _('All'),
			);
		}

		foreach(glob($this->getPathModule("*")) as $file)
		{
			$module_name = basename($file,'.php');
			$module_id 	 = strtolower($module_name);
			if ($module_id != "modulebase")
			{
				$className 		= $module_name;
				$classNameFull 	= sprintf('\\FreePBX\\modules\\Asteriskinfo\\Modules\\%s', $className);
				$moduleName  	= "";

				if (! class_exists($classNameFull, false))
				{
					include_once $this->getPathModule($className);
				}
				if (class_exists($classNameFull, false))
				{
					$o = new $classNameFull();
					if(method_exists($o, 'getName'))
					{
						$moduleName = $o->getName();
					}
					else
					{
						$moduleName = _($className);
					}

					$modules[$module_id] = array(
						'class_full' => $classNameFull,
						'class' 	 => $className,
						'name'  	 => $moduleName,
					);
				}
			}
		}
		return $modules;
	}

	public function getModuleDisplay($module)
	{
		$output 	= '';
		$ls_modules = $this->listModules();

		if (! array_key_exists(strtolower($module), $ls_modules))
		{
			$output =  sprintf(_("Error: Module '%s' not found!"), $module);
		}
		else
		{
			$classFull 	= $ls_modules[$module]['class_full'];
			$moduleName = $ls_modules[$module]['name'];

			$o = new $classFull();
			if(method_exists($o, 'getDisplay'))
			{
				$output = $o->getDisplay();
				$output = load_view(__DIR__.'/views/view.asteriskinfo.panel.php', array('title' => $moduleName, 'body' => $output));
			}
		}
		return $output;
	}

	public function getDisplay($module = 'all')
	{
		$out 		= '';
		$ls_modules = $this->listModules();

		if ($module == "all")
		{
			foreach($ls_modules as $mod_id => $mod)
			{
				$out .= $this->getModuleDisplay($mod_id);
			}
		}
		else
		{
			if (array_key_exists($module, $ls_modules))
			{
				$out = $this->getModuleDisplay($module);
			}
			else
			{
				$out = _("Module Not Valid!");
			}
		}

		return $out;
	}
}
