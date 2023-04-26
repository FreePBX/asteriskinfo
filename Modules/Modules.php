<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Modules extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name 	= _("Modules");
		$this->nameraw  = "modules";
	}
	
	public function getDisplay($ajax = false)
	{
		$data_return = "";
		$data_ari	 = $this->getARIInfo();

		if ($data_ari['status'] == false)
		{
			$data_return = sprintf('<div class="alert alert-danger">%s</div>', $data_ari['error']);
		}
		else
		{
			$data_return = $this->buildDisplay($data_ari['data'], $ajax);
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
				'class_extra' => 'table-asteriskinfo-modules',
				'row_style'	  => 'modModulesRowStyle',
				'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw),
				'cols' => array(
					'status' => array(
						'text' 	   => _("Status"),
						'class'     => 'text-center col-status',
						'sortable' => true,
						'formatter' => 'modModulesStatusFormatter'
					),
					'name' => array(
						'text' 	    => _("Name"),
						'class'     => 'col-name',
						'sortable'  => true,
					),
					'description' => array(
						'text' 	   => _("Description"),
						'class'     => 'col-description',
						'sortable' => true,
					),
					'use_count' => array(
						'text' 	   => _("Use Count"),
						'class'     => 'text-center col-use_count',
						'sortable' => true,
					),
					'support_level' => array(
						'text' 	   => _("Support Level"),
						'class'     => 'text-center col-support_level',
						'sortable' => true,
					),
				),
				'toolbar' => array(
					array(
						'type' 	   => 'dropdown-menu',
						'icon' 	   => 'fa-filter',
						'text' 	   => _("Status"),
						'id'   	   => 'filter-status-modules-btn',
						'ul-class' => 'dropdown-menu-filters',
						'subitems' => array(
							array(
								'text' 		 => _("Running"),
								'icon' 		 => 'fa-check',
								'extra-data' => array(
									'filterKey' => 'status',
									'filterVal' => 'Running',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Not Running"),
								'icon' 		 => 'fa-times',
								'extra-data' => array(
									'filterKey' => 'status',
									'filterVal' => 'Not Running',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'type' 		 => 'divider',
							),
							array(
								'text' 		 => _("Remove Filter"),
								'icon' 		 => 'fa-eraser',
								'extra-data' => array(
									'filterKey' => 'status',
									'filterVal' => '',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							)
						),
					),
					array(
						'type' 	   => 'dropdown-menu',
						'icon' 	   => 'fa-filter',
						'text' 	   => _("Support Level"),
						'id'   	   => 'filter-support_level-modules-btn',
						'ul-class' => 'dropdown-menu-filters',
						'subitems' => array(
							array(
								'text' 		 => _("Core"),
								'icon' 		 => 'fa-asterisk',
								'extra-data' => array(
									'filterKey' => 'support_level',
									'filterVal' => 'core',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Extended"),
								'icon' 		 => 'fa-plug',
								'extra-data' => array(
									'filterKey' => 'support_level',
									'filterVal' => 'extended',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Deprecated"),
								'icon' 		 => 'fa-exclamation',
								'extra-data' => array(
									'filterKey' => 'support_level',
									'filterVal' => 'deprecated',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Unknown"),
								'icon' 		 => ' fa-question',
								'extra-data' => array(
									'filterKey' => 'support_level',
									'filterVal' => 'unknown',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'type' 		 => 'divider',
							),
							array(
								'text' 		 => _("Remove Filter"),
								'icon' 		 => 'fa-eraser',
								'extra-data' => array(
									'filterKey' => 'support_level',
									'filterVal' => '',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							)
						),
					),
					array(
						'type'  => 'button',
						'icon'  => 'fa-undo',
						'text'  => _("Clean All Filters"),
						'class' => 'table-filter-clean-all-btn',
						'extra-data' => array(
							'filterMod' => $this->nameraw,
							'filterTab' => $this->nameraw,
						),
					)
				),
			);
			$out = load_view(__DIR__.'/../views/view.asteriskinfo.grid.php', $data_template);
		}
		else
		{
			
		}
		return $out;
	}

	public function getARIInfo()
	{
		return parent::getARIInfoApi('ari/asterisk/modules');
	}

	public function getByAjax()
	{
		return true;
	}

	public function getDataAjax()
	{
		$data_ari	 = $this->getARIInfo();
		$data_return = array(
			'rows' 	 => array(),
			'status' => true,
		);

		if ($data_ari['status'] == false)
		{
			$data_return['status'] 			= false;
			$data_return['rows'][]['error'] = $data_ari['error'];
		}
		else
		{
			$data_return['status'] = true;
			$data_return['rows']   = $data_ari['data'];
		}
		return $data_return;
	}
}