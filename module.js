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
 * JavaScript library for the icecream module.
 *
 * @package    mod
 * @subpackage icecream
 * @copyright  2015 Justin Hunt aka PoodLLGuy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_icecream = {
	gY: null,
	gAvailabilitySummary: null,
	gAvailabilityCode: null,


	 /**
     * @param Y the YUI object
     * @param opts an array of options
     */
    init: function(Y,opts) {
    	this.gY = Y;
    	this.gAvailabilitySummary = opts['availabilitysummary'];
    	this.gAvailabilityCode = opts['availabilitycode'];
    },
    doclonefrom: function(){
    	var lb = this.get_listbox('clonefrom');
    	var sum = this.gY.one('#id_clonefromsummary');
    	var cod = this.gY.one('#id_clonefromcode');
    	lb.all('option:checked').each(function(){
    		sum.set('innerHTML',M.mod_icecream.gAvailabilitySummary[this.get('value')]);
    		cod.set('innerHTML',M.mod_icecream.gAvailabilityCode[this.get('value')]);
		});
    
    },
    docloneto: function(){
    	var lb = this.get_listbox('cloneto');
    	var sum = this.gY.one('#id_clonetosummary');
    	var cod = this.gY.one('#id_clonetocode');
    	lb.all('option:checked').each(function(){
    		sum.set('innerHTML',M.mod_icecream.gAvailabilitySummary[this.get('value')]);
    		cod.set('innerHTML',M.mod_icecream.gAvailabilityCode[this.get('value')]);
		});
    },
    get_listbox: function(listboxname){
		return  this.gY.one("select[name='" + listboxname + "']");
	}
};
