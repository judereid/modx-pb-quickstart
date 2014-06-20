<?php
/**
 * Runs tests on the server to determine if MODX can be installed
 *
 * @package setup
 * @subpackage tests
 */
require('modinstalltest.class.php');
class modInstallTestPagoda extends modInstallTest {

    protected function _checkConfig() {
        $this->pass('config_writable');
    }

}