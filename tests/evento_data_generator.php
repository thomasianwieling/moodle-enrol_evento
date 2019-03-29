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

class ad_account{
    public $accountstatusdisabled;
    public $changed;
    public $created;
    public $hasseveralaccounts;
    public $idperson;
    public $isemployeeaccount;
    public $islectureraccount;
    public $isstudentaccount;
    public $objectsid;
    public $sAMAccountName;

    public function __construct(int $accountstatusdisabled, string $changed, string $created, int $hasseveralaccounts, int $idperson, int $isemployeeaccount, int $islectureraccount, int $isstudentaccount, string $objectsid, string $sAMAccountName) {

        $this->accountstatusdisabled = $accountstatusdisabled;
        $this->changed = $changed;
        $this->created = $created;
        $this->hasseveralaccounts = $hasseveralaccounts;
        $this->idperson = $idperson;
        $this->isemployeeaccount = $isemployeeaccount;
        $this->islectureraccount = $islectureraccount;
        $this->isstudentaccount = $isstudentaccount;
        $this->objectsid = $objectsid;
        $this->sAMAccountName = $sAMAccountName;
    }
}

class evento_status{

    public $idstatus;
    public $statusname;
    public $aenderung;
    public $aenderungvon;
    public $erfassungvon;

    public function __construct( int $idstatus, string $statusname, string $aenderungvon, string $erfassungvon, string $aenderung) {
        $this->idstatus = $idstatus;
        $this->statusName = $statusname;
        $this->aenderungvon = $aenderungvon;
        $this->erfassungvon = $erfassungvon;
        $this->aenderung = $aenderung;
    }
}

class evento_anlass_typ{

    public $aenderung;
    public $aenderungvon;
    public $anlasstypaktiv;
    public $anlasstypbez;
    public $erfassung;
    public $erfassungvon;
    public $idanlasstyp;

    public function __construct(string $aenderung, string $aenderungvon, boolean $anlasstypaktiv, string $anlasstypbez, string $erfassung, string $erfassungvon, int $idanlasstyp) {

        $this->aenderung = $aenderung;
        $this->aenderungVon = $aenderungvon;
        $this->anlasstypaktiv = $anlasstypaktiv;
        $this->anlasstypbez = $anlasstypbez;
        $this->erfassung = $erfassung;
        $this->erfassungvon = $erfassungvon;
        $this->idanlasstyp = $idanlasstyp;
    }
}

class evento_personen_anmeldung{

    public $idanmeldung;
    public $iDPAStatus;
    public $idanlass;
    public $idperson;
    public $personenanmeldungstatus;
    public $aenderung;
    public $aenderungvon;
    public $erfassung;
    public $erfassungvon;

    public function __construct(string $aenderung, string $aenderungvon, string $erfassung, string $erfassungvon, int $idanmeldung, int $iDPAStatus, int $idanlass, int $idperson, object $personenanmeldungstatus) {

        $this->aenderung = $aenderung;
        $this->aenderungvon = $aenderungvon;
        $this->erfassung = $erfassung;
        $this->erfassungvon = $erfassungvon;
        $this->idanmeldung = $idanmeldung;
        $this->iDPAStatus = $iDPAStatus;
        $this->idanlass = $idanlass;
        $this->idperson = $idperson;
        $this->personenanmeldungstatus = $personenanmeldungstatus;
    }
}

class evento_person{

    public $personnachname;
    public $personvorname;
    public $personemail;
    public $idperson;
    public $idpersonstatus;
    public $personaktiv;
    public $personkorridperson;
    public $personenanmeldung;

    public function __construct(string $personnachname, string $personvorname, string $personemail, int $idperson, int $idpersonstatus,  $personaktiv, int $personkorridperson, object $personenanmeldung)
    {
        $this->personnachname = $personnachname;
        $this->personvorname = $personvorname;
        $this->personemail = $personemail;
        $this->idperson = $idperson;
        $this->idpersonstatus = $idpersonstatus;
        $this->personaktiv = $personaktiv;
        $this->personkorridperson = $personkorridperson;
        $this->personenanmeldung = $personenanmeldung;
    }
}

class anlass_leitung_rolle
{
    public $anlassltgrolleaktiv;
    public $anlassltgrollebezeichnung;
    public $anlassltgrollebezeichnungkrz;
    public $anlassltgrollebezeichnungsort;
    public $idanlassltgrolle;
    public $aenderung;
    public $aenderungvon;
    public $erfassung;
    public $erfassungvon;

    public function __construct( boolean $anlassltgrolleaktiv, string $anlassltgrollebezeichnung,  string $anlassltgrollebezeichnungkrz, string $anlassltgrollebezeichnungsort, int $idanlassltgrolle, string $aenderung, string $aenderungvon, string $erfassung, string $erfassungvon) {

        $this->anlassLtgrolleaktiv = $anlassltgrolleaktiv;
        $this->anlassltgrollebezeichnung = $anlassltgrollebezeichnung;
        $this->anlassltgrollebezeichnungkrz = $anlassltgrollebezeichnungkrz;
        $this->anlassltgrollebezeichnungsort = $anlassltgrollebezeichnungsort;
        $this->idanlassltgrolle = $idanlassltgrolle;
        $this->aenderung = $aenderung;
        $this->aenderungvon = $aenderungvon;
        $this->erfassung = $erfassung;
        $this->erfassungvon = $erfassungvon;
    }
}

class evento_anlass_leitung{

    public $anlassleitungrolle;
    public $anlassltgidanlass;
    public $anlassltgidanlassltgrolle;
    public $anlassltgidperson;
    public $anlassltgperson;
    public $aenderung;
    public $aenderungvon;
    public $erfassung;
    public $erfassungvon;
    public $idanlassltg;

    public function __construct(anlass_ltg_person $anlassleitungrolle, int $anlassltgidanlass, int $anlassltgidanlassltgrolle, int $anlassltgidperson, anlass_ltg_person $anlassltgperson, string $aenderung, string $aenderungvon, string $erfassung, string $erfassungvon, int $idanlassltg) {

        $this->anlassleitungrolle = $anlassleitungrolle;
        $this->anlassltgidanlass = $anlassltgidanlass;
        $this->anlassltgidanlassltgrolle = $anlassltgidanlassltgrolle;
        $this->anlassltgidperson = $anlassltgidperson;
        $this->anlassltgperson = $anlassltgperson;
        $this->aenderung = $aenderung;
        $this->aenderungvon = $aenderungvon;
        $this->erfassung = $erfassung;
        $this->erfassungvon = $erfassungvon;
        $this->idanlassltg = $idanlassltg;
    }
}



class evento_anlass_kategorie{

    public $anlasskategorieaktiv;
    public $idanlasskategorie;
    public $aenderung;
    public $aenderungvon;
    public $erfassung;
    public $erfassungvon;

    public function __construct(boolean $anlasskategorieaktiv, int $idanlasskategorie, string $aenderung, string $aenderungvon, string $erfassung, string $erfassungvon) {

        $this->anlasskategorieaktiv = $anlasskategorieaktiv;
        $this->idanlasskategorie = $idanlasskategorie;
        $this->aenderung = $aenderung;
        $this->aenderungvon = $aenderungvon;
        $this->erfassung = $erfassung;
        $this->erfassungvon = $erfassungvon;
    }
}


class evento_anlass{

    public $anlassbezeichnung;
    public $anlassdatumbis;
    public $anlassdatumvon;
    public $anlasskategorie;
    public $anlassleitungidperson;
    public $anlassnummer;
    public $arrayeventoanlassleitung;
    public $idanlass;
    public $idanlasskategorie;
    public $idanlassniveau;
    public $idanlassstatus;
    public $idanlasstyp;

    public function __construct($anlassbezeichnung, $anlassdatumbis, $anlassdatumvon, $anlasskategorie, $anlassleitungidperson, $anlassnummer, $arrayeventoanlassleitung, $idanlass, $idanlasskategorie, $idanlassniveau, $idanlassstatus, $idanlasstyp) {

        $this->anlassbezeichnung = $anlassbezeichnung;
        $this->anlassdatumbis = $anlassdatumbis;
        $this->anlassdatumvon = $anlassdatumvon;
        $this->anlasskategorie = $anlasskategorie;
        $this->anlassleitungidperson = $anlassleitungidperson;
        $this->anlassnummer = $anlassnummer;
        $this->arrayeventoanlassleitung = $arrayeventoanlassleitung;
        $this->idanlass = $idanlass;
        $this->idanlasskategorie = $idanlasskategorie;
        $this->idanlassniveau = $idanlassniveau;
        $this->idanlassstatus = $idanlassstatus;
        $this->idanlasstyp = $idanlasstyp;
    }
}
