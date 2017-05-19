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
 * Evento enrolment plugin local library file.
 *
 * @package    enrol_evento
 * @copyright  2017 HTW Chur Roger Barras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
// require_once($CFG->dirroot . '/local/evento/classes/locallib.php');

/**
 * Name of the user info field for the eventoid
 */
define('ENROL_EVENTO_UIF_EVENTOID', 'eventoid');

/**
 * Sync all evento course links.
 *
 * @param progress_trace $trace
 * @param int $courseid one course, empty mean all
 * @return int 0 means ok, 1 means error, 2 means plugin disabled
 */
function enrol_evento_sync(progress_trace $trace, $courseid = null) {
    global $CFG, $DB;

    try {
        if (!enrol_is_enabled('evento')) {
            $trace->finished();
            return 2;
        }

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Starting user enrolment synchronisation...');

        // Init the time start and end for new enrolments.
        $today = time();
        $timestart = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
        $timeend = 0;

        // Set up a student and a teacher role for use in some tests.
        $eteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));

        $params = array('now1' => $today, 'now2' => $today, 'useractive' => ENROL_USER_ACTIVE, 'courselevel' => CONTEXT_COURSE);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        $instances = array();

        // Selects all active enrolments in active courses.
        // No enrolment if course enddate is reached or the cours is hidden.
        $sql = "SELECT e.*, c.idnumber, cx.id AS contextid
                FROM {enrol} e
                JOIN {context} cx ON (cx.instanceid = e.courseid AND cx.contextlevel = :courselevel)
                JOIN {course} c ON (c.id = e.courseid AND (c.enddate = 0 OR (c.enddate > 0 AND c.enddate < :now1)) AND c.visible = 1)
                WHERE e.enrol = 'evento' AND (e.enrolenddate = 0 OR (e.enrolenddate > 0 AND e.enrolenddate < :now2)) AND e.status = 0
                    $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        // debugging('start', DEBUG_DEVELOPER);

        $evenotservice = new local_evento_evento_service();
        $plugin = enrol_get_plugin('evento');

        // Iterate over each evento enrol instances.
        foreach ($rs as $ce) {
            debugging("enrolid $ce->enrolid");
            if (empty($instances[$ce->id])) {
                $instances[$ce->id] = $DB->get_record('enrol', array('id' => $ce->enrolid));
            }
            $instance = $instances[$ce->id];

            // Get event id and data.
            $event = $evenotservice->get_event_by_number($ce->idnumber);
            // Get event participants enrolments.
            $enrolments = $evenotservice->get_enrolments_by_eventid($event->idAnlass);
            // Get event teacher enrolemnts.

            if (!is_array($enrolments)) {
                // Create an array with one item.
                $enrolments = array(1 => $enrolments);
            }

            // Enrol students.
            foreach ($enrolments as $ee) {
                // Get the moodle user.
                $u = enrol_evento_get_user($evenotservice, $ee->idPerson);

                // Todo move this array to settings.
                // Valid Anlass-Anmeldungen for student enrolment.
                $enrolstateids = array(20208, 20215, 20225, 20240, 20245, 20270, 20275, 20281, 20282, 20284, 20286, 20288);
                // Check enrolment state to enrol or suspend.
                if (in_array($ee->iDPAStatus, $enrolstateids)) {
                    // Enrol.
                    if ($userenrolment = $DB->get_record('user_enrolments', array('enrolid' => $instance->id, 'userid' => $u->id))) {
                        $timestart = $userenrolment->timestart;
                        $timeend = $userenrolment->timeend;
                    }
                    $plugin->enrol_user($instance, $u->id, $studentroleid, $timestart, $timeend, ENROL_USER_ACTIVE);
                    $trace->output("enroling user $u->id in course $instance->courseid as a student", 1);
                } else {
                    // Suspend.
                    $plugin->update_user_enrol($instance, $u->id, ENROL_USER_SUSPENDED);
                    $trace->output("suspending expired user $u->id in course $instance->courseid", 1);
                }

                unset($u);
            }

            // Enrol teachers.
            $eventteachers = array();
            if (!is_array($event->array_EventoAnlassLeitung)) {
                $eventteachers[0] = $event->array_EventoAnlassLeitung;
            } else {
                $eventteachers = $event->array_EventoAnlassLeitung;
            }

            foreach ($eventteachers as $teacher) {
                // Get the moodle user.
                $u = enrol_evento_get_user($evenotservice, $teacher->anlassLtgIdPerson);

                // Check enrolment state to enrol or suspend.
                $userenrolment = $DB->get_record('user_enrolments', array('enrolid' => $instance->id, 'userid' => $u->id));
                if (in_array($ee->iDPAStatus, $enrolstateids)) {
                    // Enrol.
                    if ($userenrolment) {
                        $timestart = $userenrolment->timestart;
                        $timeend = $userenrolment->timeend;
                    }
                    $plugin->enrol_user($instance, $u->id, $studentroleid, $timestart, $timeend, ENROL_USER_ACTIVE);
                    $trace->output("enroling user $u->id in course $instance->courseid as an editingteacher", 1);
                } else {
                    // Suspend idf enrolled.
                    if ($userenrolment) {
                        $plugin->update_user_enrol($instance, $u->id, ENROL_USER_SUSPENDED);
                        $trace->output("suspending expired user $u->id in course $instance->courseid", 1);
                    }
                }
            }
        }
        $rs->close();
        unset($instances);

        $trace->output('...user enrolment synchronisation finished.');
    } catch (Exeption $ex) {
        debugging("Error: $ex->message");
        return 1;
    }
    return 0;
}


/**
 * Obtains the moodle user by an evento id
 *
 * @param local_evento_evento_service $evenotservice
 * @param int $eventopersonid
 * @param int $username
 * @param int $email
 * @param int $firstname
 * @param int $lastname
 * @return a fieldset object for the user
 */
function enrol_evento_get_user($evenotservice, $eventopersonid, $username=null, $email=null, $firstname=null, $lastname=null) {
    global $DB;

    // Todo permission check.
    $u = enrol_evento_get_user_by_eventoid($eventopersonid);

    if (!$u) {
        if (!isset($username) OR !isset($email) OR !isset($firstname) OR !isset($lastname)) {
            $person = $evenotservice->get_person_by_id($eventopersonid);
            // todo get the shibbolet id from $person or ldap
            $username = $eventopersonid; // $person->shibbolethid;
            $email = $person->personeMail;
            $firstname = $person->personVorname;
            $lastname = $person->personNachname;
        }
        $u = enrol_evento_get_user_by_username($username);
        if ($u) {
            enrol_evento_set_user_eventoid($u->id, $eventopersonid);
        }
    }
    if (!$u) {
        // Instead of shibboleth use the email for searching.
        // Remove this if condition if the searching with shibbolethid works.
        $u = $DB->get_record('user', array('email' => $email));
        if ($u) {
            enrol_evento_set_user_eventoid($u->id, $eventopersonid);
        }
    }
    if (!$u) {
        // Create an user.
        $usernew = new stdClass();

        $usernew->auth = ACCOUNT_TYPE;
        // $usernew->username = $person->shibbolethid;
        $usernew->username = $eventopersonid;
        $usernew->email = $email;
        $usernew->firstname = $firstname;
        $usernew->lastname = $lastname;

        $usernew->confirmed = 1;
        $usernew->interests = "";
        // Moodle wants more for valid users.
        $usernew->timecreated = time();

        $usernew->mnethostid = $CFG->mnet_localhost_id; // Always local user.
        $usernew->password = AUTH_PASSWORD_NOT_CACHED;  // Because of Shibboleth.

        // Finally create the user.
        $usernew->id = user_create_user($usernew, false, false);

        enrol_evento_set_user_eventoid($usernew->id, $eventopersonid);
        $u = $DB->get_record('user', array('id' => $usernew->id));
    }

    return $u;
}

/**
 * Obtains the user defined field eventoid if set.
 *
 * @param int $userid
 * @return string eventoid
 */
function enrol_evento_get_user_eventoid($userid) {
    global $DB;

    // Todo permission check.

    $sql = 'SELECT data
        FROM {user_info_data} uid
        INNER JOIN {user_info_field} uif ON uid.fieldid = uif.id
        WHERE uif.shortname = :eventoid
        AND uid.userid = :userid';

    $sqlparams = array('eventoid' => ENROL_EVENTO_UIF_EVENTOID, 'userid' => $userid);

    $data = $DB->get_field_sql($sql, $sqlparams);

    return $data;
}

/**
 * Obtains the user by an eventoid if its set.
 *
 * @param string $eventoid
 * @return a fieldset object for the user
 */
function enrol_evento_get_user_by_eventoid($eventoid) {
    global $DB;

    // Todo permission check.

    $sql = 'SELECT u.*
        FROM {user} u
        INNER JOIN {user_info_data} uid ON uid.userid = u.id
        INNER JOIN {user_info_field} uif ON uid.fieldid = uif.id
        WHERE uif.shortname = :eventoidshortname
        AND uid.data = :eventoid';

    $sqlparams = array('eventoidshortname' => ENROL_EVENTO_UIF_EVENTOID, 'eventoid' => $eventoid);

    $user = $DB->get_record_sql($sql, $sqlparams);

    return $user;
}

/**
 * Obtains the user by username (shibbolethid).
 *
 * @param string $eventoid
 * @return a fieldset object for the user
 */
function enrol_evento_get_user_by_username($username) {
    global $DB;

    // Todo permission check.

    $user = $DB->get_record('user', array('username' => $username));

    return $user;
}

/**
 * Sets or inserts the user defined field eventoid, if it exists.
 *
 * @param int $userid
 * @param int $eventoid
 * @return bool true if set successfully
 */
function enrol_evento_set_user_eventoid($userid, $eventoid) {
    global $DB;

    // Todo permission check.
    $returnvalue = false;

    // Gets an existing user info data eventoid.
    $sql = 'SELECT uid.*
        FROM {user_info_data} uid
        INNER JOIN {user_info_field} uif ON uid.fieldid = uif.id
        WHERE uif.shortname = :eventoid
        AND uid.userid = :userid';

    $sqlparams = array('eventoid' => ENROL_EVENTO_UIF_EVENTOID, 'userid' => $userid);

    $uid = $DB->get_field_sql($sql, $sqlparams);

    if ($uid) {
        // Update.
        $returnvalue = $DB->set_field('user_info_data', 'data', $eventoid, array('id' => $uid->id));
    } else {
        // Insert.
        // Gets an existing user info field for eventoid.
        $sql = 'SELECT uif.*
            FROM {user_info_field} uif
            WHERE uif.shortname = :eventoid';

        $sqlparams = array('eventoid' => ENROL_EVENTO_UIF_EVENTOID);

        $uif = $DB->get_field_sql($sql, $sqlparams);

        if ($uif) {
            // Inserts new user_info_data item.
            $item = new \stdClass();
            $item->userid = $userid;
            $item->fieldid = $uif->id;
            $item->data = $eventoid;
            $item->dataformat = 0;

            $uiditem = $DB->insert_record('user_info_data', $item);
            if ($uiditem) {
                $returnvalue = true;
            }
        }
    }

    return $returnvalue;
}
