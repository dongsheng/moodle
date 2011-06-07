<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Return token
 * @package    moodlecore
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');

$username = required_param('username', PARAM_USERNAME); 
$password = required_param('password', PARAM_RAW);
$service  = required_param('service',  PARAM_ALPHANUMEXT);

try {
    $username = trim(moodle_strtolower($username));
    $user = authenticate_user_login($username, $password);
    if (!empty($user)) {
        if (isguestuser($user)) {
            throw new moodle_exception('noguest');
        }
        if (empty($user->confirmed)) {
            throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
        }
        $sql = "SELECT t.*
                  FROM {external_tokens} t
                  JOIN {external_services} s
                       ON t.externalserviceid = s.id
                 WHERE s.shortname = ?
                   AND t.userid = ?";
        // will throw exception if no token found
        $token = $DB->get_record_sql($sql, array($service, $user->id), MUST_EXIST );
        if ($token->validuntil and $token->validuntil < time()) {
            add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('invalidtimedtoken', 'webservice'), 0);
            // delete token if expired
            $DB->delete_records('external_tokens', array('token'=>$token->token));
            throw new moodle_exception('invalidtimedtoken', 'webservice');
        }

        if ($token->iprestriction and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
            add_to_log(SITEID, 'webservice', get_string('tokenauthlog', 'webservice'), '' , get_string('invalidiptoken', 'webservice').": ".getremoteaddr(), 0);
            throw new moodle_exception('invalidiptoken', 'webservice');
        }

        // log token access
        $DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

        $user_token = new stdClass;
        $user_token->token = $token->token;
        echo json_encode($user_token);
    } else {
        throw new moodle_exception('usernamenotfound', 'moodle');
    }
} catch (Exception $ex) {
    $info = get_exception_info($ex);
    $e = new stdClass();
    $e->error      = $info->message;
    $e->stacktrace = NULL;
    $e->debuginfo  = NULL;
    if (!empty($CFG->debug) and $CFG->debug >= DEBUG_DEVELOPER) {
        if (!empty($info->debuginfo)) {
            $e->debuginfo = $info->debuginfo;
        }
        if (!empty($info->backtrace)) {
            $e->stacktrace = format_backtrace($info->backtrace, true);
        }
    }
    @header('Content-Type: application/json; charset=utf-8');
    echo json_encode($e);
}
