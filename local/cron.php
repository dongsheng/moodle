<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("$CFG->dirroot/config.php");

try{
    mtrace("\n--Moving records from log_temp to log--\n");
    $sql = "INSERT into {$CFG->prefix}log (time, userid, ip, course, module, cmid, action, url, info)
            SELECT lt.time, lt.userid, lt.ip, lt.course, lt.module, lt.cmid, lt.action, lt.url, lt.info
            FROM {$CFG->prefix}log_temp lt";
    execute_sql($sql);
    mtrace("\n--Moved log data to log table--\n");
    mtrace("\n--Cleaning up log_temp table--\n");
    delete_records('log_temp');
    mtrace("\n--done--\n");
} catch(Exception $ex){
    mtrace('Fatal exception from local/cron.php');
    mtrace($ex);
}
