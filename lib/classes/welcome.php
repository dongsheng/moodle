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
 * This file contains the core_welcome class
 *
 * @package    core
 * @copyright  2021 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

/**
 * This Class contains helper functions for welcome message.
 *
 * @copyright  2021 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_welcome {
    /**
     * @var string User preference key to save sesskey
     */
    protected const USER_PREFERENCE_NAME = 'core_welcome_sesskey';

    /**
     * Displays welcome message.
     */
    public static function display_welcome_message(): void {
        global $USER;
        $currentsesskey = sesskey();
        if (!isloggedin() || isguestuser()) {
            return;
        }
        $welcomemessage = null;
        $savedsession = get_user_preferences(self::USER_PREFERENCE_NAME);
        $fullname = fullname($USER);

        if (empty($savedsession)) {
            $welcomemessage = get_string('welcometosite', 'moodle', $fullname);
        } else if ($savedsession != $currentsesskey) {
            $welcomemessage = get_string('welcomeback', 'moodle', $fullname);
        }
        if (!empty($welcomemessage)) {
            echo html_writer::tag('h2', $welcomemessage, ['class' => 'mb-3']);
            set_user_preferences([self::USER_PREFERENCE_NAME => $currentsesskey]);
        }
    }
}
