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


 class mod_myplugin_sample_basic_testcase extends advanced_testcase {

   public function test_create_course_category()
   {
     global $DB;
     $this->resetAfterTest(true);
     $this->assertFalse(is_siteadmin());   // by default no user is logged-in
     $this->setUser(2);                    // switch $USER
     $this->assertTrue(is_siteadmin());    // admin is logged-in now
     $category2 = $this->getDataGenerator()->create_category(array('name'=>'Some subcategory'));
   }


   public function test_create_evento_course()
   {
     global $DB;
     $this->resetAfterTest(true);
     $this->assertFalse(is_siteadmin());   // by default no user is logged-in
     $this->setUser(2);                    // switch $USER
     $this->assertTrue(is_siteadmin());    // admin is logged-in no
     $category2 = $this->getDataGenerator()->create_category(array('name'=>'Hello'));
     $this->assertArrayHasKey('name', ['name' => 'Hello']);
     $this->getDataGenerator()->create_course(array('name'=>'hallo', 'category'=>$category2->id));
   }
 }
?>
