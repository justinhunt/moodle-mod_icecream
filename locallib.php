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
 * Private Icecream Module utility functions
 *
 * @package mod_icecream
 * @copyright  2015 Justin Hunt (http://poodll.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/icecream/lib.php");
require_once($CFG->libdir.'/formslib.php');


class mod_icecream_cloneform extends moodleform {

    function definition() {
        global $DB;
		list($allactivities) = $this->_customdata;
        $mform = $this->_form;
		$mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);
        $mform->addElement('textarea', 'manualinput', get_string('manualinput', MOD_ICECREAM_LANG));
		$mform->setType('manualinput', PARAM_TEXT);
		$mform->addElement('select', 'clonefrom',get_string('clonefrom',MOD_ICECREAM_LANG),$allactivities,array('onchange'=>'M.mod_icecream.doclonefrom()'));
		$mform->addElement('static', 'clonefromsummarycode',null,'<div class="mod_icecream_divcontainer"><div id="id_clonefromsummary"></div><div id="id_clonefromcode"></div></div>');
		$mform->addElement('select', 'cloneto',get_string('cloneto',MOD_ICECREAM_LANG),$allactivities,array('onchange'=>'M.mod_icecream.docloneto()'));
		$mform->addElement('static', 'clonetosummarycode',null,'<div class="mod_icecream_divcontainer"><div id="id_clonetosummary"></div><div id="id_clonetocode"></div></div>');
        $this->add_action_buttons();
    }
}

class mod_icecream_helper{

	
	static function get_all_activities($course){
		global $DB;
	
		$modinfo = get_fast_modinfo($course);
		$cms = $modinfo->get_cms();
		$alldata = array();	
		foreach($cms as $cm){
				$alldata[$cm->id]= '(' . $cm->id . ') ' . $cm->name;
		}
		return $alldata;
	}
	
	static function get_availabilitysummary($course){
		$alldata = array();
		$modinfo = get_fast_modinfo($course);
		$cms = $modinfo->get_cms();
		foreach($cms as $cm){
			$infoobject = new \core_availability\info_module($cm);
			$alldata[$cm->id] = $infoobject->get_full_information();
		}
		return $alldata;
	}
	
	static function get_availabilitycode($course){
		$alldata = array();
		$modinfo = get_fast_modinfo($course);
		$cms = $modinfo->get_cms();
		foreach($cms as $cm){
			$alldata[$cm->id] = $cm->availability;
		}
		return $alldata;
	}

	
}
