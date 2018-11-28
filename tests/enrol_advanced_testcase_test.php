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

   protected function setUp(){
     /*Create Moodle categories*/
     $this->cat1 = $this->getDataGenerator()->create_category();
     $this->cat2 = $this->getDataGenerator()->create_category();
     /*Create Object $locallib*/


     $builder = new builder;
     /*Create Evento Course*/
     $evento_anlass = $builder->add_anlass("Audio- & Kameratechnik 1", "2019-02-17T00:00:00.000+01:00", "2018-09-17T00:00:00.000+02:00", null, 117828, "mod.mmpAUKATE1.HS18_BS.001", null, 25490, 1, 60, 10230, 3 );
     $evento_anlass = $builder->add_anlass("Audio- & Kameratechnik 2", "2019-02-17T00:00:00.000+01:00", "2018-09-17T00:00:00.000+02:00", null, 117828, "mod.mmpAUKATE1.HS18_BS.002", null, 25490, 1, 60, 10230, 3 );

     /**/
     $evento_status = $builder->add_evento_status(20215, "aA.Angemeldet", "BI_gzap", "auto", "2008-07-04T10:03:23.000+02:00");
     /*Create evento person Hans Meier*/
     $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00","auto" ,415864, 20215, 25490, 141703, $evento_status);
     $evento_person = $builder->add_person("Meier", "Hans", "hans.meier@stud.htwchur.ch",  141703, 30040, true, 141703, $evento_personen_anmeldung);
     $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 141703, 0, 0, 1, "S-1-5-21-2460181390-1097805571-3701207438-51315", "HanMei");
     /*create evento person Max Muster*/
     $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00","auto" ,415864, 20215, 25491, 100001, $evento_status);
     $evento_person = $builder->add_person("Muster", "Max", "max.muster@stud.htwchur.ch",  100001, 30040, true, 100001, $evento_personen_anmeldung);
     $ad_account = $builder->add_ad_account(0, "2019-02-17T00:00:00.000+01:00", "2019-02-17T00:00:00.000+01:00", 0, 100001, 0, 0, 1, "S-1-5-21-2460181391-1097805571-3701207438-51315", "MaxMuster");

     $evento_status = $builder->add_evento_status(20214, "aA.Angemeldet", "BI_gzap", "auto", "2008-07-04T10:03:23.000+02:00");
     $evento_personen_anmeldung = $builder->add_personen_anmeldung("2019-02-17T00:00:00.000+01:00", "hoferlis", "2018-06-05T08:58:20.723+02:00","auto" ,415864, 20214, 25491, 141703, $evento_status);

     $this->simulator =$builder->service;
     $this->locallib = new enrol_evento_user_sync_exposed($this->simulator);
     $this->resetAfterTest(false);
   }
   /*Create courses*/
   protected function create_moodle_course(){
     $plugin = 'evento';
     $evento_plugin = enrol_get_plugin($plugin);
     $course1 = $this->getDataGenerator()->create_course(array('category'=>$this->cat1->id, 'idnumber' => 'mod.mmpAUKATE1.HS18_BS.001'));
     $instanceid = $evento_plugin->add_default_instance($course1);
   }
   /*Enable plugin method*/
   protected function enable_plugin(){
     $enabled = enrol_get_plugins(true);
     $enabled['evento'] = true;
     $enabled = array_keys($enabled);
     set_config('enrol_plugins_enabled', implode(',', $enabled));
   }
   /*disable plugin method*/
   protected function disable_plugin(){
     $enabled = enrol_get_plugins(true);
     unset($enabled['evento']);
     $enabled = array_keys($enabled);
     set_config('enrol_plugins_enabled', implode(',', $enabled));
   }
   /*get enroled user from course*/
   protected function get_enroled_user($id){
     global $DB;
     $this->user_enrolment = $DB->get_record('enrol', array('courseid'=>$id, 'enrol'=>'evento'), '*', MUST_EXIST);
     $this->enrolments = $DB->count_records('user_enrolments', array('enrolid'=>$this->user_enrolment->id));
   }

   /*Simuation test if plugin is enabled*/
   /**
   * @test
   */
   public function basic(){
     $anlass = $this->simulator->get_event_by_number("mod.mmpAUKATE1.HS18_BS.002");
     $personen_anmeldung = $this->simulator->get_enrolments_by_eventid(25490);
     //var_dump($personen_anmeldung);
     $personen_anmeldung = $this->simulator->get_enrolments_by_eventid(25491);
     //var_dump($personen_anmeldung);
     $person = $this->simulator->get_person_by_id(141703);
     //var_dump($person);
     $ad_account = $this->simulator->get_ad_accounts_by_evento_personid(141701, null, null);
     //var_dump($ad_account);
     $ad_account_student = $this->simulator->get_all_ad_accounts(null);
     //var_dump($ad_account_student);
}

   /*Basic test if plugin is enabled*/
   /**
   * @test
   */
   public function basics(){

     $this->resetAfterTest(true);

     $this->enable_plugin();
     $plugin = 'evento';
     $evento_plugin = enrol_get_plugin($plugin);

     $this->assertEquals( $evento_plugin->get_name(), 'evento');
     $this->assertNotEmpty( $evento_plugin);
   }
   /*get_user() Test for a new user*/
   /**
   * @test
   */
   public function get_user(){
     /*set global DB variable*/
     global $DB;

     /*Get Plugin name*/
     $evento = new enrol_evento\task\evento_member_sync_task();
     $name = $evento->get_name();

     /*Get new user*/
     $eventoperson = $this->locallib->get_user_exposed(141703, $isstudent=true, $username=null);
     $eventoperson = $this->locallib->get_user_exposed(100001, $isstudent=true, $username=null);

     /*Get Database Records*/
     $user1 = $DB->get_record('user', array('username'=>'2460181390-1097805571-3701207438-51315@fh-htwchur.ch'));
     $user2 = $DB->get_record('user', array('username'=>'2460181391-1097805571-3701207438-51315@fh-htwchur.ch'));

     /*^Database Record equals new user*/
     $this->assertEquals($name, 'Evento synchronisation');
     $this->assertEquals($user1->email, 'hans.meier@stud.htwchur.ch');
     $this->assertEquals($user2->email, 'max.muster@stud.htwchur.ch');
   }

   /**
   * @test
   */
   public function get_ad_user()
   {
     /*set evento person ID*/
     $eventopersonid = 141703;
     /*Get ad User*/
     $person = $this->locallib->get_ad_user_exposed($eventopersonid, $isstudent=null);
     /*Accountname  equals ad username*/
     $this->assertEquals(current($person)->sAMAccountName, 'HanMei');
   }

   /*Get user by evento id test*/
   /**
   * @test
   */
   public function get_users_by_eventoid()
   {
     /*Set the evento person ID*/
     $eventopersonid = 100001;
     /*Get the user by evento ID*/
     $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
     $user = reset($person);
     /*Evento user email equals email adress*/
     $this->assertEquals($user->email, 'max.muster@stud.htwchur.ch');
   }

   /**
   * @test
   */
    public function get_eventoid_by_userid()
    {
      /*set evento person id*/
      $eventopersonid = 141703;
      /*Get user by evento person ID for user ID*/
      $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      $user = reset($person);
      $userid = $user->id;
      /*get the evento Person ID by user ID*/
      $personbyid = $this->locallib->get_eventoid_by_userid_exposed($userid);
      /*person by ID equals evento person ID*/
      $this->assertEquals($eventopersonid, $personbyid);
    }

    /**
    * @test
    */
    public function get_user_by_username()
    {
      /*set username*/
      $username = "2460181390-1097805571-3701207438-51315@fh-htwchur.ch";
      /*get user by username*/
      $person = $this->locallib->get_user_by_username_exposed($username);
      /*username from method equals username*/
      $this->assertEquals($person->username, $username);
    }

    /*Kurs einschreibung*/
    /**
    * @test
    */
    public function user_sync()
    {
      /*Set global DB variable*/
      global $DB;
      /*create moodle course and enable plugin*/
      $this->create_moodle_course();
      $this->enable_plugin();
      /*create Object trace and enrol*/
      $trace = new null_progress_trace();
  //    $enrol = new enrol_evento_user_sync;
      /*Get course records and add enrol instances*/
      $courses = $DB->get_recordset_select('course', 'category > 0', null, '', 'id');
      foreach ($courses as $course)
      {
        $instanceid = null;
        $instances = enrol_get_instances($course->id, true);
      }
      /*Enrol Users into courses*/
       $this->locallib->user_sync($trace, $courseid =null);
      /*Get user enrolment record to count enrolments*/
      $this->get_enroled_user($course->id);
      $this->assertEquals($this->enrolments, 1, "Einschreibungen");
    }

    /*Student enrolment update*/
    /**
    * @test
    */
/*    public function update_student_enrolment()
    {
      global $DB;

      $eventoenrolstate = 20215;
      $eventopersonid =  141703;
      /*Get the user records*/
/*      $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      $user = reset($person);
      /*Get course settings*/
/*      $course = $DB->get_record('course', array('idnumber'=>'mod.mmpAUKATE1.HS18_BS.001'));
      $this->get_enroled_user($course->id);

      /*Delete a user to re-enrol after*/
/*      $DB->delete_records('user_enrolments', array('userid'=> current($person)->id));
      $DB->get_record('user_enrolments', array('userid'=> current($person)->id));
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 2);
      /*Get enrolment instance of course*/
/*      $instance = $DB->get_record('enrol', array('id' => $this->user_enrolment->id));
      /*re-enrol user*/
/*      $this->locallib->update_student_enrolment_exposed($eventopersonid, $eventoenrolstate, $instance);
      /*Count users*/
/*      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 3);
    }

    /*Teacher enrolment*/
    /**
    * @test
    */
/*    public function enrol_teacher()
    {
      global $DB;
      $eventopersonid =  117828;

      /*Get the teacher record*/
/*      $person_teacher = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      /*Get the course settings*/
/*      $course = $DB->get_record('course', array('idnumber'=>'mod.mmpAUKATE1.HS18_BS.001'));
      $this->get_enroled_user($course->id);
      $user = $DB->get_record('user_enrolments', array('userid'=>current($person_teacher)->id));
      /*Delete the teacher to re-enroll after*/
/*      $id = $DB->delete_records('user_enrolments', array('userid'=>current($person_teacher)->id));
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 2);
      /**Get the course instance*/
/*      $instance = $DB->get_record('enrol', array('id'=>$this->user_enrolment->id));
      /*Re-enroll the teacher*/
/*      $teacher = $this->locallib->enrol_teacher_exposed($eventopersonid, $instance);
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 3);
    }

    /*Set evento id to user*/
    /**
    * @test
    */
/*    public function set_user_eventoid()
    {

      $user = $this->getDataGenerator()->create_user();
      $eventoid = 12345;
      $this->locallib->set_user_eventoid_exposed($user->id, $eventoid);
      $user_evento = $this->locallib->get_users_by_eventoid_exposed($eventoid, $isstudent=null);
      $this->assertEquals(current($user_evento)->id, $user->id);
    }*/
  }
?>
