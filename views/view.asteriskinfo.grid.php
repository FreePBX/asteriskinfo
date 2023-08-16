<?php
$toolbar_html = "";
foreach ( $toolbar as $item )
{
	$item_id    = empty($item['id']) ? '' : sprintf('id="%s"', $item['id']);
	$item_class = empty($item['class']) ? '' : $item['class'];
	
	switch ( $item['type'] ) 
	{
		case 'button':
			$item_data = '';
			if (! empty($item['extra-data']) && is_array($item['extra-data']))
			{
				foreach ($item['extra-data'] as $data_key => $data_value)
				{
					$item_data .= sprintf(' data-%s ="%s" ', $data_key, $data_value);
				}
			}
			$toolbar_html .= sprintf('<button type="button" class="btn btn-default %s" %s %s><i class="fa %s fa-fw"></i> %s</button>', $item_class, $item_id, $item_data, $item['icon'], $item['text']);
			break;

		case 'dropdown-menu':
			$item_id_dropdown = empty($item['id']) 		 ? '' : sprintf('dropdown-menu-%s', $item['id']);
			$ul_class 		  = empty($item['ul-class']) ? '' : $item['ul-class'];

			$toolbar_html .= '<div class="btn-group">';
			$toolbar_html .= sprintf('<a class="btn btn-primary %s" data-toggle="dropdown" href="#" %s><i class="fa %s fa-fw"></i> %s</a>', $item_class, $item_id, $item['icon'], $item['text']);
			$toolbar_html .= '<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-caret-down" title="Toggle dropdown menu"></span></a>';
			$toolbar_html .= sprintf('<ul class="dropdown-menu %s %s">', $ul_class, $item_id_dropdown);
			if (! empty($item['subitems']) && is_array($item['subitems']) )
			{
				foreach ($item['subitems'] as $subitem) 
				{
					$subitem_type = empty($subitem['type']) ? '' : $subitem['type'];

					if ($subitem_type == 'divider')
					{
						$toolbar_html .= '<li class="divider"></li>';
					}
					else
					{
						$subitem_id    = sprintf('id="%s"', empty($subitem['id']) ? '' : $subitem['id']);
						$subitem_class = empty($subitem['class']) ? '' : $subitem['class'];
						$subitem_data = '';
						if (! empty($subitem['extra-data']) && is_array($subitem['extra-data']))
						{
							foreach ($subitem['extra-data'] as $data_key => $data_value)
							{
								$subitem_data .= sprintf(' data-%s ="%s" ', $data_key, $data_value);
							}
						}
						$toolbar_html .= sprintf('<li><a href="#" %s><i class="fa %s fa-fw"></i> %s</a></li>', $subitem_data, $subitem['icon'], $subitem['text']);
					}
				}
			}
			$toolbar_html .= '</ul>';
			$toolbar_html .= '</div>';
			break;

		default:

	}
}

$class_extra = empty($class_extra) ? '' : $class_extra;
$row_style 	 = empty($row_style)    ? '' : sprintf('data-row-style="%s"', $row_style);
?>

<div id="toolbar-all-<?php echo $table_id; ?>">
	<?php echo $toolbar_html; ?>
</div>
<table id="<?php echo $table_id; ?>"
	data-url="<?php echo $url_ajax; ?>"
	data-cache="false"
	data-toggle="table"
	data-pagination="true"
	data-search="true"
	data-show-refresh="true"
	data-toolbar="#toolbar-all-<?php echo $table_id; ?>"
	<?php echo $row_style; ?>
	class="table table-striped <?php echo $class_extra; ?>">
	<thead>
		<tr>
			<?php
			foreach ($cols as $col_id => $col_val)
			{
				$oter_opt = "";
				foreach ($col_val as $key => $val)
				{
					switch ($key)
					{
						case "sortable":
							if ($val == true)
							{
								$oter_opt .= ' data-sortable="true" ';
							}
							break;

						case "class":
							if (!empty(trim((string) $val)))
							{
								$oter_opt .= sprintf(' class="%s" ', trim((string) $val));
							}
							break;
					
						case "formatter":
							if (!empty(trim((string) $val)))
							{
								$oter_opt .= sprintf(' data-formatter="%s" ', trim((string) $val));
							}
							break;

						case "width":
							if (!empty(trim((string) $val)))
							{
								$oter_opt .= sprintf(' data-width="%s" ', trim((string) $val));
							}
							break;
							break;
					}
				}
				$out = sprintf('<th data-field="%s" %s>%s</th>', $col_id, $oter_opt, $col_val['text']);
				echo $out;
			}
			?>
		</tr>
	</thead>
</table>