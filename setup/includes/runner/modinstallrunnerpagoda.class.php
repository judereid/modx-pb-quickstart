<?php
require_once strtr(realpath(MODX_SETUP_PATH.'includes/runner/modinstallrunner.class.php'),'\\','/');
/**
 * @package modx
 * @subpackage setup
 */
class modInstallRunnerPagoda extends modInstallRunner {

    public function initialize() {
        /* set the time limit infinite in case it takes a bit
         * TODO: fix this by allowing resume when it takes a long time
         */
        @ set_time_limit(0);
        @ ini_set('max_execution_time', 240);
        @ ini_set('memory_limit','128M');
    }
    /**
     * Execute the installation process.
     *
     * @param integer $mode The install mode.
     * @return array An array of result messages collected during execution.
     */
    public function execute($mode) {
        $mode = 1;
        /* get connection */
        $this->install->getConnection($mode);

        /* run appropriate database routines */
        $modx =& $this->install->xpdo;
        $install =& $this->install;
        include MODX_SETUP_PATH . 'includes/tables_create.php';

        if ($this->install->xpdo) {
            if (!$this->installPackage()) {
                return $this->getResults();
            }

            $this->updateWorkspace();

            $modx =& $this->install->xpdo;
            $settings =& $this->install->settings;
            $install =& $this->install;

            include MODX_SETUP_PATH.'includes/new.install.php';

            $this->postRun();

            $this->success = true;
        }

        return $this->getResults();
    }

    /**
     * Do post-run cleanups
     * @return void
     */
    public function cleanup() {
        /* empty sessions table to prevent old permissions from loading */
        $tableName = $this->install->xpdo->getTableName('modSession');
        $this->install->xpdo->exec($this->install->driver->truncate($tableName));

        /* clear cache */
        $this->install->xpdo->cacheManager->deleteTree(MODX_CORE_PATH.'cache/',array(
            'skipDirs' => false,
            'extensions' => array(
                '.cache.php',
                '.tpl.php',
            ),
        ));

        $this->install->settings->store(array(
            'finished' => true,
        ));
    }

    public function postRun() {
        $compressJs = $this->install->settings->get('compress_js');
        if ($compressJs === 0) {
            /** @var modSystemSetting $setting */
            $setting = $this->install->xpdo->getObject('modSystemSetting',array(
                'key' => 'compress_js',
            ));
            if (empty($setting)) {
                $setting = $this->install->xpdo->newObject('modSystemSetting');
                $setting->fromArray(array(
                    'key' => 'compress_js',
                    'xtype' => 'combo-boolean',
                    'namespace' => 'core',
                    'area' => 'manager',
                ),'',true);
            }
            $setting->set('value',0);
            $setting->save();
        }
        $compressCss = $this->install->settings->get('compress_css');
        if ($compressCss === 0) {
            /** @var modSystemSetting $setting */
            $setting = $this->install->xpdo->getObject('modSystemSetting',array(
                'key' => 'compress_css',
            ));
            if (empty($setting)) {
                $setting = $this->install->xpdo->newObject('modSystemSetting');
                $setting->fromArray(array(
                    'key' => 'compress_css',
                    'xtype' => 'combo-boolean',
                    'namespace' => 'core',
                    'area' => 'manager',
                ),'',true);
            }
            $setting->set('value',0);
            $setting->save();
        }
        return true;
    }
    
    public function writeConfig() {
        return true;
    }
}
