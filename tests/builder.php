<?php
require_once($CFG->dirroot . '/enrol/evento/tests/evento_service_generator.php');
require_once($CFG->dirroot . '/enrol/evento/tests/evento_data_generator.php');
class builder
{

    // @var
    public $service;

    public function __construct()
    {
        $this->service = new service();
    }

    public function add_anlass($anlassbezeichnung, $anlassdatumbis, $anlassdatumvon, $anlasskategorie, $anlassleitungidperson, $anlassnummer, $arrayeventoanlassleitung, $idanlass, $idanlasskategorie, $idanlassniveau, $idanlassstatus, $idanlasstyp)
    {
        $evento_anlass = new evento_anlass($anlassbezeichnung, $anlassdatumbis, $anlassdatumvon, $anlasskategorie, $anlassleitungidperson, $anlassnummer, $arrayeventoanlassleitung, $idanlass, $idanlasskategorie, $idanlassniveau, $idanlassstatus, $idanlasstyp);
        $this->service->evento_anlass[] = $evento_anlass;
        return $evento_anlass;
    }

    public function add_evento_status($idstatus, $statusname, $aenderungvon, $erfassungvon, $aenderung)
    {
        $evento_status = new evento_status($idstatus, $statusname, $aenderungvon, $erfassungvon, $aenderung);
        $this->service->evento_status[] = $evento_status;
        return $evento_status;
    }

    public function add_personen_anmeldung($aenderung, $aenderungvon, $erfassung, $erfassungvon, $idanmeldung, $iDPAStatus, $idanlass, $idperson, $personenanmeldungstatus)
    {
        $evento_personen_anmeldung = new evento_personen_anmeldung($aenderung, $aenderungvon, $erfassung, $erfassungvon, $idanmeldung, $iDPAStatus, $idanlass, $idperson, $personenanmeldungstatus);
        $this->service->evento_personen_anmeldungen[] = $evento_personen_anmeldung;
        return $evento_personen_anmeldung;
    }

    public function add_person($personnachname, $personvorname, $personemail, $idperson, $idpersonstatus, $personaktiv, $personkorridperson, $personenanmeldung)
    {
        $evento_person = new evento_person($personnachname, $personvorname, $personemail, $idperson, $idpersonstatus, $personaktiv, $personkorridperson, $personenanmeldung);
        $this->service->evento_personen[] = $evento_person;
        return $evento_person;
    }

    public function add_ad_account($accountstatusdisabled, $changed, $created, $hasseveralaccounts, $idperson, $isemployeeaccount, $islectureraccount, $isstudentaccount, $objectsid, $sAMAccountName)
    {
        $ad_account = new ad_account($accountstatusdisabled, $changed, $created, $hasseveralaccounts, $idperson, $isemployeeaccount, $islectureraccount, $isstudentaccount, $objectsid, $sAMAccountName);
        $this->service->ad_accounts[] = $ad_account;
        return $ad_account;
    }

}
