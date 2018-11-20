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

defined('MOODLE_INTERNAL') || die();

/**
* DateTime format of the evento xml dateTime imagetypes
*/
define('LOCAL_EVENTO_DATETIME_FORMAT', "Y-m-d\TH:i:s.uP");





/**
 * Class definition for the evento webservice call
 *
 * @package    local_evento
 * @copyright  2017 HTW Chur Roger Barras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_evento_evento_service {

  // Plugin configuration.
  private $config;
  private $client;

  public function __construct()
  {
    global $CFG;
    $this->config = get_config('local_evento');
    $this->config = new stdClass();
    $this->config->adsidprefix = "S-1-5-21-";
    $this->config->adshibbolethsuffix = "@fh-htwchur.ch";
  }



  /**
   * Doing a simple init Webservice call to open the connection
   * @return boolean true if the request was successfully
  */
  public function init_call()
  {
        return true;
  }

    /**
     * Obtains an event by the id-number
     * @param string $number the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */
    public function get_event_by_number($number)
    {

      if($number = 'mod.mmpAUKATE1.HS18_BS.001'){
      $anlassLeitungRolle_object = (object)
      [
        "anlassLtgRolleAktiv"=> true,
        "anlassLtgRolleBezeichnung"=> "Hauptleitung",
        "anlassLtgRolleBezeichnungKrz"=> "Hauptleitung",
        "anlassLtgRolleBezeichnungSort"=> "100",
        "idAnlassLtgRolle"=> 2
      ];

      $personenAnmeldungStatus_object = (object)
      [
        'idStatus'=>20175,
        'statusName'=> "pA.Abgeschlossen"
      ];

      $array_personenanmeldungen_object = (object)
      [
        "iDAnmeldung"=> 123822,
        "iDPAStatus"=> 20175,
        "idAnlass"=> 25490,
        "idPerson"=> 117828,
        "personenAnmeldungStatus"=>$personenAnmeldungStatus_object
      ];

      $personenStatus_object = (object)
      [
          'idStatus'=>30040,
          'statusName'=> "ps.Aktiv"
      ];

      $anlassLtgPerson_object = (object)
      [
        'array_personenanmeldungen' => array($array_personenanmeldungen_object),
        'idPerson' => 117828,
        'idPersonStatus' => 30040,
        'personAktiv' => true,
        'personKorrIdPerson' => 117828,
        'personNachname' => "Müller",
        'personRechIdPerson' => 117828,
        'personVorname' => "Peter",
        'personeMail' => "peter.mueller@htwchur.ch",
        'personenStatus' => $personenStatus_object
      ];

      $array_EventoAnlassLeitung_object = (object)
      [
        'anlassLeitungRolle'=> $anlassLeitungRolle_object,
        "anlassLtgIdAnlass"=>25490,
        "anlassLtgIdAnlassLtgRolle"=>2,
        "anlassLtgIdPerson"=>117828,
        'anlassLtgPerson' => $anlassLtgPerson_object
      ];

      $anlassStatus_object = (object)
      [
      //  'idStatus' => 10230,
      ];

      $anlassKategorie_object = (object)
      [
        'anlassKategorieAktiv' => true,
        'idAnlassKategorie' => 1
      ];

      $returnObject = (object)
      [
        'anlassBezeichnung'=> "Audio- & Kameratechnik 1",
        'anlassDatumBis'=> "2019-02-17T00:00:00.000+01:00",
        'anlassDatumVon'=> "2018-09-17T00:00:00.000+02:00",
        'anlassKategorie' => $anlassKategorie_object,
        'anlassLeitungIdPerson' => 117828,
        'anlassNummer' => "mod.mmpAUKATE1.HS18_BS.001",
        'anlassStatus' => $anlassStatus_object,
        'array_EventoAnlassLeitung' => $array_EventoAnlassLeitung_object,
        'idAnlass'=> 25490,
        'idAnlassKategorie'=> 1,
        'idAnlassNiveau'=> 60,
        'idAnlassStatus'=> 10230,
        'idAnlassTyp'=>3
      ];

      return $returnObject;
    }
  }

    /**
     * Obtains events by filters
     * @param local_evento_eventoanlassfilter $eventoanlassfilter the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @param local_evento_limitationfilter2 $limitationfilter2 filter for response limitation
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */
    public function get_events_by_filter(local_evento_eventoanlassfilter $eventoanlassfilter, local_evento_limitationfilter2 $limitationfilter2) {
        // Set request filter.
        !empty($eventoanlassfilter->anlassnummer) ? $request['theEventoAnlassFilter']['anlassNummer'] = $eventoanlassfilter->anlassnummer : null;
        !empty($eventoanlassfilter->idanlasstyp) ? $request['theEventoAnlassFilter']['idAnlassTyp'] = $eventoanlassfilter->idanlasstyp : null;
        // To limit the response size if something went wrong.
        !empty($limitationfilter2->themaxresultvalue) ? $request['theLimitationFilter2']['theMaxResultsValue'] = $limitationfilter2->themaxresultvalue : null;
        !empty($limitationfilter2->thefromdate) ? $request['theLimitationFilter2']['theFromDate'] = $limitationfilter2->thefromdate : null;
        !empty($limitationfilter2->thetodate) ? $request['theLimitationFilter2']['theToDate'] = $limitationfilter2->thetodate : null;
        // Sort order.
        !empty($limitationfilter2->sortfield) ? $request['theLimitationFilter2']['theSortField'] = $limitationfilter2->sortfield : null;
        $result = $this->client->listEventoAnlass($request);

        return array_key_exists("return", $result) ? $result->return : null;
    }

    /**
     * Obtains the enrolments of an event
     * @param string $eventid the evento eventid
     * @return array of stdClass event object "EventoPersonenAnmeldung" definied in the wsdl
     */
    public function get_enrolments_by_eventid($eventid)
    {
      if($eventid == 25490)
      {
        $personenAnmeldungStatus_object = (object)
        [
          'aenderung'=> "2008-07-04T10:03:23.000+02:00",
          'aenderungVon'=> "BI_gzap",
          'erfassungVon'=> "auto",
          'idStatus'=>20215,
          'statusName'=> "aA.Angemeldet"
        ];

        $return_object = (object)
        [
          'aenderung'=> "2018-06-05T08:58:20.723+02:00",
          'aenderungVon'=> "hoferlis",
          'erfassung'=> "2018-06-05T08:58:20.723+02:00",
          'erfassungVon'=> "hoferlis",
          'iDAnmeldung'=>415864,
          'iDPAStatus'=>20215,
          'idAnlass'=>25490,
          'idPerson'=>141703,
          'personenAnmeldungStatus' => $personenAnmeldungStatus_object
        ];
        return $return_object;
      }
    }

    /**
     * Obtains the person details
     * @param string $personid the evento eventid
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_person_by_id($personid)
    {
      if($personid == 141703)
      {
        $personen_anmeldung_object = (object)
        [
          "iDAnmeldung"=> 415870,
          "iDPAStatus"=> 20215,
          "idAnlass"=> 25490,
          "idPerson"=> 141703,
        ];

        $returnObject = (object)
        [
          "array_personenanmeldungen"=> array($personen_anmeldung_object),
          "idPerson"=> 141703,
          "idPersonStatus"=>30040,
          "personAktiv"=>true,
          "personKorrIdPerson"=>141703,
          "personNachname"=> "Muster",
          "personVorname"=> "Max",
          "personeMail"=> "max.muster@stud.htwchur.ch",
        ];
        return $returnObject;
      }

      if($personid == 143440)
      {
        $personen_anmeldung_object = (object)
        [
          "iDAnmeldung"=> 415870,
          "iDPAStatus"=> 20215,
          "idAnlass"=> 25490,
          "idPerson"=> 143440,
        ];

        $returnObject = (object)
        [
          "array_personenanmeldungen"=> array($personen_anmeldung_object),
          "idPerson"=> 143440,
          "idPersonStatus"=>30040,
          "personAktiv"=>true,
          "personKorrIdPerson"=>143440,
          "personNachname"=> "Meier",
          "personVorname"=> "Hans",
          "personeMail"=> "hans.meier@stud.htwchur.ch",
        ];
        return $returnObject;
      }
    }

    /**
     * Obtains the Active Directory accountdetails
     *
     * @param string $personid the evento eventid
     * @param bool $isactive true to get only active accounts; default null.
     * @param bool $isstudent true if you like to get students; default null.
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_ad_accounts_by_evento_personid($personid, $isactive = null, $isstudent=null)
    {
      if($personid == 141703)
      {
        $object = (object)
        [
          'accountStatusDisabled' => 0,
          'hasSeveralAccounts' => 0,
          'idPerson' => 141703,
          'isEmployeeAccount' => 0,
          'isLecturerAccount' => 0,
          'isStudentAccount' => 1,
          'objectSid' => "S-1-5-21-2460181390-1097805571-3701207438-51315",
          'sAMAccountName' => "MaxMuster"
        ];

        $person = array($object);
        return $person;
      }
    }

    /**
     * Obtains the Active Directory accountdetails of students
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_student_ad_accounts($isactive = null) {

      $object = (object) [
        'accountStatusDisabled' => 0,
        'hasSeveralAccounts' => 0,
        'idPerson' => 141703,
        'isEmployeeAccount' => 0,
        'isLecturerAccount' => 0,
        'isStudentAccount' => 1,
        'objectSid' => "S-1-5-21-2460181390-1097805571-3701207438-51315",
        'sAMAccountName' => "MaxMuster"
      ];

      $object1 = (object) [
        'accountStatusDisabled' => 0,
        'changed' => "2018-09-21T15:31:55.293+02:00",
        'created' => "2018-09-01T15:31:55.293+02:00",
        'hasSeveralAccounts' => 0,
        'idPerson' => 143440,
        'isEmployeeAccount' => 0,
        'isLecturerAccount' => 0,
        'isStudentAccount' => 1,
        'objectSid' => "S-1-5-21-2360181390-1097805571-3701207438-51315",
        'sAMAccountName' => "HansMeier"
      ];


      $person = array($object, $object1);

      return $person;
}


    /**
     * Obtains the Active Directory accountdetails of lecturers
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_lecturer_ad_accounts($isactive = null) {
      $object = (object) [
        'accountStatusDisabled' => 0,
        'changed' => "2018-09-21T15:31:55.293+02:00",
        'created' => "2018-09-01T15:31:55.293+02:00",
        'hasSeveralAccounts' => 0,
        'idPerson' => 117828,
        'isEmployeeAccount' => 0,
        'isLecturerAccount' => 1,
        'isStudentAccount' => 0,
        'objectSid' => "S-1-5-21-2360181390-1097805571-3701207438-51315",
        'sAMAccountName' => "HansMeier"
      ];
      $person = array($object);

      return $person;

    }

    /**
     * Obtains the Active Directory accountdetails of employees
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_employee_ad_accounts($isactive = null) {


      //$person = array($object);

      return NULL;
    }

    /**
     * Obtains all the Active Directory accountdetails
     * of employees, lecturers, students
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_all_ad_accounts($isactive = null) {
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

    /**
     * Converts an AD SID to a shibboleth Id
     *
     * @param string $sid sid of the user from the Active Directory
     * @return string shibboleth id
     */
    public function sid_to_shibbolethid($sid) {
        return trim(str_replace($this->config->adsidprefix, "", $sid) . $this->config->adshibbolethsuffix);
    }

    /**
     * Converts a shibboleth ID to an Active Directory SID
     *
     * @param string $sishibbolethid shibbolethid of the user
     * @return string sid from the Active Directory
     */
    public function shibbolethid_to_sid($shibbolethid) {
        return trim($this->config->adsidprefix . str_replace($this->config->adshibbolethsuffix, "", $shibbolethid));
    }

        /**
         * Create an array if the value is not already one.
         *
         * @param var $value
         * @return array of the $value
         */
        public static function to_array($value) {
            $returnarray = array();
            if (is_array($value)) {
                $returnarray = $value;
            } else if (!is_null($value)) {
                $returnarray[0] = $value;
            }
            return $returnarray;
        }

    }



    /**
     * Enumeration of "idAnlassTyp"
     *
     * @package    local_evento
     * @copyright  2017 HTW Chur Roger Barras
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    abstract class local_evento_idanlasstyp {
        const MODULANLASS = 3;
}
