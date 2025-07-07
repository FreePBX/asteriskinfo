<?php

namespace FreePBX\modules\Asteriskinfo\Modules;

require_once 'ModuleBase.php';

class Voicemail extends ModuleBase
{
    public function __construct()
    {
        parent::__construct();
        $this->name    = _('Voicemail');
        $this->nameraw = 'voicemail';

        $this->cmd       = 'voicemail show users';
        $this->cmd_title = _('Voicemail Users');
    }

    public function getByAjax()
    {
        return true;
    }

    public function getDisplay($ajax = false)
    {
        $data_return = '';

        $data_cmd = $this->getVoicemailCmd();
        $rows     = $data_cmd['rows'];

        if ($data_cmd['status'] == false) {
            $data_return = sprintf('<div class="alert alert-danger">%s</div>', $data_cmd['error']);
        } else {
            $data_return = $this->buildDisplay($rows, $ajax);
        }

        return $data_return;
    }

    public function buildDisplay($voicemails = [], $ajax = false)
    {
        $out = null;
        if ($ajax == true) {
            $data_template = [
                'table_id'    => $this->nameraw,
                'module_id'   => $this->nameraw,
                'class_extra' => 'table-asteriskinfo-voicemail',
                'row_style'   => 'modVoicemailRowStyle',
                'url_ajax'    => $this->getAjaxURL(),
                'cols'        => [
                    'context' => [
                        'text'     => _('Dialplan Context'),
                        'class'    => 'col-context',
                        'sortable' => true,
                        'title'    => _('Logical call context to which the voicemail box belongs. Typically “default” if no advanced configuration is used.'),
                    ],
                    'mbox' => [
                        'text'     => _('Mailbox Number'),
                        'class'    => 'col-mbox',
                        'sortable' => true,
                        'title'    => _('The mailbox number, which is usually the same as the user ID.'),
                    ],
                    'user' => [
                        'text'     => _('User ID'),
                        'class'    => 'col-user',
                        'sortable' => true,
                        'title'    => _('The user ID associated with the voicemail box, often the same as the mailbox number.'),
                    ],
                    'zone' => [
                        'text'     => _('Time Zone'),
                        'class'    => 'col-zone',
                        'sortable' => true,
                        'title'    => _('Time zone used for voicemail message timestamps. If empty, the system default time zone is used.'),
                    ],
                    'newmsg' => [
                        'text'      => _('New Voicemail Count'),
                        'class'     => 'text-center col-newmsg',
                        'sortable'  => true,
                        'formatter' => 'modNewMsgFormatter',
                        'title'     => _('The number of new voicemails in the mailbox.'),
                    ],
                ],
                'toolbar' => [
                    [
                        'type'     => 'dropdown-menu',
                        'icon'     => 'fa-filter',
                        'text'     => _('New Messages'),
                        'id'       => 'filter-newmsg-btn',
                        'ul-class' => 'dropdown-menu-filters',
                        'subitems' => [
                            [
                                'text'       => _('With New Messages'),
                                'icon'       => 'fa-envelope text-success',
                                'extra-data' => [
                                    'filterKey' => 'isnewmsg',
                                    'filterVal' => 'yes',
                                    'filterMod' => $this->nameraw,
                                    'filterTab' => $this->nameraw,
                                ],
                            ],
                            [
                                'text'       => _('Without New Messages'),
                                'icon'       => 'fa-envelope-open',
                                'extra-data' => [
                                    'filterKey' => 'isnewmsg',
                                    'filterVal' => 'no',
                                    'filterMod' => $this->nameraw,
                                    'filterTab' => $this->nameraw,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'       => 'button',
                        'icon'       => 'fa-undo',
                        'text'       => _('Clean Filter'),
                        'class'      => 'table-filter-clean-all-btn',
                        'extra-data' => [
                            'filterMod' => $this->nameraw,
                            'filterTab' => $this->nameraw,
                        ],
                    ],
                ],
            ];
            $out = load_view(__DIR__.'/../views/view.asteriskinfo.grid.php', $data_template);
        } else {

        }

        return $out;
    }

    /**
     * Get data via AJAX request
     *
     * This method executes the command to retrieve voicemail users and formats the output.
     * It processes the command output, extracting relevant fields and returning them in a structured format.
     * * @return array An associative array containing the rows of voicemail users and their status.
     *
     * Example input:
     * $this->getOutput('voicemail show users');
     * * The command output is expected to be in the format:
     * ```
     * Context    Mbox  User                      Zone       NewMsg
     * default    6055  6055                                      0
     * default    8055  8055                                      0
     * 2 voicemail users configured.
     * ```
     *
     * Example output:
     * ```
     * [
     *     {
     *         "context": "default",
     *         "mbox": "6055",
     *         "user": "6055",
     *         "zone": "",
     *         "newmsg": "0"
     * *   },
     *     {
     *         "context": "default",
     *         "mbox": "8055",
     *         "user": "8055",
     *         "zone": "",
     *         "newmsg": "0"
     *     }
     * ]
     * ```
     */
    public function getDataAjax()
    {
        $data_cmd = $this->getVoicemailCmd();
        $rows     = $data_cmd['rows'];

        $data_return = [
            'rows'   => [],
            'status' => $data_cmd['status'],
        ];

        if ($data_cmd['status'] == false) {
            $data_return['rows'][]['error'] = $data_cmd['error'];
        } else {
            // Remove last row summarizing the count
            array_pop($rows);

            // Calc the offset of each field based on the header
            $header  = $rows[0];
            $fields  = preg_split('/\s+/', trim($header));
            $offsets = [];

            // Find position of each field in the original line
            foreach ($fields as $field) {
                $pos                         = strpos($header, $field);
                $offsets[strtolower($field)] = $pos;
            }

            // sort the offsets by position
            asort($offsets);

            // Add end to cut the last field
            $positions   = array_values($offsets);
            $positions[] = strlen($header);

            // Process each row, extracting fields based on offsets
            for ($i = 1; $i < count($rows); $i++) {
                $row     = $rows[$i];
                $new_row = [];
                $keys    = array_keys($offsets);
                for ($j = 0; $j < count($keys); $j++) {
                    $start              = $positions[$j];
                    $end                = $positions[$j + 1];
                    $value              = trim(substr($row . str_repeat(' ', $end), $start, $end - $start));
                    $new_row[$keys[$j]] = $value;
                }

                // Convert newmsg to integer and set isnewmsg
                $new_row['newmsg']   = (int) $new_row['newmsg'];
                $new_row['isnewmsg'] = $new_row['newmsg'] == 0 ? 'no' : 'yes';

                // Add the new row to the return data
                $data_return['rows'][] = $new_row;
            }
        }

        return $data_return;
    }

    private function getVoicemailCmd()
    {
        $data_cmd    = $this->getOutput($this->cmd);
        $data_return = [
            'rows'   => [],
            'count'  => 0,
            'status' => true,
            'error'  => '',
        ];

        $rows = explode("\n", str_replace("\r\n", "\n", $data_cmd));
        $rows = array_values(array_filter($rows, fn ($linea) => trim($linea) !== ''));

        $rows_count = count($rows);

        if ($rows_count < 2) {
            $data_return['status'] = false;
            $data_return['error']  = _('Error executing command');
        }

        $data_return['count'] = $rows_count;
        $data_return['rows']  = $rows;

        return $data_return;
    }
}
