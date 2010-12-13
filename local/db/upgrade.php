<?php
function xmldb_local_upgrade($oldversion) {
    global $CFG, $db;

    $result = true;
    $result = install_from_xmldb_file(dirname(__FILE__).'/install.xml');

    return $result;
}
