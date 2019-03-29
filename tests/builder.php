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
 * Unit-Test for enrolment plugin
 *
 * @package    enrol_evento
 * @copyright  2019 HTW Chur Thomas Wieling
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/enrol/evento/tests/evento_service_generator.php');
require_once($CFG->dirroot . '/enrol/evento/tests/evento_data_generator.php');
class builder
{

    // @var
    public $service;

    public function __construct() {
        $this->service = new service();
    }

    public function add_anlass($anlassbezeichnung, $anlassdatumbis, $anlassdatumvon, $anlasskategorie, $anlassleitungidperson, $anlassnummer, $arrayeventoanlassleitung, $idanlass, $idanlasskategorie, $idanlassniveau, $idanlassstatus, $idanlasstyp) {
        $evento_anlass = new evento_anlass($anlassbezeichnung, $anlassdatumbis, $anlassdatumvon, $anlasskategorie, $anlassleitungidperson, $anlassnummer, $arrayeventoanlassleitung, $idanlass, $idanlasskategorie, $idanlassniveau, $idanlassstatus, $idanlasstyp);
        $this->service->evento_anlass[] = $evento_anlass;
        return $evento_anlass;
    }

    public function add_evento_status($idstatus, $statusname, $aenderungvon, $erfassungvon, $aenderung) {
        $evento_status = new evento_status($idstatus, $statusname, $aenderungvon, $erfassungvon, $aenderung);
        $this->service->evento_status[] = $evento_status;
        return $evento_status;
    }

    public function add_personen_anmeldung($aenderung, $aenderungvon, $erfassung, $erfassungvon, $idanmeldung, $iDPAStatus, $idanlass, $idperson, $personenanmeldungstatus) {
        $evento_personen_anmeldung = new evento_personen_anmeldung($aenderung, $aenderungvon, $erfassung, $erfassungvon, $idanmeldung, $iDPAStatus, $idanlass, $idperson, $personenanmeldungstatus);
        $this->service->evento_personen_anmeldungen[] = $evento_personen_anmeldung;
        return $evento_personen_anmeldung;
    }

    public function add_person($personnachname, $personvorname, $personemail, $idperson, $idpersonstatus, $personaktiv, $personkorridperson, $personenanmeldung) {
        $evento_person = new evento_person($personnachname, $personvorname, $personemail, $idperson, $idpersonstatus, $personaktiv, $personkorridperson, $personenanmeldung);
        $this->service->evento_personen[] = $evento_person;
        return $evento_person;
    }

    public function add_ad_account($accountstatusdisabled, $changed, $created, $hasseveralaccounts, $idperson, $isemployeeaccount, $islectureraccount, $isstudentaccount, $objectsid, $sAMAccountName) {
        $ad_account = new ad_account($accountstatusdisabled, $changed, $created, $hasseveralaccounts, $idperson, $isemployeeaccount, $islectureraccount, $isstudentaccount, $objectsid, $sAMAccountName);
        $this->service->ad_accounts[] = $ad_account;
        return $ad_account;
    }

}
