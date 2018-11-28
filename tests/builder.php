<?php
require_once($CFG->dirroot . '/enrol/evento/tests/evento_service_generator.php');
require_once($CFG->dirroot . '/enrol/evento/tests/evento_data_generator.php');
class builder{

  // @var
	public $service;

  public function __construct() {
		$this->service = new service();
	}

	public function add_anlass($anlassBezeichnung, $anlassDatumBis, $anlassDatumVon, $anlassKategorie, $anlassLeitungIdPerson, $anlassNummer, $array_EventoAnlassLeitung, $idAnlass, $idAnlassKategorie, $idAnlassNiveau, $idAnlassStatus, $idAnlassTyp){
    $evento_anlass = new evento_anlass($anlassBezeichnung, $anlassDatumBis, $anlassDatumVon, $anlassKategorie, $anlassLeitungIdPerson, $anlassNummer, $array_EventoAnlassLeitung, $idAnlass, $idAnlassKategorie, $idAnlassNiveau, $idAnlassStatus, $idAnlassTyp);
    $this->service->evento_anlass[] = $evento_anlass;
    return $evento_anlass;
  }

  public function add_evento_status($idStatus, $statusName, $aenderungVon, $erfassungVon, $aenderung){
    $evento_status = new evento_status($idStatus, $statusName, $aenderungVon, $erfassungVon, $aenderung);
    $this->service->evento_status[] = $evento_status;
    return $evento_status;
  }

  public function add_personen_anmeldung($aenderung, $aenderungVon, $erfassung, $erfassungVon, $idAnmeldung, $iDPAStatus, $idAnlass, $idPerson, $personenAnmeldungStatus){
    $evento_personen_anmeldung = new evento_personen_anmeldung($aenderung, $aenderungVon, $erfassung, $erfassungVon, $idAnmeldung, $iDPAStatus, $idAnlass, $idPerson, $personenAnmeldungStatus);
    $this->service->evento_personen_anmeldungen[] = $evento_personen_anmeldung;
    return $evento_personen_anmeldung;
  }

  public function add_person($personNachname, $personVorname, $personeMail, $idPerson, $idPersonStatus, $personAktiv, $personKorrIdPerson, $personen_anmeldung){
    $evento_person = new evento_person($personNachname, $personVorname, $personeMail, $idPerson, $idPersonStatus, $personAktiv, $personKorrIdPerson, $personen_anmeldung);
    $this->service->evento_personen[] = $evento_person;
    return $evento_person;
  }

  public function add_ad_account($accountStatusDisabled, $changed, $created, $hasSeveralAccounts, $idPerson, $isEmployeeAccount, $isLecturerAccount, $isStudentAccount, $objectSid, $sAMAccountName){
    $ad_account = new ad_account($accountStatusDisabled, $changed, $created, $hasSeveralAccounts, $idPerson, $isEmployeeAccount, $isLecturerAccount, $isStudentAccount, $objectSid, $sAMAccountName);
    $this->service->ad_accounts[] = $ad_account;
    return $ad_account;
  }

}
