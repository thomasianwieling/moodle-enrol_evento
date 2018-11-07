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
require_once($CFG->dirroot . '/enrol/evento/classes/task/evento_member_sync_task.php');
//require_once($CFG->dirroot . '/enrol/evento/tests/local_evento_service_simulation.php');
require_once($CFG->dirroot . '/local/evento/classes/evento_service.php');
require_once($CFG->dirroot . '/enrol/evento/locallib.php');
require_once($CFG->dirroot . '/enrol/evento/tests/locallib_exposed.php');


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

   protected function setUp()
   {
     /*Create Moodle categories*/
     $this->cat1 = $this->getDataGenerator()->create_category();
     $this->cat2 = $this->getDataGenerator()->create_category();
     /*Create Object $locallib*/
     $this->locallib = new enrol_evento_user_sync_exposed();
   }
   /*Create courses*/
   protected function create_moodle_course()
   {
     $this->course1 = $this->getDataGenerator()->create_course(array('category'=>$this->cat1->id, 'idnumber' => 'mod.mmpAUKATE1.HS18_BS.001'));
   }
   /*Enable plugin method*/
   protected function enable_plugin()
   {
     $enabled = enrol_get_plugins(true);
     $enabled['evento'] = true;
     $enabled = array_keys($enabled);
     set_config('enrol_plugins_enabled', implode(',', $enabled));
   }
   /*disable plugin method*/
   protected function disable_plugin()
   {
     $enabled = enrol_get_plugins(true);
     unset($enabled['evento']);
     $enabled = array_keys($enabled);
     set_config('enrol_plugins_enabled', implode(',', $enabled));
   }
   /*get enroled user from course*/
   protected function get_enroled_user($id)
   {
     global $DB;
     $this->user_enrolment = $DB->get_record('enrol', array('courseid'=>$id, 'enrol'=>'evento'), '*', MUST_EXIST);
     $this->enrolments = $DB->count_records('user_enrolments', array('enrolid'=>$this->user_enrolment->id));
   }
   /*Basic test if plugin is enabled*/
   public function test_basics()
   {
     $this->resetAfterTest(true);
     $this->assertFalse(enrol_is_enabled('evento'));
     $this->enable_plugin();
     $plugin = 'evento';
     $evento_plugin = enrol_get_plugin($plugin);
     $this->assertEquals( $evento_plugin->get_name(), 'evento');
     $this->assertNotEmpty( $evento_plugin);
   }
   /*get_user() Test for a new user*/
   public function test_get_user()
   {
     global $DB;
     /*Reset after Test */
     $this->resetAfterTest(false);
     $this->assertFalse(enrol_is_enabled('evento'));
     $plugin = $this->enable_plugin();
     /*Get Plugin name*/
     $evento = new enrol_evento\task\evento_member_sync_task();
     $name = $evento->get_name();
     $this->assertEquals($name, 'Evento synchronisation');
     /*Get new user*/
     $eventoperson = $this->locallib->get_user_exposed(141703, $isstudent=true, $username=null);
//     $eventoperson = $this->locallib->get_user_exposed(117828, $isstudent=false, $username=null);
   }
   public function test_get_ad_user()
   {
     $this->resetAfterTest(false);
     $eventopersonid = 141703;
     $person = $this->locallib->get_ad_user_exposed($eventopersonid, $isstudent=null);
     $this->assertEquals(current($person)->sAMAccountName, 'burchmilena');
   }
   /*Get user by evento id test*/
   public function test_get_users_by_eventoid()
   {
     $this->resetAfterTest(false);
     $eventopersonid = 141703;
     $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
     $user = reset($person);
     $this->assertEquals($user->email, 'milena.burch@stud.htwchur.ch');
   }
    public function test_get_eventoid_by_userid()
    {
      $this->resetAfterTest(false);
      $eventopersonid = 141703;
      $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      $user = reset($person);
      $userid = $user->id;
      $personbyid = $this->locallib->get_eventoid_by_userid_exposed($userid);
      $this->assertEquals($eventopersonid, $personbyid);
    }
    public function test_get_user_by_username()
    {
      $this->resetAfterTest(false);
      $username = "2460181390-1097805571-3701207438-51315@fh-htwchur.ch";
      $person = $this->locallib->get_user_by_username_exposed($username);
      $this->assertEquals($person->username, $username);
    }
    /*Kurs einschreibung*/
    public function test_user_sync()
    {
      global $DB;
      $this->resetAfterTest(false);
      $this->create_moodle_course();
      $this->enable_plugin();
      /*create Object trace and enrol*/
      $trace = new null_progress_trace();
      $enrol = new enrol_evento_user_sync;
      /*Get the evento enrol plugin*/
      $plugin = 'evento';
      $evento_plugin = enrol_get_plugin($plugin);
      $courses = $DB->get_recordset_select('course', 'category > 0', null, '', 'id');
      foreach ($courses as $course)
      {
        $instanceid = null;
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $inst)
        {
          if ($inst->enrol == $plugin)
          {
            $instanceid = (int)$inst->id;
            break;
          }
        }
        if (empty($instanceid))
        {
          $instanceid = $evento_plugin->add_default_instance($course);
          if (empty($instanceid))
          {
            $instanceid = $evento_plugin->add_instance($course);
          }
        }
        if (!empty($instanceid))
        {
          // Do additional config of instance if needed.
          ($instanceid);
        }
      }

      $record = new stdClass();
      $record->id = 246003;
      $record->customint1 = 0;
      $DB->update_record('enrol', $record, $bulk=false);
      /*Enrol Users into courses*/
      $enrol->user_sync($trace, $courseid =null);
      /*Get user enrolment record to count enrolments*/
      $this->get_enroled_user($this->course1->id);
      $this->assertEquals($this->enrolments, 33, "Einschreibungen");
    }
    public function test_update_student_enrolment()
    {
      global $DB;
      $this->resetAfterTest(false);
      $eventoenrolstate = 20215;
      $eventopersonid =  141703;
      /*Get the user records*/
      $person = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      $user = reset($person);
      /*Get course settings*/
      $course = $DB->get_record('course', array('idnumber'=>'mod.mmpAUKATE1.HS18_BS.001'));
      $this->get_enroled_user($course->id);

      /*Delete a user to re-enrol after*/
      $DB->delete_records('user_enrolments', array('userid'=> current($person)->id));
      $DB->get_record('user_enrolments', array('userid'=> current($person)->id));
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 32);
      /*Get enrolment instance of course*/
      $instance = $DB->get_record('enrol', array('id' => $this->user_enrolment->id));
      /*re-enrol user*/
      $this->locallib->update_student_enrolment_exposed($eventopersonid, $eventoenrolstate, $instance);
      /*Count users*/
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 33);
    }
    public function test_enrol_teacher()
    {
      sleep(20);
      global $DB;
      $eventopersonid =  117828;
      $this->resetAfterTest(false);
      /*Get the teacher record*/
      $person_teacher = $this->locallib->get_users_by_eventoid_exposed($eventopersonid, $isstudent=null);
      /*Get the course settings*/
      $course = $DB->get_record('course', array('idnumber'=>'mod.mmpAUKATE1.HS18_BS.001'));
      $this->get_enroled_user($course->id);
      $user = $DB->get_record('user_enrolments', array('userid'=>current($person_teacher)->id));
      /*Delete the teacher to re-enroll after*/
      $id = $DB->delete_records('user_enrolments', array('userid'=>current($person_teacher)->id));
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 32);
      /**Get the course instance*/
      $instance = $DB->get_record('enrol', array('id'=>$this->user_enrolment->id));
      /*Re-enroll the teacher*/
      $teacher = $this->locallib->enrol_teacher_exposed($eventopersonid, $instance);
      $this->assertEquals($DB->count_records('user_enrolments', array('enrolid'=> $this->user_enrolment->id)), 33);
    }
    public function test_set_user_eventoid()
    {
      $this->resetAfterTest(false);
      $user = $this->getDataGenerator()->create_user();
      $eventoid = 12345;
      $this->locallib->set_user_eventoid_exposed($user->id, $eventoid);
      $user_evento = $this->locallib->get_users_by_eventoid_exposed($eventoid, $isstudent=null);
      $this->assertEquals(current($user_evento)->id, $user->id);
    }
  }
?>
