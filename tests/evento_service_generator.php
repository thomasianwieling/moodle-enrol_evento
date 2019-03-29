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
 * Evento enrolment plugin main library file.
 *
 * @package   enrol_evento
 * @copyright 2019 HTW Chur Thomas Wieling
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class service implements interface_evento_service
{
    public $config;
    public $evento_anlass;
    public $evento_personen_anmeldungen;
    public $evento_personen;
    public $ad_accounts;

    public function __construct() {
        global $CFG;
        $this->config = get_config('local_evento');
        $this->config = new stdClass();
        $this->config->adsidprefix = "S-1-5-21-";
        $this->config->adshibbolethsuffix = "@fh-htwchur.ch";
    }

    public function init_call() {
        return true;
    }

    public function get_event_by_number($number) {
        $evento_anlass = $this->evento_anlass;

        foreach ($evento_anlass as $anlass) {
            if ($number == $anlass->anlassnummer) {
                return $anlass;
            }
        }

        return null;
    }

    public function get_enrolments_by_eventid($eventid) {
        $personenanmeldungen = $this->evento_personen_anmeldungen;
        $personenanmeldungen_array = null;

        foreach ($personenanmeldungen as $personenanmeldung) {
            if ($eventid == $personenanmeldung->idanlass) {
                $personenanmeldungen_array[] = $personenanmeldung;
            }
        }

        return $personenanmeldungen_array;
    }

    public function get_person_by_id($personid) {
        $evento_personen = $this->evento_personen;

        foreach ($evento_personen as $evento_person) {
            if ($personid == $evento_person->idperson) {
                return $evento_person;
            }
        }

        return null;
    }

    public function get_ad_accounts_by_evento_personid($personid, $isactive = null, $isstudent=null) {
        $ad_accounts = $this->ad_accounts;
        foreach ($ad_accounts as $ad_account) {
            if ($personid == $ad_account->idperson) {
                return $ad_account;
            }
        }

        return null;
    }

    public function get_student_ad_accounts($isactive=null) {
        $ad_accounts = $this->ad_accounts;
        $student_ad_account = null;
        foreach ($ad_accounts as $ad_account) {
            if (1 == $ad_account->isstudentaccount) {
                $student_ad_account[] = $ad_account;
            }
        }

        return $student_ad_account;
    }

    public function get_lecturer_ad_accounts($isactive=null) {
        $ad_accounts = $this->ad_accounts;
        $lecturer_ad_account = null;
        foreach ($ad_accounts as $ad_account) {
            if (1 == $ad_account->islectureraccount) {
                $lecturer_ad_account[] = $ad_account;
            }
        }

        return $lecturer_ad_account;
    }

    public function get_employee_ad_accounts($isactive=null) {
        $ad_accounts = $this->ad_accounts;
        $employee_ad_account = null;
        foreach ($ad_accounts as $ad_account) {
            if (1 == $ad_account->isemployeeaccount) {
                $employee_ad_account[] = $ad_account;
            }
        }

        return $employee_ad_account;
    }

    public function get_all_ad_accounts($isactive=null) {
        // Set request filter.
        $result = array();
        $employees = self::to_array($this->get_employee_ad_accounts($isactive));
        $lecturers = self::to_array($this->get_lecturer_ad_accounts($isactive));
        $students = self::to_array($this->get_student_ad_accounts($isactive));

        if (isset($employees) && isset($lecturers)) {
            $result = array_merge($employees, $lecturers);

        }
        if (isset($students)) {
            $result = array_merge($students, $result);
        }
        return $result;
    }

    public static function to_array($value) {
        $returnarray = array();
        if (is_array($value)) {
            $returnarray = $value;
        } else if (!is_null($value)) {
            $returnarray[0] = $value;
        }
        return $returnarray;
    }

    public function shibbolethid_to_sid($shibbolethid) {
        return trim($this->config->adsidprefix . str_replace($this->config->adshibbolethsuffix, "", $shibbolethid));
    }

    public function sid_to_shibbolethid($sid) {
        return trim(str_replace($this->config->adsidprefix, "", $sid) . $this->config->adshibbolethsuffix);
    }
}
