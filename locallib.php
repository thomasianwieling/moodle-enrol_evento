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
        // Init.
        $config = get_config('enrol_evento');
        $plugin = enrol_get_plugin('evento');
        $evenotservice = new local_evento_evento_service();
        // Todo move this array to settings.
        // Valid Anlass-Anmeldungen for student enrolment.
        $enrolstateids = array(20208, 20215, 20225, 20240, 20245, 20270, 20275, 20281, 20282, 20284, 20286, 20288);

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Starting evento enrolment synchronisation...');

        // Init the time start and end for new enrolments.
        $now = time();
        $timestart = make_timestamp(date('Y', $now), date('m', $now), date('d', $now), 0, 0, 0);
        $timeend = 0;

        // Set up a student and a teacher role for use in some tests.
        $eteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));

        $params = array('now1' => $now, 'now2' => $now, 'now3' => $now, 'courselevel' => CONTEXT_COURSE, 'enabled' => ENROL_INSTANCE_ENABLED);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        $instances = array();

        // Selects all active enrolments in unfinished courses.
        // No enrolment if course enddate is reached or the cours is hidden.
        $sql = "SELECT e.*, c.idnumber, cx.id AS contextid
                FROM {enrol} e
                JOIN {context} cx ON (cx.instanceid = e.courseid AND cx.contextlevel = :courselevel)
                JOIN {course} c ON (c.id = e.courseid AND (c.enddate = 0 OR (c.enddate > 0 AND c.enddate >= :now1)) AND c.visible = 1)
                WHERE e.enrol = 'evento' AND (e.enrolenddate = 0 OR (e.enrolenddate > 0 AND e.enrolenddate >= :now2))
                      AND (e.enrolstartdate = 0 OR (e.enrolstartdate > 0 AND e.enrolstartdate <= :now3))
                      AND e.status = :enabled
                    $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);

        // Iterate over each evento enrol instance.
        foreach ($rs as $ce) {
            try {
                if (empty($instances[$ce->id])) {
                    $instances[$ce->id] = $DB->get_record('enrol', array('id' => $ce->id));
                }
                $instance = $instances[$ce->id];
                // Timestamps for enrolemnts.
                $timestart = $ce->enrolstartdate;
                $timeend = $ce->enrolenddate;
                // Array of ids of active enrolled users.
                $entolledusersids = array();

                // Get event id and data.
                $event = $evenotservice->get_event_by_number(trim($ce->idnumber));
                // Get event participants enrolments.
                $enrolments = $evenotservice->get_enrolments_by_eventid($event->idAnlass);


                if (!is_array($enrolments)) {
                    // Create an array with one item.
                    $enrolments = array(1 => $enrolments);
                }

                // Enrol students.
                foreach ($enrolments as $ee) {
                    try {
                        // Get the moodle user.
                        $u = enrol_evento_get_user($evenotservice, $ee->idPerson);

                        // Check enrolment state to enrol or suspend.
                        if (in_array($ee->iDPAStatus, $enrolstateids)) {
                            // Enrol.
                            $plugin->enrol_user($instance, $u->id, $studentroleid, $timestart, $timeend, ENROL_USER_ACTIVE);
                            $entolledusersids[] = $u->id;
                            $trace->output("enroling user {$u->id} in course {$instance->courseid} as a student", 1);
                        } else {
                            // Suspend user which do not have an active state in evento.
                            $plugin->update_user_enrol($instance, $u->id, ENROL_USER_SUSPENDED);
                            $trace->output("suspending expired user {$u->id} in course {$instance->courseid}", 1);
                        }

                        unset($u);
                    } catch (Exception $ex) {
                        debugging("Enrolemnt sync of user evento personid: {$ee->idPerson} aborted with error: {$ex->message}");
                        $trace->output('...user enrolment synchronisation aborted unexpected during sync of enrolment with evento personid: {$ee->idPerson}');
                    }
                }

                // Enrol teachers.
                $eventteachers = array();
                if (!is_array($event->array_EventoAnlassLeitung)) {
                    $eventteachers[0] = $event->array_EventoAnlassLeitung;
                } else {
                    $eventteachers = $event->array_EventoAnlassLeitung;
                }
                foreach ($eventteachers as $teacher) {
                    try {
                        // Get or create the moodle user.
                        $u = enrol_evento_get_user($evenotservice, $teacher->anlassLtgIdPerson);
                        // Enrol.
                        $plugin->enrol_user($instance, $u->id, $eteacherroleid, $timestart, $timeend, ENROL_USER_ACTIVE);
                        $entolledusersids[] = $u->id;
                        $trace->output("enroling user {$u->id} in course {$instance->courseid} as an editingteacher", 1);
                    } catch (Exception $ex) {
                        debugging("Enrolemnt sync of user evento personid: {$teacher->anlassLtgIdPerson} aborted with error: {$ex->message}");
                        $trace->output("...user enrolment synchronisation aborted unexpected during sync of enrolment with evento personid: {$teacher->anlassLtgIdPerson}");
                    }
                }

                // Suspend users that are already enrolled in moodle, but not anymore in evento.
                $allenrolledusers = $DB->get_records('user_enrolments', array('enrolid' => $ce->id, 'status' => ENROL_USER_ACTIVE), 'userid', 'userid');
                foreach ($allenrolledusers as $enrolleduser) {
                    try {
                        if (!in_array($enrolleduser->userid, $entolledusersids)) {
                            $plugin->update_user_enrol($instance, $enrolleduser->userid, ENROL_USER_SUSPENDED);
                            $trace->output("suspending expired user {$enrolleduser->userid} in course {$instance->courseid}", 1);
                        }
                    } catch (Exception $ex) {
                        debugging("Error durring suspending of user with id: {$enrolleduser->userid} aborted with error: {$ex->message}");
                        $trace->output("...user enrolment synchronisation aborted unexpected during suspending with userid: {$enrolleduser->userid}");
                    }
                }
            } catch (Exception $ex) {
                debugging("Instance with id {$ce->id} aborted with error: {$ex->message}");
                $trace->output("...user enrolment synchronisation aborted unexpected during sync of enrol instance id: {$ce->id}");
            }
        }
        $rs->close();
        unset($instances);

        $trace->output('...user enrolment synchronisation finished.');
    } catch (Exeption $ex) {
        debugging("Error: {$ex->message}");
        $trace->output('...user enrolment synchronisation aborted unexpected');
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
    global $DB, $CFG;

    // Todo permission check.
    $u = enrol_evento_get_user_by_eventoid($eventopersonid);

    if (!$u) {
        if (!isset($username) OR !isset($email) OR !isset($firstname) OR !isset($lastname)) {
            $person = $evenotservice->get_person_by_id($eventopersonid);
            // todo get the shibbolet id from $person or ldap
            $username = (string)$eventopersonid; // $person->shibbolethid;
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
        require_once($CFG->dirroot . "/user/lib.php");
        $config = get_config('enrol_evento');

        $usernew = new stdClass();
        $usernew->auth = $config->accounttype;
        // $usernew->username = $person->shibbolethid;
        $usernew->username = (string)$eventopersonid;
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
        debugging('user created with username: {$usernew->username}', DEBUG_DEVELOPER);
    }

    return $u;
}

/**
 * Obtains the user defined field eventoid if it is set.
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

    return (string)$data;
}

/**
 * Obtains the user by an eventoid if it is set.
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

    $sqlparams = array('eventoidshortname' => ENROL_EVENTO_UIF_EVENTOID, 'eventoid' => (string)$eventoid);

    $user = $DB->get_record_sql($sql, $sqlparams);

    return $user;
}

/**
 * Obtains the user by username (shibbolethid).
 *
 * @param string $username
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
    $sql = 'SELECT uid.id
        FROM {user_info_data} uid
        INNER JOIN {user_info_field} uif ON uid.fieldid = uif.id
        WHERE uif.shortname = :eventoid
        AND uid.userid = :userid';

    $sqlparams = array('eventoid' => ENROL_EVENTO_UIF_EVENTOID, 'userid' => $userid);

    $uid = $DB->get_field_sql($sql, $sqlparams);

    if ($uid) {
        // Update.
        $returnvalue = $DB->set_field('user_info_data', 'data', $eventoid, array('id' => $uid));
    } else {
        // Insert.
        // Gets an existing user info field for eventoid.
        $sql = 'SELECT uif.id
            FROM {user_info_field} uif
            WHERE uif.shortname = :eventoid';

        $sqlparams = array('eventoid' => ENROL_EVENTO_UIF_EVENTOID);

        $uifid = $DB->get_field_sql($sql, $sqlparams);

        if ($uifid) {
            // Inserts new user_info_data item.
            $item = new \stdClass();
            $item->userid = $userid;
            $item->fieldid = $uifid;
            $item->data = (string)$eventoid;
            $item->dataformat = 0;

            $uiditem = $DB->insert_record('user_info_data', $item);
            if ($uiditem) {
                $returnvalue = true;
            }
        }
    }

    return $returnvalue;
}
