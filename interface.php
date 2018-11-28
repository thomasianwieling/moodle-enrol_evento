<?php
//require_once($CFG->dirroot . '/enrol/evento/tests/evento_service_generator.php');
//require_once($CFG->dirroot . '/local/evento/classes/evento_service.php');
interface interface_evento_service
{
  /**
   * Doing a simple init Webservice call to open the connection
   * @return boolean true if the request was successfully
  */
  public function init_call();

    /**
     * Obtains an event by the id-number
     * @param string $number the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */
    public function get_event_by_number($number);

    /**
     * Obtains events by filters
     * @param local_evento_eventoanlassfilter $eventoanlassfilter the evento event-number like "mod.bspEA2.HS16_BS.001"
     * @param local_evento_limitationfilter2 $limitationfilter2 filter for response limitation
     * @return stdClass event object "EventoAnlass" definied in the wsdl
     */
//    public function get_events_by_filter(local_evento_eventoanlassfilter $eventoanlassfilter, local_evento_limitationfilter2 $limitationfilter2);

    /**
     * Obtains the enrolments of an event
     * @param string $eventid the evento eventid
     * @return array of stdClass event object "EventoPersonenAnmeldung" definied in the wsdl
     */
    public function get_enrolments_by_eventid($eventid);

    /**
     * Obtains the person details
     * @param string $personid the evento eventid
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_person_by_id($personid);

    /**
     * Obtains the Active Directory accountdetails
     *
     * @param string $personid the evento eventid
     * @param bool $isactive true to get only active accounts; default null.
     * @param bool $isstudent true if you like to get students; default null.
     * @return stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_ad_accounts_by_evento_personid($personid, $isactive = null, $isstudent=null);

    /**
     * Obtains the Active Directory accountdetails of students
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_student_ad_accounts($isactive = null);


    /**
     * Obtains the Active Directory accountdetails of lecturers
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_lecturer_ad_accounts($isactive = null);

    /**
     * Obtains the Active Directory accountdetails of employees
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_employee_ad_accounts($isactive = null);

    /**
     * Obtains all the Active Directory accountdetails
     * of employees, lecturers, students
     *
     * @param bool $isactive true to get only active accounts; default null to get all.
     * @return array of stdClass person object "EventoPerson" definied in the wsdl
     */
    public function get_all_ad_accounts($isactive = null);

    /**
     * Converts an AD SID to a shibboleth Id
     *
     * @param string $sid sid of the user from the Active Directory
     * @return string shibboleth id
     */
  //  public function sid_to_shibbolethid($sid);

    /**
     * Converts a shibboleth ID to an Active Directory SID
     *
     * @param string $sishibbolethid shibbolethid of the user
     * @return string sid from the Active Directory
     */
  //  public function shibbolethid_to_sid($shibbolethid);

    /**
    * Create an array if the value is not already one.
    *
    * @param var $value
    * @return array of the $value
    */
    public static function to_array($value);
  }
