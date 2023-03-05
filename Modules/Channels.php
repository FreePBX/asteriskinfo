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
		if ($ajax == true)
		{
			$data_template = array(
				'table_id' 	  => $this->nameraw,
				'module_id'   => $this->nameraw,
				'class_extra' => 'table-asteriskinfo-channels',
				'row_style'	  => 'modChannelsRowStyle',
				'url_ajax'	  => sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $this->nameraw),
				'cols' => array(
					'state' => array(
						'text' 	    => _("Status"),
						'class'     => 'text-center col-status',
						'sortable'  => true,
						'formatter' => 'modChannelsStatusFormatter'
					),
					'technology' => array(
						'text' 	   => _("Tech"),
						'class'     => 'col-tecnology',
						'sortable' => true,
					),
					'resource' => array(
						'text' 	   => _("Resource"),
						'class'     => 'col-resource',
						'sortable' => true,
					),
					'channel_count' => array(
						'text' 	   => _("Channel Count"),
						'class'     => 'text-center col-channel_count',
						'sortable' => true,
					),
				),
				'toolbar' => array(
					array(
						'type' 	   => 'dropdown-menu',
						'icon' 	   => 'fa-filter',
						'text' 	   => _("Status"),
						'id'   	   => 'filter-status-channels-btn',
						'ul-class' => 'dropdown-menu-filters',
						'subitems' => array(
							array(
								'text' 		 => _("Online"),
								'icon' 		 => 'fa-check',
								'extra-data' => array(
									'filterKey' => 'state',
									'filterVal' => 'online',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Offline"),
								'icon' 		 => 'fa-times',
								'extra-data' => array(
									'filterKey' => 'state',
									'filterVal' => 'offline',
									'filterMod' => $this->nameraw,
									'filterTab' => $this->nameraw,
								),
							),
							array(
								'text' 		 => _("Unknown"),
								'icon' 		 => 'fa-question',
								'extra-data' => array(
									'filterKey' => 'state',
									'filterVal' => 'unknown',
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
			foreach($data_ari['data'] as $row)
			{
				$row['channel_count']  = count($row['channel_ids']);
				$data_return['rows'][] = $row;
			}
		}
		return $data_return;
	}
}