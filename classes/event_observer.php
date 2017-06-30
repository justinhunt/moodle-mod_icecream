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
 * Event observer for icecream plugin
 *
 * @package    mod_icecream
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 namespace mod_icecream;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot .'/mod/icecream/lib.php');


/**
 * Event observer for mod_icecream
 *
 * @package    mod_icecream
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_observer{

    /**
     * Triggered via course_deleted event.
     *
     * @param \core\event\course_deleted $event
     * @return bool true on success
     */
    public static function course_deleted(\core\event\course_deleted $event) {
       global $DB;
		//MOD_ICECREAM_TABLE should be deleted elsewhere
		//this is just to demonstrate how to handle an event. 
		//It is probably not even necessary to clear data from here when a course is deleted.
		$ret = $DB->delete_records(MOD_ICECREAM_USERTABLE,array('courseid'=>$event->objectid));
		return $ret;
	}
}
