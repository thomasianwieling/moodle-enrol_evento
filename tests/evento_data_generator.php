<?php

class ad_account{
  public $accountStatusDisabled;
  public $changed;
  public $created;
  public $hasSeveralAccounts;
  public $idPerson;
  public $isEmployeeAccount;
  public $isLecturerAccount;
  public $isStudentAccount;
  public $objectSid;
  public $sAMAccountName;

  public function __construct(int $accountStatusDisabled, string $changed, string $created, int $hasSeveralAccounts, int $idPerson, int $isEmployeeAccount, int $isLecturerAccount, int $isStudentAccount, string $objectSid, string $sAMAccountName){
  $this->accountStatusDisabled = $accountStatusDisabled;
  $this->changed = $changed;
  $this->created = $created;
  $this->hasSeveralAccounts = $hasSeveralAccounts;
  $this->idPerson = $idPerson;
  $this->isEmployeeAccount = $isEmployeeAccount;
  $this->isLecturerAccount = $isLecturerAccount;
  $this->isStudentAccount = $isStudentAccount;
  $this->objectSid = $objectSid;
  $this->sAMAccountName = $sAMAccountName;
  }
}

class evento_status{
  public $idStatus;
  public $statusName;
  public $aenderung;
  public $aenderungVon;
  public $erfassungVon;

  public function __construct( int $idStatus, string $statusName, string $aenderungVon, string $erfassungVon, string $aenderung){
    $this->idStatus = $idStatus;
    $this->statusName = $statusName;
    $this->aenderungVon = $aenderungVon;
    $this->erfassungVon = $erfassungVon;
    $this->aenderung = $aenderung;
  }
}

class evento_anlass_typ{
  public $aenderung;
  public $aenderungVon;
  public $anlassTypAktiv;
  public $anlassTypBez;
  public $erfassung;
  public $erfassungVon;
  public $idAnlassTyp;

  public function __construct(string $aenderung, string $aenderungVon, boolean $anlassTypAktiv, string $anlassTypBez, string $erfassung, string $erfassungVon, int $idAnlassTyp){
  $this->aenderung = $aenderung;
  $this->aenderungVon = $aenderungVon;
  $this->anlassTypAktiv = $anlassTypAktiv;
  $this->anlassTypBez = $anlassTypBez;
  $this->erfassung = $erfassung;
  $this->erfassungVon = $erfassungVon;
  $this->idAnlassTyp = $idAnlassTyp;
  }
}

class evento_personen_anmeldung {
  public $idAnmeldung;
  public $iDPAStatus;
  public $idAnlass;
  public $idPerson;
  public $personenAnmeldungStatus;
  public $aenderung;
  public $aenderungVon;
  public $erfassung;
  public $erfassungVon;

  public function __construct(string $aenderung, string $aenderungVon, string $erfassung, string $erfassungVon, int $idAnmeldung, int $iDPAStatus, int $idAnlass, int $idPerson, object $personenAnmeldungStatus){
    $this->aenderung = $aenderung;
    $this->aenderungVon = $aenderungVon;
    $this->erfassung = $erfassung;
    $this->erfassungVon = $erfassungVon;
    $this->idAnmeldung = $idAnmeldung;
    $this->iDPAStatus = $iDPAStatus;
    $this->idAnlass = $idAnlass;
    $this->idPerson = $idPerson;
    $this->personenAnmeldungStatus = $personenAnmeldungStatus;
  }
}

class evento_person{
  public $personNachname;
  public $personVorname;
  public $personeMail;
  public $idPerson;
  public $idPersonStatus;
  public $personAktiv;
  public $personKorrIdPerson;
  public $personen_anmeldung;

  public function __construct(string $personNachname, string $personVorname, string $personeMail, int $idPerson, int $idPersonStatus,  $personAktiv, int $personKorrIdPerson, object $personen_anmeldung){
    $this->personNachname = $personNachname;
    $this->personVorname = $personVorname;
    $this->personeMail = $personeMail;
    $this->idPerson = $idPerson;
    $this->idPersonStatus = $idPersonStatus;
    $this->personAktiv = $personAktiv;
    $this->personKorrIdPerson = $personKorrIdPerson;
    $this->personen_anmeldung = $personen_anmeldung;
  }
}

class anlass_leitung_rolle{
  public $anlassLtgRolleAktiv;
  public $anlassLtgRolleBezeichnung;
  public $anlassLtgRolleBezeichnungKrz;
  public $anlassLtgRolleBezeichnungSort;
  public $idAnlassLtgRolle;
  public $aenderung;
  public $aenderungVon;
  public $erfassung;
  public $erfassungVon;

  public function __construct( boolean $anlassLtgRolleAktiv, string $anlassLtgRolleBezeichnung,  string $anlassLtgRolleBezeichnungKrz, string $anlassLtgRolleBezeichnungSort, int $idAnlassLtgRolle, string $aenderung, string $aenderungVon, string $erfassung, string $erfassungVon){
    $this->anlassLtgRolleAktiv = $anlassLtgRolleAktiv;
    $this->anlassLtgRolleBezeichnung = $anlassLtgRolleBezeichnung;
    $this->anlassLtgRolleBezeichnungKrz = $anlassLtgRolleBezeichnungKrz;
    $this->anlassLtgRolleBezeichnungSort = $anlassLtgRolleBezeichnungSort;
    $this->idAnlassLtgRolle = $idAnlassLtgRolle;
    $this->aenderung = $aenderung;
    $this->aenderungVon = $aenderungVon;
    $this->erfassung = $erfassung;
    $this->erfassungVon = $erfassungVon;
  }
}

class evento_anlass_leitung{
  public $anlassLeitungRolle;
  public $anlassLtgIdAnlass;
  public $anlassLtgIdAnlassLtgRolle;
  public $anlassLtgIdPerson;
  public $anlassLtgPerson;
  public $aenderung;
  public $aenderungVon;
  public $erfassung;
  public $erfassungVon;
  public $idAnlassLtg;

  public function __construct(anlass_ltg_person $anlassLeitungRolle, int $anlassLtgIdAnlass, int $anlassLtgIdAnlassLtgRolle, int $anlassLtgIdPerson, anlass_ltg_person $anlassLtgPerson, string $aenderung, string $aenderungVon, string $erfassung, string $erfassungVon, int $idAnlassLtg){
    $this->anlassLeitungRolle = $anlassLeitungRolle;
    $this->anlassLtgIdAnlass = $anlassLtgIdAnlass;
    $this->anlassLtgIdAnlassLtgRolle = $anlassLtgIdAnlassLtgRolle;
    $this->anlassLtgIdPerson = $anlassLtgIdPerson;
    $this->anlassLtgPerson = $anlassLtgPerson;
    $this->aenderung = $aenderung;
    $this->aenderungVon = $aenderungVon;
    $this->erfassung = $erfassung;
    $this->erfassungVon = $erfassungVon;
    $this->idAnlassLtg = $idAnlassLtg;
  }
}



class evento_anlass_kategorie{
  public $anlassKategorieAktiv;
  public $idAnlassKategorie;
  public $aenderung;
  public $aenderungVon;
  public $erfassung;
  public $erfassungVon;

  public function __construct(boolean $anlassKategorieAktiv, int $idAnlassKategorie, string $aenderung, string $aenderungVon, string $erfassung, string $erfassungVon){
    $this->anlassKategorieAktiv = $anlassKategorieAktiv;
    $this->idAnlassKategorie = $idAnlassKategorie;
    $this->aenderung = $aenderung;
    $this->aenderungVon = $aenderungVon;
    $this->erfassung = $erfassung;
    $this->erfassungVon = $erfassungVon;
  }
}


class evento_anlass{
  public $anlassBezeichnung;
  public $anlassDatumBis;
  public $anlassDatumVon;
  public $anlassKategorie;
  public $anlassLeitungIdPerson;
  public $anlassNummer;
  public $array_EventoAnlassLeitung;
  public $idAnlass;
  public $idAnlassKategorie;
  public $idAnlassNiveau;
  public $idAnlassStatus;
  public $idAnlassTyp;

  public function __construct($anlassBezeichnung, $anlassDatumBis, $anlassDatumVon, $anlassKategorie, $anlassLeitungIdPerson, $anlassNummer, $array_EventoAnlassLeitung, $idAnlass, $idAnlassKategorie, $idAnlassNiveau, $idAnlassStatus, $idAnlassTyp){
    $this->anlassBezeichnung = $anlassBezeichnung;
    $this->anlassDatumBis = $anlassDatumBis;
    $this->anlassDatumVon = $anlassDatumVon;
    $this->anlassKategorie = $anlassKategorie;
    $this->anlassLeitungIdPerson = $anlassLeitungIdPerson;
    $this->anlassNummer = $anlassNummer;
    $this->array_EventoAnlassLeitung = $array_EventoAnlassLeitung;
    $this->idAnlass = $idAnlass;
    $this->idAnlassKategorie = $idAnlassKategorie;
    $this->idAnlassNiveau = $idAnlassNiveau;
    $this->idAnlassStatus = $idAnlassStatus;
    $this->idAnlassTyp = $idAnlassTyp;
  }
}