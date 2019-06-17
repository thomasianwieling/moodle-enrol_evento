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
 * @copyright  2018 HTW Chur Thomas Wieling
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/enrol/evento/interface.php');
require_once($CFG->dirroot . '/enrol/evento/tests/locallib_exposed.php');
require_once($CFG->dirroot . '/enrol/evento/tests/builder.php');

class mod_evento_advanced_testcase extends advanced_testcase {
    /** @var stdClass Instance. */
    private $instance;
    /** @var stdClass Student. */
    private $student;
    /** @var stdClass First course. */
    private $course1;
    /** @var stdClass Second course. */
    private $course2;
    /** @var stdClass Second course. */
    private $cat1;
    /** @var stdClass Second course. */
    private $cat2;
    /** @var stdClass Plugin. */
    private $plugin;
    /** @var stdClass Plugin. */
    private $locallib;
    /** @var stdClass Plugin. */
    private $user_enrolment;
    /** @var stdClass Plugin. */
    private $enrolments;
    /** @var stdClass Plugin. */
    private $simulator;

    /*Create courses*/
    protected function create_moodle_course() {
        $plugin = 'evento';
        $evento_plugin = enrol_get_plugin($plugin);
        $course1 = $this->getDataGenerator()->create_course(array('category' => $this->cat1->id, 'idnumber' => 'mod.mmpAUKATE1.HS18_BS.001'));
        $instanceid = $evento_plugin->add_default_instance($course1);
    }

    protected function setUp() {
        global $DB;
        /*Create Moodle categories*/
        $this->cat1 = $this->getDataGenerator()->create_category();
        $this->cat2 = $this->getDataGenerator()->create_category();
        /*Create Object $locallib*/

        $builder = new builder;
        /*Create Evento Course*/
        $evento_anlass = $builder->add_anlass("Audio- & Kameratechnik 1", "2019-02-17T00:00:00.000+01:00", "2018-09-17T00:00:00.000+02:00", null, 117829, "mod.mmpAUKATE1.HS18_BS.001", null, 25490, 1, 60, 10230, 3 );
        $evento_anlass = $builder->add_anlass("Audio- & Kameratechnik 2", "2019-02-17T00:00:00.000+01:00", "2018-09-17T00:00:00.000+02:00", null, 117828, "mod.mmpAUKATE1.HS18_BS.002", null, 25491, 1, 60, 10230, 3 );

        /**/
        $evento_status = $builder->add_evento_status(20215, "aA.Angemeldet", "BI_gzap", "auto", "2008-07-04T10:03:23.000+02:00");
        /*Create evento person Hans Meier*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto", 415864, 20215, 25490, 141703, $evento_status);
        $evento_person = $builder->add_person("Meier", "Hans", "hans.meier@stud.htwchur.ch",  141703, 30040, true, 141703, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 141703, 0, 0, 1, "S-1-5-21-2460181390-1097805571-3701207438-51315", "HanMei");

        /*create evento person Max Muster*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto", 415864, 20215, 25490, 117828, $evento_status);
        $evento_person = $builder->add_person("Muster", "Max", "max.muster@stud.htwchur.ch",  117828, 30040, true, 117828, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 117828, 0, 0, 1, "S-1-5-21-2460181391-1097805571-3701207438-51315", "MusMax");

        /*create evento person Peter Mann*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto" , 415864, 20215, 25490, 117829, $evento_status);
        $evento_person = $builder->add_person("Mann", "Peter", "peter.mann@stud.htwchur.ch",  117829, 30040, true, 117829, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 117829, 0, 0, 1, "S-1-5-21-2460181392-1097805571-3701207438-51315", "ManPet");

        /*create evento person Peter Mann*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto" , 415864, 20215, 25490, 117999, $evento_status);
        $evento_person = $builder->add_person("Muster", "Fritz", "peter.mann@stud.htwchur.ch",  117999, 30040, true, 117999, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 117999, 0, 0, 1, "S-1-5-21-2460181393-1097805571-3701207438-51315", "MusFri");

        /*create teacher person*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto" , 415864, 20216, 25491, 118000, $evento_status);
        $evento_person = $builder->add_person("Teacher", "Mister", "mister.teacher@stud.htwchur.ch",  118000, 30040, true, 118000, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 118000, 0, 1, 0, "S-1-5-21-2460181394-1097805571-3701207438-51315", "MisTe");

        /*create existing moodle user*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto" , 415864, 20216, 25491, 118200, $evento_status);
        $evento_person = $builder->add_person("Max", "Fritz", "max.fritz@htwchur.ch",  118200, 30040, true, 118200, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 118200, 0, 0, 1, "S-1-5-21-2460181394-1097805571-3701207438-51000", "MaFri");

        $this->simulator = $builder->service;
        $this->locallib = new enrol_evento_user_sync_exposed($this->simulator);
        $this->resetAfterTest(true);
        $this->create_moodle_course();

        $user1 = $this->getDataGenerator()->create_user(array('email' => 'max.fritz@htwchur.ch', 'username' => '2460181394-1097805571-3701207438-51000@htwchur.ch', 'firstname' => 'Max', 'lastname' => 'Fritz', 'timecreated' => 1548078299, 'timemodified' => 1548078299));
        $result = $DB->get_records('user', array('lastname' => 'Fritz'));

        $item = new \stdClass();
        $item->userid = reset($result)->id;
        $item->data = (string)118200;
        $item->dataformat = 0;
        $item->fieldid = 1;
        $uiditem = $DB->insert_record('user_info_data', $item);
        //$result = $DB->get_records('user_info_data', array('userid' => reset($result)->id));

        /*create new moodle user*/
        $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00", "auto" , 415864, 20216, 25491, 118201, $evento_status);
        $evento_person = $builder->add_person("Hanspeter", "Mueller", "hanspeter.mueller@htwchur.ch",  118201, 30040, true, 118201, $evento_personen_anmeldung);
        $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 118201, 0, 0, 1, "S-1-5-21-2460181395-1097805571-3701207438-51000", "MaFri");



    }

    /*Enable plugin method*/
    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['evento'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    /*Disable plugin method*/
    protected function disable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    /*Get enroled user from course*/
    protected function get_enroled_user($id) {
        global $DB;
        $this->user_enrolment = $DB->get_record('enrol', array('courseid' => $id, 'enrol' => 'evento'), '*', MUST_EXIST);
        $this->enrolments = $DB->count_records('user_enrolments', array('enrolid' => $this->user_enrolment->id));
    }

    /*Get mail from person with person id*/
    protected function get_mail_from_person_id($personid) {
        $evento_personen = $this->simulator->evento_personen;

        foreach ($evento_personen as $evento_person) {
            if ($personid == $evento_person->idperson) {
                return $evento_person->personemail;
            }
        }
    }

    /*Get username from person with personid*/
    protected function get_username_from_person_id($personid) {
        $ad_accounts = $this->simulator->ad_accounts;

        foreach ($ad_accounts as $ad_account) {
            if ($personid == $ad_account->idperson) {
                return $ad_account->objectsid;
            }
        }
    }

    protected function get_s_am_accountname($personid){
        $ad_accounts = $this->simulator->ad_accounts;

        foreach ($ad_accounts as $ad_account) {
            if ($personid == $ad_account->idperson) {
                return $ad_account->sAMAccountName;
            }
        }
    }

    /*Simuation test if plugin is enabled*/
    /**
     * @test
     */
    public function basic() {
        $anlass = $this->simulator->get_event_by_number("mod.mmpAUKATE1.HS18_BS.002");
        $personenanmeldung = $this->simulator->get_enrolments_by_eventid(25490);
        $personenanmeldung = $this->simulator->get_enrolments_by_eventid(25490);

        $person = $this->simulator->get_person_by_id(141703);
        $person = $this->simulator->get_person_by_id(117828);
        $ad_account = $this->simulator->get_ad_accounts_by_evento_personid(141701, null, null);
        $ad_account_student = $this->simulator->get_all_ad_accounts(null);
    }

    /*Basic test if plugin is enabled*/
    /**
     * @test
     */
    public function basics() {

        $this->resetAfterTest(true);

        $this->enable_plugin();
        $plugin = 'evento';
        $evento_plugin = enrol_get_plugin($plugin);

        $this->assertEquals( $evento_plugin->get_name(), 'evento');
        $this->assertNotEmpty( $evento_plugin);
    }

    /**
    * @test
    */
   public function get_ad_user() {
       /*set evento person ID*/
       $eventoid = 141703;
       /*Get ad User*/
       $person = $this->locallib->get_ad_user_exposed($eventopersonid, $isstudent = null);
       /*Accountname  equals ad username*/
       $this->assertEquals(current($person)->sAMAccountName, $this->get_s_am_accountname($personid));
   }

    /* Test that get_user returns an **existing** user with given evento */
    /**
     * @test
     */
     public function get_user_existing_user() {
         $this->resetAfterTest(true);
         global $DB;

         $eventoid = 118200;
        // var_dump("getuserexistinguser");
         /*Get user by evento person ID for user ID*/
        $person = $this->locallib->get_user_exposed($eventoid);
        var_dump(reset($person));

        $this->assertEquals(reset($person)->email, $this->get_mail_from_person_id($eventoid));

    }

    public function get_user_new_user() {
        $this->resetAfterTest(true);
        global $DB;

        $eventoid = 118201;
       // var_dump("getuserexistinguser");
        /*Get user by evento person ID for user ID*/
       $person = $this->locallib->get_user_exposed($eventoid);
       var_dump(reset($person));

       $this->assertEquals(reset($person)->email, $this->get_mail_from_person_id($eventoid));

   }


}
