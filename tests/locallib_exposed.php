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
 * Exposed class for PHPUnit tests, protected functions in locallib.php could be called
 *
 * @package   enrol_evento
 * @copyright 2018 HTW Chur Thomas Wieling
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . '/enrol/evento/locallib.php';



/*Just for Testcase*/

class enrol_evento_user_sync_exposed extends enrol_evento_user_sync
{
    public function get_user_exposed($eventopersonid, $isstudent=true, $username=null) {
        return parent::get_user($eventopersonid, $isstudent = true, $username = null);
    }
    public function get_ad_user_exposed($eventopersonid, $isstudent=null) {
        return parent::get_ad_user($eventopersonid, $isstudent = null);
    }
    public function get_users_by_eventoid_exposed($eventopersonid, $isstudent=null) {
        return parent::get_users_by_eventoid($eventopersonid, $isstudent = null);
    }
    public function get_eventoid_by_userid_exposed($userid) {
        return parent::get_eventoid_by_userid($userid);
    }
    public function get_user_by_username_exposed($username) {
        return parent::get_user_by_username($username);
    }
    public function update_student_enrolment_exposed($eventopersonid, $eventoenrolstate, $instance) {

        $now = time();
        $this->timestart = make_timestamp(date('Y', $now), date('m', $now), date('d', $now), 0, 0, 0);
        $this->timeend = 0;
        $this->trace = new null_progress_trace();
        return parent::update_student_enrolment($eventopersonid, $eventoenrolstate, $instance);
    }
    public function enrol_teacher_exposed($eventopersonid, $instance) {
        $now = time();
        $this->timestart = make_timestamp(date('Y', $now), date('m', $now), date('d', $now), 0, 0, 0);
        $this->timeend = 0;
        $this->trace = new null_progress_trace();
        return parent::enrol_teacher($eventopersonid, $instance);
    }
    public function set_user_eventoid_exposed($userid, $eventoid) {
        return parent::set_user_eventoid($userid, $eventoid);
    }
}
