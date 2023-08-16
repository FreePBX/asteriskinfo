<?php
namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Channels extends ModuleBase
{
	public function __construct()
	{
		parent::__construct();
		$this->name 	= _("Channels");
		$this->nameraw  = "channels";
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
			$data_template = ['table_id' 	  => $this->nameraw, 'module_id'   => $this->nameraw, 'class_extra' => 'table-asteriskinfo-channels', 'row_style'	  => 'modChannelsRowStyle', 'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw), 'cols' => ['state' => ['text' 	    => _("Status"), 'class'     => 'text-center col-status', 'sortable'  => true, 'formatter' => 'modChannelsStatusFormatter'], 'technology' => ['text' 	   => _("Tech"), 'class'     => 'col-tecnology', 'sortable' => true], 'resource' => ['text' 	   => _("Resource"), 'class'     => 'col-resource', 'sortable' => true], 'channel_count' => ['text' 	   => _("Channel Count"), 'class'     => 'text-center col-channel_count', 'sortable' => true]], 'toolbar' => [['type' 	   => 'dropdown-menu', 'icon' 	   => 'fa-filter', 'text' 	   => _("Status"), 'id'   	   => 'filter-status-channels-btn', 'ul-class' => 'dropdown-menu-filters', 'subitems' => [['text' 		 => _("Online"), 'icon' 		 => 'fa-check', 'extra-data' => ['filterKey' => 'state', 'filterVal' => 'online', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Offline"), 'icon' 		 => 'fa-times', 'extra-data' => ['filterKey' => 'state', 'filterVal' => 'offline', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]], ['text' 		 => _("Unknown"), 'icon' 		 => 'fa-question', 'extra-data' => ['filterKey' => 'state', 'filterVal' => 'unknown', 'filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]]]], ['type' 		 => 'button', 'icon' 		 => 'fa-undo', 'text'		 => _("Clean Filter"), 'class' 	 => 'table-filter-clean-all-btn', 'extra-data' => ['filterMod' => $this->nameraw, 'filterTab' => $this->nameraw]]]];
			$out = load_view(__DIR__.'/../views/view.asteriskinfo.grid.php', $data_template);
		}
		else
		{
			
		}
		return $out;
	}

	public function getARIInfo()
	{
		return parent::getARIInfoApi('ari/endpoints');
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
			foreach($data_ari['data'] as $row)
			{
				$row['channel_count']  = is_countable($row['channel_ids']) ? count($row['channel_ids']) : 0;
				$data_return['rows'][] = $row;
			}
		}
		return $data_return;
	}
}