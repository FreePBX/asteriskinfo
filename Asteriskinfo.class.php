<?php
namespace FreePBX\modules;
use FreePBX\modules\Asteriskinfo\Modules;

class Asteriskinfo implements \BMO
{
	public $FreePBX;
	public $db;
	public $astman;
	public $config;
	public $output;

	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		$this->FreePBX = $freepbx;
		$this->db 	   = $freepbx->Database;
		$this->astman  = $this->FreePBX->astman;
		$this->config  = $this->FreePBX->Config;
		$this->output  = [];
	}

	public function install()
	{
		$configs = ['HTTPENABLED' => true, 'ENABLE_ARI'  => true];
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

	public function getRightNav($request, $params = [])
	{
		$modules = $this->listModules(true);
		$data 	 = ['modules' => $modules, 'module'  => empty($_REQUEST['module']) ? 'all' : $_REQUEST['module']];
		$data_return = $this->showPage("rnav", $data);
		return $data_return;
	}

	public function showPage($page, $params = [])
	{
		$data = ["asteriskinfo"	=> $this, 'request'	 	=> $_REQUEST, 'page' 		 	=> $page];
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
		$response = $this->astman->send_request('Command',['Command'=>$command]);
		$new_value = htmlentities((string) $response['data'],ENT_COMPAT | ENT_HTML401, "UTF-8");
		return ltrim($new_value,'Privilege: Command');
	}

	public function listModules($includeAll = false)
	{
		$modules = [];

		if ($includeAll === true)
		{
			$modules['all'] = ['class' => 'all', 'name'  => _('All')];
		}

		foreach(glob($this->getPathModule("*")) as $file)
		{
			$module_name = basename((string) $file,'.php');
			$module_id 	 = strtolower($module_name);
			if ($module_id != "modulebase")
			{
				$className 		= $module_name;
				$classNameFull 	= sprintf('\\FreePBX\\modules\\Asteriskinfo\\Modules\\%s', $className);
				$moduleName  	= "";
				$moduleAjax 	= false;

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

					if(method_exists($o, 'getNamePretty'))
					{
						$namePretty = $o->getNamePretty();
					}
					else
					{
						$namePretty = $moduleName;
					}

					if(method_exists($o, 'getByAjax'))
					{
						$moduleAjax = $o->getByAjax();
					}

					$modules[$module_id] = ['class_full'  => $classNameFull, 'class' 	  => $className, 'name'  	  => $moduleName, 'name_pretty' => $namePretty, 'ajax'		  => $moduleAjax];
				}
			}
		}
		return $modules;
	}

	public function getModuleDisplay($module)
	{
		$output 	= '';
		$ls_modules = $this->listModules();

		if (! array_key_exists(strtolower((string) $module), $ls_modules))
		{
			$output =  sprintf(_("Error: Module '%s' not found!"), $module);
		}
		else
		{
			$classFull 	= $ls_modules[$module]['class_full'];
			$moduleName = $ls_modules[$module]['name_pretty'];
			$isAjax     = $ls_modules[$module]['ajax'];

			$o = new $classFull();
			if(method_exists($o, 'getDisplay'))
			{
				$output = $o->getDisplay($isAjax);
				$output = load_view(__DIR__.'/views/view.asteriskinfo.panel.php', ['title' => $moduleName, 'body' => $output]);
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

	public function ajaxRequest($req, &$setting)
	{
		return match ($req) {
      'getGrid' => true,
      default => false,
  };
	}

	public function ajaxHandler()
	{
		$request = $_REQUEST;
		$command = isset($request['command']) ? trim((string) $request['command']) : '';
		switch($command)
		{
			case 'getGrid':
				$module = isset($request['module_info']) ? strtolower(trim((string) $request['module_info'])) : '';
				if ($module == "all")
				{
					$module = "";
				}

				$ret = [];
				if (! empty($module))
				{
					$ls_modules = $this->listModules();
					if (array_key_exists($module, $ls_modules))
					{
						$classFull = $ls_modules[$module]['class_full'];
						$isAjax    = $ls_modules[$module]['ajax'];

						if ($isAjax)
						{
							$o = new $classFull();
							if(method_exists($o, 'getDataAjax'))
							{
								$ajax = $o->getDataAjax();
								if ($ajax['status'] == true)
								{
									$ret = $ajax['rows'];
								}
								else
								{
									dbug($ajax['error']);
								}
							}
						}
					}
					else
					{
						dbug(sprintf(_("Error: Module '%s' not found!"), $module));
					}
				}
				$retrun_data = $ret;
				break;

			default:
				$retrun_data = ["status" => false, "message" => _("Command not found!"), "command" => $command];
		}
		return $retrun_data;
	}
}
