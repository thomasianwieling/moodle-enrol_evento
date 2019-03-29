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

interface interface_evento_service{
    /**
     * Doing a simple init Webservice call to open the connection
     *
     * @return boolean true if the request was successfully
     */
    public function init_call();

    /**
     * Obtains an event by the id-number
     *
     * @param  string $number the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */
    public function get_event_by_number($number);

    /**
     * Obtains events by filters
     *
     * @param  local_evento_eventoanlassfilter $eventoanlassfilter the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @param  local_evento_limitationfilter2 $limitationfilter2 filter for response limitation
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */

    /**
     * Obtains the enrolments of an event
     *
     * @param  string $eventid the evento eventid
     * @return array of stdClass event object "EventoPersonenAnmeldung" definied in the wsdl
     */
    public function get_enrolments_by_eventid($eventid);

    /**
     * Obtains the person details
     *
     * @param  string $personid the evento eventid
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_person_by_id($personid);

    /**
     * Obtains the Active Directory accountdetails
     *
     * @param  string $personid  the evento eventid
     * @param  bool   $isactive  true to get only active accounts; default null.
     * @param  bool   $isstudent true if you like to get students; default null.
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_ad_accounts_by_evento_personid($personid, $isactive = null, $isstudent=null);

    /**
     * Obtains the Active Directory accountdetails of students
     *
     * @param  bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_student_ad_accounts($isactive = null);


    /**
     * Obtains the Active Directory accountdetails of lecturers
     *
     * @param  bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_lecturer_ad_accounts($isactive = null);

    /**
     * Obtains the Active Directory accountdetails of employees
     *
     * @param  bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_employee_ad_accounts($isactive = null);

    /**
     * Obtains all the Active Directory accountdetails
     * of employees, lecturers, students
     *
     * @param  bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_all_ad_accounts($isactive = null);

    /**
     * Converts an AD SID to a shibboleth Id
     *
     * @param  string $sid sid of the user from the Active Directory
     * @return string shibboleth id
     */
    // public function sid_to_shibbolethid($sid);

    /**
     * Create an array if the value is not already one.
     * 0
     *
     * @param  var $value0
     * @return array of the $value
     */
    public static function to_array($value);
}
