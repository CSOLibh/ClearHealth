<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Address');
class PracticeAddress extends Address {
	var $_relation = "practice_address";
	var $_fkey = "practice_id";

	function setup($id = 0,$parent = false, $type = "practice") {
		//var_dump("new PersonAddress",get_class($this));
		parent::setup($id,$parent,$type);
	}
}
