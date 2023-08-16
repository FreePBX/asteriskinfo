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
		$out = null;
  if ($ajax == true)
		{
			$data_template = ['table_id' 	  => $this->nameraw, 'module_id'   => $this->nameraw, 'class_extra' => 'table-asteriskinfo-modules', 'row_style'	  => 'modModulesRowStyle', 'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw), 'cols' => ['status' => ['text' 	   => _("Status"), 'class'     => 'text-center col-status', 'sortable' => true, 'formatter' => 'modModulesStatusFormatter'], 'name' => ['text' 	    => _("Name"), 'class'     => 'col-name', 'sortable'  => true], 'description' => ['text' 	   => _("Description"), 'class'     => 'col-description', 'sortable' => true], 'use_count' => ['text' 	   => _("Use Count"), 'class'     => 'text-center col-use_count', 'sortable' => true], 'support_level' => ['text' 	   => _("Support Level"), 'class'     => 'text-center col-support_level', 'sortable' => true]], 'toolbar' => [['type' 	   => 'dropdown-menu', 'icon' 	   => 'fa-filter', 'text' 	   => _("Status"), 'id'   	   => 'filter-status-modules-btn', 'ul-class' => 'dropdown-menu-filters', 'subitems' => [['text' 		 => _("Running"), 'icon' 		 => 'fa-check', 'extra-data' => ['filterKey' => 'status', 'filterVal' => 'Running', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Not Running"), 'icon' 		 => 'fa-times', 'extra-data' => ['filterKey' => 'status', 'filterVal' => 'Not Running', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['type' 		 => 'divider'], ['text' 		 => _("Remove Filter"), 'icon' 		 => 'fa-eraser', 'extra-data' => ['filterKey' => 'status', 'filterVal' => '', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]]]], ['type' 	   => 'dropdown-menu', 'icon' 	   => 'fa-filter', 'text' 	   => _("Support Level"), 'id'   	   => 'filter-support_level-modules-btn', 'ul-class' => 'dropdown-menu-filters', 'subitems' => [['text' 		 => _("Core"), 'icon' 		 => 'fa-asterisk', 'extra-data' => ['filterKey' => 'support_level', 'filterVal' => 'core', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Extended"), 'icon' 		 => 'fa-plug', 'extra-data' => ['filterKey' => 'support_level', 'filterVal' => 'extended', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Deprecated"), 'icon' 		 => 'fa-exclamation', 'extra-data' => ['filterKey' => 'support_level', 'filterVal' => 'deprecated', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Unknown"), 'icon' 		 => ' fa-question', 'extra-data' => ['filterKey' => 'support_level', 'filterVal' => 'unknown', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['type' 		 => 'divider'], ['text' 		 => _("Remove Filter"), 'icon' 		 => 'fa-eraser', 'extra-data' => ['filterKey' => 'support_level', 'filterVal' => '', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]]]], ['type'  => 'button', 'icon'  => 'fa-undo', 'text'  => _("Clean All Filters"), 'class' => 'table-filter-clean-all-btn', 'extra-data' => ['filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]]]];
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
		$data_return = ['rows' 	 => [], 'status' => true];

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