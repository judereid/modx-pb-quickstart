<?php
/* do a little bit of environment cleanup if possible */
@ ini_set('magic_quotes_runtime', 0);
@ ini_set('magic_quotes_sybase', 0);
@ ini_set('opcache.revalidate_freq', 0);

/* start session */
session_start();

$errors = array();
if (!empty($_POST)) {
    $generate_file = true;
    if (!isset($_POST['cmsadmin']) || empty($_POST['cmsadmin'])) {
        $generate_file = false;
    }

    if (!isset($_POST['cmspassword']) || empty($_POST['cmspassword'])) {
        $errors[] = 'Please enter a password';
        $generate_file = false;
    }

    if ($_POST['cmspassword'] != $_POST['cmspasswordconfirm']) {
        $errors[] = 'Passwords do not match';
        $generate_file = false;
    }

    if (!isset($_POST['cmsadminemail']) || !filter_var($_POST['cmsadminemail'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please supply a valid email address';
        $generate_file = false;
    }

    if ($generate_file) {
        $tpl = "<modx>
            <database_type>mysql</database_type>
            <database_server>" .  $_SERVER["DB1_HOST"] . "</database_server>
            <database>" . $_SERVER["DB1_NAME"] . "</database>
            <database_user>" . $_SERVER["DB1_USER"] . "</database_user>
            <database_password>" . $_SERVER["DB1_PASS"] . "</database_password>
            <database_connection_charset>utf8</database_connection_charset>
            <database_charset>utf8</database_charset>
            <database_collation>utf8_general_ci</database_collation>
            <table_prefix>modx_</table_prefix>
            <https_port>443</https_port>
            <http_host>localhost</http_host>
            <cache_disabled>0</cache_disabled>

            <inplace>0</inplace>
            <unpacked>1</unpacked>

            <language>" . $_POST['language']. "</language>

            <cmsadmin>" . $_POST['cmsadmin'] . "</cmsadmin>
            <cmspassword>" . $_POST['cmspassword'] . "</cmspassword>
            <cmsadminemail>" . $_POST['cmsadminemail'] . "</cmsadminemail>

            <core_path>" . dirname(dirname(__FILE__)) . "/core/</core_path>

            <context_mgr_path>" . dirname(dirname(__FILE__)) . "/manager/</context_mgr_path>
            <context_mgr_url>/manager/</context_mgr_url>
            <context_connectors_path>" . dirname(dirname(__FILE__)) . "/connectors/</context_connectors_path>
            <context_connectors_url>/connectors/</context_connectors_url>
            <context_web_path>" . dirname(dirname(__FILE__)) . "/</context_web_path>
            <context_web_url>/</context_web_url>

            <remove_setup_directory>1</remove_setup_directory>
        </modx>";
        file_put_contents('config.xml', $tpl);

        $setupPath= strtr(realpath(dirname(__FILE__)), '\\', '/') . '/';
        define('MODX_SETUP_PATH', $setupPath);
        $installPath= strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/';
        define('MODX_INSTALL_PATH', $installPath);

        /* check for compatible PHP version */
        define('MODX_SETUP_PHP_VERSION', phpversion());
        $php_ver_comp = version_compare(MODX_SETUP_PHP_VERSION, '5.1.1');
        if ($php_ver_comp < 0) {
            die('<html><head><title></title></head><body><h1>FATAL ERROR: MODX Setup cannot continue.</h1><p>Wrong PHP version! You\'re using PHP version '.MODX_SETUP_PHP_VERSION.', and MODX requires version 5.1.1 or higher.</p></body></html>');
        }

        /* make sure json extension is available */
        if (!function_exists('json_encode')) {
            die('<html><head><title></title></head><body><h1>FATAL ERROR: MODX Setup cannot continue.</h1><p>MODX requires the PHP JSON extension! You\'re PHP configuration at version '.MODX_SETUP_PHP_VERSION.' does not appear to have this extension enabled. This should be a standard extension on PHP 5.2+; it is available as a PECL extension in 5.1.</p></body></html>');
        }

        /* make sure date.timezone is set for PHP 5.3.0+ users */
        if (version_compare(MODX_SETUP_PHP_VERSION,'5.3.0') >= 0) {
            $phptz = @ini_get('date.timezone');
            if (empty($phptz)) {
                die('<html><head><title></title></head><body><h1>FATAL ERROR: MODX Setup cannot continue.</h1><p>To use PHP 5.3.0+, you must set the date.timezone setting in your php.ini. Please do set it to a proper timezone before proceeding. A list can be found <a href="http://us.php.net/manual/en/timezones.php">here</a>.</p></body></html>');
            }
        }

        if (!include(MODX_SETUP_PATH . 'includes/config.core.php')) {
            die('<html><head><title></title></head><body><h1>FATAL ERROR: MODX Setup cannot continue.</h1><p>Make sure you have uploaded all of the setup/ files; your setup/includes/config.core.php file is missing.</p></body></html>');
        }
        if (!include(MODX_SETUP_PATH . 'includes/modinstall.class.php')) {
            die('<html><head><title></title></head><body><h1>FATAL ERROR: MODX Setup cannot continue.</h1><p>Make sure you have uploaded all of the setup/ files; your setup/includes/modinstall.class.php file is missing.</p></body></html>');
        }

        $modInstall = new modInstall();
        if ($modInstall->getService('lexicon','modInstallLexicon')) {
            $modInstall->lexicon->load('default');
        }
        $modInstall->findCore();
        $modInstall->doPreloadChecks();
        $requestClass = 'request.modInstallPagodaRequest';
        $modInstall->getService('request',$requestClass);
        echo $modInstall->request->handle();
        exit();
    }
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>MODX Revolution 2.2.14-pl &raquo; Install</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="assets/css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="assets/css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="assets/css/960.css" />

    <link rel="stylesheet" href="assets/modx.css" type="text/css" media="screen" />

    <link rel="stylesheet" href="assets/css/print.css" type="text/css" media="print" />
    
    <link href="assets/css/style.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="assets/js/ext-core.js"></script>
    <script type="text/javascript" src="assets/js/modx.setup.js"></script>
    <!--[if lt IE 7]>
    
        <script type="text/javascript" src="assets/js/inc/say.no.to.ie.6.js"></script>
        <style type="text/css">
        body {
            behavior:url("assets/js/inc/csshover2.htc");
        }
        .pngfix {
            behavior:url("assets/js/inc/iepngfix.htc");
        }
        </style>
        
        <![endif]-->

    </head>

    <body>
        <!-- start header -->
        <div id="header">
            <div class="container_12">
                <div id="metaheader">
                    <div class="grid_6">
                        <div id="mainheader">
                            <h1 id="logo" class="pngfix"><span>MODX</span></h1>
                        </div>
                    </div>
                    <div id="metanav" class="grid_6">
                        <a href="#"><strong>MODX Revolution</strong>&nbsp;<em>version 2.2.14-pl</em></a>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        </div>
        <!-- end header -->

        <div id="contentarea">
            <div class="container_16">
             <!-- start content -->
             <div id="content" class="grid_12">

                <form id="install" action="?" method="post">
                    <div id="modx-db" class="modx-hidden" style="visibility: visible;">
                        <p class="title">Choose Language:
                            <select name="language">
                                <option value="cs">cs</option>
                                <option value="de">de</option>
                                <option value="en" selected="selected">en</option>
                                <option value="es">es</option>
                                <option value="fr">fr</option>
                                <option value="it">it</option>
                                <option value="ja">ja</option>
                                <option value="nl">nl</option>
                                <option value="ru">ru</option>
                                <option value="sv">sv</option>
                                <option value="th">th</option>
                            </select>
                        </p>
                        <p class="title">Default Admin User</p>
                        <p>Now you'll need to enter some details for the main administrator account. You can fill in your own name here, and a password you're not likely to forget. You'll need these to log into Admin once setup is complete.</p>

                        <?php foreach ($errors as $error) : ?>
                            <p id="modx-db-step2-msg" class="modx-hidden2 error"><span class="result"><?php echo $error; ?></span></p>
                        <?php endforeach; ?>
                        <div class="labelHolder">
                            <label for="cmsadmin">Administrator username:</label>
                            <input type="text" name="cmsadmin" id="cmsadmin" value="<?= isset($_POST['cmsadmin']) ? $_POST['cmsadmin'] : ''; ?>">
                            &nbsp;<span class="field_error" id="cmsadmin_error"></span>
                        </div>
                        <div class="labelHolder">
                            <label for="cmsadminemail">Administrator email:</label>
                            <input type="text" name="cmsadminemail" id="cmsadminemail" value="<?= isset($_POST['cmsadminemail']) ? $_POST['cmsadminemail'] : ''; ?>">
                            &nbsp;<span class="field_error" id="cmsadminemail_error"></span>
                        </div>
                        <div class="labelHolder">
                            <label for="cmspassword">Administrator password:</label>
                            <input type="password" id="cmspassword" name="cmspassword" value="">
                            &nbsp;<span class="field_error" id="cmspassword_error"></span>
                        </div>
                        <div class="labelHolder">
                            <label for="cmspasswordconfirm">Confirm password:</label>
                            <input type="password" id="cmspasswordconfirm" name="cmspasswordconfirm" value="">
                            &nbsp;<span class="field_error" id="cmspasswordconfirm_error"></span>
                        </div>
                    </div>
                    <br />
                    <div class="setup_navbar">
                        <input type="submit" name="proceed" value="Go">
                    </div>
                </form>        
            </div>
            <!-- end content -->
            <div class="clear">&nbsp;</div>
        </div>
    </div>

    <!-- start footer -->
    <div id="footer">
        <div id="footer-inner">
            <div class="container_12">
                <p>&copy; 2005-2014 the <a href="http://www.modx.com/" onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;">MODX</a> Content Management Framework (CMF) project. All rights reserved. MODX is licensed under the GNU GPL.</p>
                <p>MODX is free software.  We encourage you to be creative and make use of MODX in any way you see fit. Just make sure that if you do make changes and decide to redistribute your modified MODX, that you keep the source code free!</p>
            </div>
        </div>
    </div>

    <div class="post_body">

    </div>
    <!-- end footer -->
</body>
</html>
<? } ?>