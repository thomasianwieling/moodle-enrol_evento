<?php

class service implements interface_evento_service
{
    public $config;
    public $evento_anlass;
    public $evento_personen_anmeldungen;
    public $evento_personen;
    public $ad_accounts;

    public function __construct()
    {
        global $CFG;
        $this->config = get_config('local_evento');
        $this->config = new stdClass();
        $this->config->adsidprefix = "S-1-5-21-";
        $this->config->adshibbolethsuffix = "@fh-htwchur.ch";
    }

    public function init_call()
    {
        return true;
    }

    public function get_event_by_number($number)
    {
        $evento_anlass = $this->evento_anlass;

        foreach ($evento_anlass as $anlass){
            if ($number == $anlass->anlassNummer) {
                return $anlass;
            }
        }

        return null;
    }

    public function get_enrolments_by_eventid($eventid)
    {
        $personen_anmeldungen = $this->evento_personen_anmeldungen;
        $personen_anmeldungen_array = null;

        foreach ($personen_anmeldungen as $personen_anmeldung){
            if ($eventid == $personen_anmeldung->idAnlass) {
                $personen_anmeldungen_array[] = $personen_anmeldung;
            }
        }

        return $personen_anmeldungen_array;
    }

    public function get_person_by_id($personid)
    {
        $evento_personen = $this->evento_personen;

        foreach ($evento_personen as $evento_person){
            if ($personid == $evento_person->idPerson) {
                return $evento_person;
            }
        }

        return null;
    }

    public function get_ad_accounts_by_evento_personid($personid, $isactive = null, $isstudent=null)
    {
        $ad_accounts = $this->ad_accounts;
        foreach ($ad_accounts as $ad_account){
            if ($personid == $ad_account->idPerson) {
                return $ad_account;
            }
        }

        return null;
    }

    public function get_student_ad_accounts($isactive=null)
    {
        $ad_accounts = $this->ad_accounts;
        $student_ad_account = null;
        foreach ($ad_accounts as $ad_account){
            if (1 == $ad_account->isStudentAccount) {
                $student_ad_account[] = $ad_account;
            }
        }

        return $student_ad_account;
    }

    public function get_lecturer_ad_accounts($isactive=null)
    {
        $ad_accounts = $this->ad_accounts;
        $lecturer_ad_account = null;
        foreach ($ad_accounts as $ad_account){
            if (1 == $ad_account->isLecturerAccount) {
                $lecturer_ad_account[] = $ad_account;
            }
        }

        return $lecturer_ad_account;
    }

    public function get_employee_ad_accounts($isactive=null)
    {
        $ad_accounts = $this->ad_accounts;
        $employee_ad_account = null;
        foreach ($ad_accounts as $ad_account){
            if (1 == $ad_account->isEmployeeAccount) {
                $employee_ad_account[] = $ad_account;
            }
        }

        return $employee_ad_account;
    }

    public function get_all_ad_accounts($isactive=null)
    {
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

    public static function to_array($value)
    {
        $returnarray = array();
        if (is_array($value)) {
            $returnarray = $value;
        } else if (!is_null($value)) {
            $returnarray[0] = $value;
        }
        return $returnarray;
    }

    public function shibbolethid_to_sid($shibbolethid)
    {
        return trim($this->config->adsidprefix . str_replace($this->config->adshibbolethsuffix, "", $shibbolethid));
    }

    public function sid_to_shibbolethid($sid)
    {
        return trim(str_replace($this->config->adsidprefix, "", $sid) . $this->config->adshibbolethsuffix);
    }


}
