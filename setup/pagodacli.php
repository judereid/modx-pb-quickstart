<?php
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