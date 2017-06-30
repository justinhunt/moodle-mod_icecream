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
 * Prints a particular instance of icecream
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_icecream
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');


$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // icecream instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('icecream', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance  = $DB->get_record('icecream', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $moduleinstance  = $DB->get_record('icecream', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('icecream', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$PAGE->set_url('/mod/icecream/view.php', array('id' => $cm->id));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);
require_capability('mod/icecream:preview',$modulecontext);

//Diverge logging logic at Moodle 2.7
if($CFG->version<2014051200){
	add_to_log($course->id, 'icecream', 'view', "view.php?id={$cm->id}", $moduleinstance->name, $cm->id);
}else{
	// Trigger module viewed event.
	$event = \mod_icecream\event\course_module_viewed::create(array(
	   'objectid' => $moduleinstance->id,
	   'context' => $modulecontext
	));
	$event->add_record_snapshot('course_modules', $cm);
	$event->add_record_snapshot('course', $course);
	$event->add_record_snapshot('icecream', $moduleinstance);
	$event->trigger();
} 

//if we got this far, we can consider the activity "viewed"
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

//are we a teacher or a student?
$mode= "view";
$success='';

$allactivities = mod_icecream_helper::get_all_activities($course);
$availabilitysummary = mod_icecream_helper::get_availabilitysummary($course);
$availabilitycode= mod_icecream_helper::get_availabilitycode($course);

$allsections = mod_icecream_helper::get_all_sections($course);
$sectionavailabilitysummary = mod_icecream_helper::get_section_availabilitysummary($course);
$sectionavailabilitycode= mod_icecream_helper::get_section_availabilitycode($course);

$mform = new mod_icecream_cloneform(null,array($allactivities));
//if the cancel button was pressed, we are out of here
if (!$mform->is_cancelled()) {
    //if we have data, then our job here is to save it;
	if ($formdata = $mform->get_data()) {
		$newavailability=false;
		$manualinput = $formdata->manualinput;
		$manualinput = $manualinput; 
		if(!empty(trim($manualinput))){
			$newavailability = $formdata->manualinput;
		}else{
			$original = $DB->get_record('course_modules',array('id'=>$formdata->clonefrom));
			if($original){
				$newavailability = $original->availability;
			 }
		}
		if($newavailability){
			foreach($formdata->cloneto as $theitem){
				$DB->set_field('course_modules', 'availability', $newavailability, array('id' => $theitem));
			}
			$success='yes';
			rebuild_course_cache($course->id);
			redirect($PAGE->url,get_string('updatedsettings',MOD_ICECREAM_LANG),3); 
			return;
		}else{
			$success='no';
		}
	}
}

$mform = new mod_icecream_sectioncloneform(null,array($allsections));
//if the cancel button was pressed, we are out of here
if (!$mform->is_cancelled()) {
    //if we have data, then our job here is to save it;
	if ($formdata = $mform->get_data()) {
		$newavailability=false;
		$manualinput = $formdata->manualinput;
		$manualinput = $manualinput; 
		if(!empty(trim($manualinput))){
			$newavailability = $formdata->manualinput;
		}else{
			$original = $DB->get_record('course_sections',array('course'=>$COURSE->id,'section'=>$formdata->sectionclonefrom));
			if($original){
				$newavailability = $original->availability;
			 }
		}
		if($newavailability){
			foreach($formdata->sectioncloneto as $thesection){
				$DB->set_field('course_sections', 'availability', $newavailability, 				array('course'=>$COURSE->id,
				'section'=>$thesection));
			}
			$success='yes';
			rebuild_course_cache($course->id);
			redirect($PAGE->url,get_string('updatedsettings',MOD_ICECREAM_LANG),3); 
			return;
		}else{
			$success='no';
		}
	}
}

/// Set up the page header
$PAGE->set_url('/mod/icecream/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');



//get our javascript all ready to go
//We can omit $jsmodule, but its nice to have it here, 
//if for example we need to include some funky YUI stuff
$jsmodule = array(
	'name'     => 'mod_icecream',
	'fullpath' => '/mod/icecream/module.js',
	'requires' => array()
);
//here we set up any info we need to pass into javascript
$opts =Array('availabilitysummary'=>$availabilitysummary,'availabilitycode'=>$availabilitycode,'sectionavailabilitysummary'=>$sectionavailabilitysummary, 'sectionavailabilitycode'=>$sectionavailabilitycode );

//this inits the M.mod_icecream thingy, after the page has loaded.
$PAGE->requires->js_init_call('M.mod_icecream.init', array($opts),false,$jsmodule);


//This puts all our display logic into the renderer.php file in this plugin
//theme developers can override classes there, so it makes it customizable for others
//to do it this way.
$renderer = $PAGE->get_renderer('mod_icecream');

//From here we actually display the page.
//this is core renderer stuff


//if we are teacher we see tabs. If student we just see the quiz
if(has_capability('mod/icecream:preview',$modulecontext)){
	echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view', MOD_ICECREAM_LANG));
}else{
	echo $renderer->notabsheader();
}

echo $renderer->show_intro($moduleinstance,$cm);

echo $renderer->show_success($success);
$mform = new mod_icecream_cloneform(null,array($allactivities));
$data=new stdClass();
$data->id=$cm->id;
$data->course=$course->id;
$mform->set_data($data);
echo $renderer->show_form_title('activity');
$mform->display();

$mform = new mod_icecream_sectioncloneform(null,array($allsections));
$data=new stdClass();
$data->id=$cm->id;
$data->course=$course->id;
$mform->set_data($data);
echo $renderer->show_form_title('section');
$mform->display();
// Finish the page
echo $renderer->footer();
