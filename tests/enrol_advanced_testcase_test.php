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
require_once($CFG->dirroot . '/local/evento/classes/evento_service.php');

 class mod_evento_advanced_testcase extends advanced_testcase {

   /** @var stdClass Plugin. */
   private $plugin;

   /*Enable evento enrol plugin*/
   private function enable_plugin()
   {
     $this->assertFalse(enrol_is_enabled('evento'));            // disabled by default
     $plugin = enrol_get_plugin('evento');                      // correct enrol instance
     $this->assertInstanceOf('enrol_evento_plugin', $plugin);
     return $plugin;
   }

   /*Basic test for enabling plugin*/
   public function test_basics()
   {
     $this->assertFalse(enrol_is_enabled('evento'));
     $plugin = $this->enable_plugin();
     $this->assertEquals($plugin->get_name(), 'evento');
   }



   public function test_create_course_category()
   {
     global $DB;
     $this->resetAfterTest(true);
     $this->assertFalse(is_siteadmin());   // by default no user is logged-in
     $this->setUser(2);                    // switch $USER
     $this->assertTrue(is_siteadmin());    // admin is logged-in now
     $course = $this->getDataGenerator()->create_course();
     $user = $this->getDataGenerator()->create_user();
     $this->getDataGenerator()->enrol_user($user->id, $course->id);
     $courses = get_courses();
     $this->assertEquals(2, count($courses));
     $user1 = $DB->get_record('user', array('id'=>$user->id));
     $this->assertEquals($user1, $user);
   }


   public function test_create_evento_user()
   {
     global $DB;
     $this->resetAfterTest(true);
     $this->enable_plugin();
     $eventoenrolplugin= task::get_name();
   }
 }
?>