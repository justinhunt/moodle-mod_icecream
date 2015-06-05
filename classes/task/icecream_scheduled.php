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
 * The mod_page course module viewed event.
 *
 * @package    mod_icecream
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_icecream\task;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/icecream/lib.php');

/**
 * The mod_icecream course module viewed event class.
 *
 * @package    mod_icecream
 * @since      Moodle 2.7
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icecream_scheduled extends \core\task\scheduled_task {    
		
	public function get_name() {
        // Shown in admin screens
        return get_string('waitinglisttask', MOD_ICECREAM_LANG);
    }
	
	 /**
     *  Run all the tasks
     */
	 public function execute(){
		$trace = new \text_progress_trace();
        $icecream->mod_icecream_dotask($trace);
	}

}

