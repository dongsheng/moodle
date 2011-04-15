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

/*
 * Handling all ajax request for comments API
 */
define('AJAX_SCRIPT', true);

require_once('../config.php');
require_once($CFG->dirroot . '/comment/lib.php');

$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
$action    = optional_param('action', '', PARAM_ALPHA);

list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_context($context);
$PAGE->set_url('/comment/comment_ajax.php');

if (!confirm_sesskey()) {
    $error = array('error'=>get_string('invalidsesskey'));
    die(json_encode($error));
}

$client_id = required_param('client_id', PARAM_RAW);
$area      = optional_param('area',      '', PARAM_ALPHAEXT);
$commentid = optional_param('commentid', -1, PARAM_INT);
$content   = optional_param('content',   '', PARAM_RAW);
$itemid    = optional_param('itemid',    '', PARAM_INT);
$page      = optional_param('page',      0,  PARAM_INT);
$component = optional_param('component', '',  PARAM_ALPHAEXT);

// initilising comment object
$args = new stdClass;
$args->context   = $context;
$args->course    = $course;
$args->cm        = $cm;
$args->area      = $area;
$args->itemid    = $itemid;
$args->client_id = $client_id;
$args->component = $component;
$manager = new comment($args);

echo $OUTPUT->header(); // send headers

// process ajax request
switch ($action) {
    case 'add':
        if ($manager->can_post()) {
            $result = $manager->add($content);
            if (!empty($result) && is_object($result)) {
                $result->count = $manager->count();
                $result->client_id = $client_id;
                echo json_encode($result);
                die();
            }
        }
        break;
    case 'delete':
        if ($manager->can_delete()) {
            $result = $manager->delete($commentid);
            if ($result === true) {
                echo json_encode(array('client_id'=>$client_id, 'commentid'=>$commentid));
                die();
            }
        }
        break;
    case 'get':
    default:
        if ($manager->can_view()) {
            $result = array();
            $comments = $manager->get_comments($page);
            $result['list'] = $comments;
            $result['count'] = $manager->count();
            $result['pagination'] = $manager->get_pagination($page);
            $result['client_id']  = $client_id;
            echo json_encode($result);
            die();
        }
        break;
}

if (!isloggedin()) {
    // tell user to log in to view comments
    echo json_encode(array('error'=>'require_login'));
}
// ignore request
die;