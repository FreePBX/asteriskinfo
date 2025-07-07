<?php

namespace FreePBX\modules\Asteriskinfo\Modules;

class ModuleBase
{
    public $name      = '';
    public $nameraw   = '';
    public $cmd       = '';
    public $cmd_title = '';

    protected $freepbx;
    protected $config;
    protected $astman;
    protected $asteriskinfo;

    protected $ariPassword  = '';
    protected $ariUser      = '';
    protected $httpprefix   = '';
    protected $httpbindport = '';
    protected $httpbindaddr = '';

    public function __construct()
    {
        $this->freepbx      = \FreePBX::Create();
        $this->config       = $this->freepbx->Config;
        $this->astman       = $this->freepbx->astman;
        $this->asteriskinfo = $this->freepbx->Asteriskinfo;

        $this->ariPassword  = $this->config->get('FPBX_ARI_PASSWORD');
        $this->ariUser      = $this->config->get('FPBX_ARI_USER');
        $this->httpprefix   = $this->config->get('HTTPPREFIX');
        $this->httpbindport = $this->config->get('HTTPBINDPORT');
        $this->httpbindaddr = $this->config->get('HTTPBINDADDRESS');
    }

    /**
     * Get the name of the module.
     * This method retrieves the name of the module.
     * If the name is not set, it defaults to the class name.
     * @return string The name of the module, or the class name if not set.
     * @deprecated Use the `\FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getName()` method instead.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getName()
     * @example
     * $moduleName = $this->getName();
     * // Returns the name of the module, or the class name if not set.
     */
    public function getName()
    {
        $data_return = '';
        if (! empty($this->name)) {
            $data_return = $this->name;
        } else {
            $this_class  = static::class;
            $data_return = substr($this_class, (strrpos($this_class, '\\') ?: -1) + 1) ;
        }

        return $data_return;
    }

    /**
     * Get Output from Asterisk CLI.
     * This method retrieves the output of a command executed in the Asterisk CLI.
     * It uses the Asteriskinfo service to execute the command and return the output.
     * @param string $cmd The command to execute in the Asterisk CLI.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getOutput()
     * @example
     * $output = $this->getOutput('core show version');
     * // Returns the output of the command executed in the Asterisk CLI.
     * @throws \Exception If the command execution fails or if there is an error retrieving the output.
     * @return string The output of the command executed in the Asterisk CLI.
     */
    public function getOutput($cmd)
    {
        return $this->asteriskinfo->getOutput($cmd);
    }

    /**
     * Get a panel with the data.
     * This method formats the provided data into a panel structure.
     * If a title is provided, it includes the title in the panel header.
     * If no title is provided, it creates a panel without a header.
     * @param string $data The data to display in the panel.
     * @param string $title The title of the panel (optional).
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getPanel()
     * @example
     * $panel = $this->getPanel('This is some data', 'Panel Title');
     * // Returns a formatted HTML panel with the provided data and title.
     */
    public function getPanel($data, $title = '')
    {
        if (empty($title)) {
            return sprintf('<div class="panel panel-default"><div class="panel-body"><pre>%s</pre></div></div>', $data);
        } else {
            return sprintf('<div class="panel panel-default"><div class="panel-heading">%s</div><div class="panel-body"><pre>%s</pre></div></div>', $title, $data);
        }
    }

    public function getDisplay()
    {
        $output = '';
        if (! empty($this->cmd)) {
            $data   = $this->getOutput($this->cmd);
            $output = $this->getPanel($data, $this->cmd_title);
        }

        return $output;
    }

    /**
     * Check if a specific Asterisk module is loaded.
     * This method checks if a given module is loaded in Asterisk by executing a command
     * to show the module and checking the output for loaded modules.
     * @param string $module The name of the module to check.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::checkModuleLoad()
     * @example
     * $isModuleLoaded = $this->checkModuleLoad('chan_sip.so');
     * // Returns true if the module is loaded, false otherwise.
     * @throws \Exception If the command execution fails or if there is an error retrieving the output.
     * @throws \Exception If the command execution fails or if there is an error retrieving the output.
     * @return bool Returns true if the module is loaded, false otherwise.
     * @return bool True if the module is loaded, false otherwise.
     */
    public function checkModuleLoad($module)
    {
        $cmd_check = sprintf('module show like %s', $module);
        $mod_check = $this->astman->send_request('Command', ['Command' => $cmd_check]);
        $mod_load  = preg_match('/[1-9] modules loaded/', (string) $mod_check['data']);

        return (bool) $mod_load;
    }

    /**
     * Indicate if the module can be accessed via AJAX.
     * This method is used to determine if the module supports AJAX requests.
     * It returns false by default, indicating that the module does not support AJAX.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getByAjax()
     * @example
     * $canAccessByAjax = $this->getByAjax();
     * // Returns false, indicating that the module does not support AJAX access.
     */
    public function getByAjax()
    {
        return false;
    }

    public function getDataAjax()
    {
        return [];
    }

    /**
     * Check if the Asterisk REST Interface (ARI) is enabled.
     * This method checks the configuration file for ARI settings
     * and determines if ARI is enabled based on the 'enabled' key.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::checkARIStatus()
     * @example
     * $isARIEnabled = $this->checkARIStatus();
     * // Returns true if ARI is enabled, false otherwise.
     * @throws \Exception If the configuration file cannot be read or parsed.
     * @return bool Returns true if ARI is enabled, false otherwise.
     * @return bool True if ARI is enabled, false otherwise.
     */
    public function checkARIStatus()
    {
        $status    = false;
        $dir       = $this->config->get('ASTETCDIR');
        $file_conf = sprintf('%s/ari_general_additional.conf', $dir);

        if (file_exists($file_conf)) {
            $contents = file_get_contents($file_conf);
            $lines    = parse_ini_string($contents, INI_SCANNER_RAW);
            if (isset($lines['enabled']) && $lines['enabled']) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * Get information from the Asterisk REST Interface (ARI) API.
     *
     * This method retrieves data from the ARI API using the provided endpoint.
     * It checks if the ARI module is loaded and if the ARI is enabled.
     * If the ARI is not loaded or enabled, it returns an error message.
     * If the ARI is loaded and enabled, it constructs the API URL and retrieves the data.
     * If the API call fails, it returns an error message.
     * @param string $api The ARI API endpoint to call.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getARIInfoApi()
     * @example
     * $ariData = $this->getARIInfoApi('channels');
     * // Returns an array with 'status' => true/false, 'error' => 'error message', and 'data' => API response data.
     * @throws \JsonException If the JSON decoding fails.
     * @throws \Exception If the ARI API call fails or if the ARI module is not loaded or enabled.
     * @return array An associative array containing the status, error message (if any), and the data retrieved from the ARI API.
     */
    protected function getARIInfoApi($api)
    {
        $data_return = ['status' => true, 'error' => '', 'data' => []];

        $data = $this->getOutput('ari show status');
        if (preg_match('(No such command)', (string) $data) === 1) {
            $data_return['status'] = false;
            $data_return['error']  = _('The Asterisk REST Interface Module is not loaded in asterisk');
        } else {
            $status = $this->checkARIStatus();
            if (!$status) {
                $data_return['status'] = false;
                $data_return['error']  = _('The Asterisk REST Interface is Currently Disabled.');
            } else {
                $prefix = (!empty($this->httpprefix)) ? '/'.$this->httpprefix : '';
                $host   = (!empty($this->httpbindaddr) && $this->httpbindaddr != '::') ? $this->httpbindaddr : 'localhost';
                $url    = sprintf('http://%s:%s@%s:%s%s/%s', $this->ariUser, $this->ariPassword, $host, $this->httpbindport, $prefix, $api);

                $result = @file_get_contents($url);
                if ($result === false) {
                    $data_return['status'] = false;
                    $data_return['error']  = _('The Asterisk REST Interface is not able to connect please check configuration in advanced settings.');
                } else {
                    $data_return['data'] = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
                }
            }
        }

        return $data_return;
    }

    /**
     * Get the URL for AJAX requests.
     *
     * This method constructs the URL for AJAX requests to the Asteriskinfo module.
     * It uses the module name provided or defaults to the current module's name.
     * @param string $module The name of the module for which to get the AJAX URL.
     * @return string The constructed AJAX URL.
     * @see \FreePBX\modules\Asteriskinfo\Modules\ModuleBase::getAjaxURL()
     * @example
     * $ajaxUrl = $this->getAjaxURL('myModule');
     * // Returns: 'ajax.php?module=asteriskinfo&command=getGrid&module_info=myModule'
     */
    protected function getAjaxURL($module = '')
    {
        if (empty($module)) {
            $module = $this->nameraw;
        }
        $module = strtolower(trim($module));
        $url    = sprintf('ajax.php?module=asteriskinfo&command=getGrid&module_info=%s', $module);

        return $url;
    }
}
