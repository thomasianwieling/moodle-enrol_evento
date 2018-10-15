<?php
 /**
  * Unit tests for (some of) mod/quiz/editlib.php.
  *
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
  * @package question
  */

 if (!defined('MOODLE_INTERNAL')) {
     die('Direct access to this script is forbidden.'); //  It must be included from a Moodle page
 }

 // Make sure the code being tested is accessible.
 require_once($CFG->dirroot . '/mod/quiz/editlib.php'); // Include the code to test

 /** This class contains the test cases for the functions in editlib.php. */
 class mod_myplugin_sample_testcase extends advanced_testcase {
      public function test_adding() {
          $this->assertEquals(2, 1+2);
      }
  }
 ?>
