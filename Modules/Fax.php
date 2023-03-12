<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Fax extends ModuleBase
{
	private $name_pretty;

	public function __construct()
	{
		parent::__construct();
		$this->name			= _("Fax");
		$this->name_pretty 	= _("Fax Statistics");
		$this->nameraw 		= "fax";

		$this->cmd 	 	 = "fax show stats";
		$this->cmd_title = _("Statistics");
	}

	public function getNamePretty()
	{
		return $this->name_pretty;
	}

	public function getDisplay($ajax = false)
	{
		$data_return = "";
		if ($ajax == true)
		{
			$data_return = $this->buildDisplay(array(), $ajax);
		}
		else
		{
			$data_return = parent::getDisplay($ajax);
		}
		return $data_return;
	}

	public function buildDisplay($endpoints = [], $ajax = false)
	{
		if ($ajax == true)
		{
			$data_template = array(
				'table_id' 	  => $this->nameraw,
				'module_id'   => $this->nameraw,
				'class_extra' => 'table-asteriskinfo-fax',
				'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw),
				'cols' => array(
					'module' => array(
						'text' 	    => _("Module"),
						'class'     => 'col-fax-module',
						'sortable'  => true,
					),
					'key' => array(
						'text' 	   => _("Data"),
						'class'    => 'col-fax-key',
						'sortable' => true,
					),
					'val' => array(
						'text' 	   => _("Value"),
						'class'     => 'col-fax-val',
						'sortable' => true,
					),
				),
				'toolbar' => array(
					array(
						'type' 	   => 'dropdown-menu',
						'icon' 	   => 'fa-filter',
						'text' 	   => _("Modules"),
						'ul-class' => 'dropdown-menu-filters',
						'subitems' => array(
							array(
								'text' 		 => _("Global"),
								'icon' 		 => 'fa-globe',
								'extra-data' => array(
									'filterKey' => 'module',
									'filterVal' => 'Global',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
						),
					),
					array(
						'type' 		 => 'button',
						'icon' 		 => 'fa-undo',
						'text'		 => _("Clean Filter"),
						'class' 	 => 'table-filter-clean-all-btn',
						'extra-data' => array(
							'filterMod' => $this->nameraw,
							'filterTab' => $this->nameraw,
						),
					)
				),
			);
			$cmdReturn = $this->getResultCmd();
			if (! empty($cmdReturn['modules']) )
			{
				$data_template['toolbar'][0]['subitems'][] = array('type'=> 'divider');
				foreach ($cmdReturn['modules'] as $moduleName => $moduleVal)
				{
					$data_template['toolbar'][0]['subitems'][] = array(
						'text' 		 => $moduleName,
						'icon' 		 => 'fa-plug',
						'extra-data' => array(
							'filterKey' => 'module',
							'filterVal' => $moduleName,
							'filterMod' => $this->nameraw,
							'filterTab' => $this->nameraw,
						),
					);
				}
			}
			$out = load_view(__DIR__.'/../views/view.asteriskinfo.grid.php', $data_template);
			
		}
		else
		{
			$out = sprintf('<div class="alert alert-danger">%s</div>', _("AJAX shows up as disabled!"));
		}
		return $out;
	}

	public function getByAjax()
	{
		return true;
	}

	public function getDataAjax()
	{
		$data_return = array(
			'rows' 	 => array(),
			'status' => true,
		);

		if (! empty($this->cmd))
		{
			$data_cmd = $this->getResultCmd();

			// Format AJAX
			$rows = array();
			foreach ($data_cmd['global'] as $name => $value)
			{
				$rows[] = array(
					'module' => 'Global',
					'key' 	 => $name,
					'val' 	 => $value,
				);
			}

			foreach ($data_cmd['modules'] as $moduleName => $moduleVal)
			{
				foreach ($moduleVal as $propertyName => $propertyValue)
				{
					$rows[] = array(
						'module' => $moduleName,
						'key' 	 => $propertyName,
						'val' 	 => $propertyValue,
					);
				}
			}

			$data_return['rows'] = $rows;
		}
		else
		{
			$data_return['status'] = false;
			$data_return['rows'][]['error'] = _("No command detected!");
		}
		return $data_return;
	}

	private function getResultCmd()
	{
		$data_return = array();
		if (! empty($this->cmd))
		{
			$return_cmd = $this->getOutput($this->cmd);
			$cmd_lines  = explode("\n", $return_cmd);

			$data_cmd = array();
			$moduleSec = "";
			
			foreach ($cmd_lines as $line)
			{
				if (trim($line) == '' || trim($line) == '---------------')
				{
					continue;
				}

				if (strpos($line, ":") === false)
				{
					$moduleSec = trim($line);
				}
				else
				{
					list($key, $val) = array_map('trim', explode(':', $line, 2));
					if (strtolower($key) ==  strtolower("FAX Statistics"))
					{
						// $moduleSec = $key;
						$moduleSec = "";
					}
					else
					{
						if ( empty($moduleSec))
						{
							$data_cmd['global'][$key] = $val;
						}
						else
						{
							$data_cmd['modules'][$moduleSec][$key] = $val;
						}
						
					}
				}
			}
			$data_return = $data_cmd;
		}
		return $data_return;
	}

}