<?php
/**
 * Object Relational Persistence Mapping Class for table: insurance_program
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: insurance_program
 *
 * @package	com.uversainc.clearhealth
 */
class InsuranceProgram extends ORDataObject {

	/**#@+
	 * Fields of table: insurance_program mapped to class members
	 */
	var $id			= '';
	var $payer_type		= '';
	var $company_id		= '';
	var $name		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function InsuranceProgram($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'insurance_program';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Insurance_program with this
	 */
	function setup($id = 0,$company_id = 0) {
		if ($company_id > 0) {
			$this->set('company_id',$company_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('insurance_program_id');
	}

	/**#@+
	 * Getters and Setters for Table: insurance_program
	 */

	
	/**
	 * Getter for Primary Key: insurance_program_id
	 */
	function get_insurance_program_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: insurance_program_id
	 */
	function set_insurance_program_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	var $_typeCache = false;

	/**
	 * Cached lookup for payer type
	 */
	function lookupPayerType($type_id) {
		if ($this->_typeCache === false) {
			$this->_typeCache = $this->getPayerTypeList();
		}
		if (isset($this->_typeCache[$type_id])) {
			return $this->_typeCache[$type_id];
		}
	}

	function getPayerTypeList() {
		$list = $this->_load_enum('payer_type',false);
		return array_flip($list);
	}

	function programList() {
		$res = $this->_execute("select insurance_program_id, name from $this->_table");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	function detailedProgramList($company_id) {
		settype($company_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "name, payer_type",
				'from' 	=> "$this->_table",
				'where' => " company_id = $company_id"
			),
			array('name' => 'Program Name','payer_type' => 'Payer Type'));

		$ds->registerFilter('payer_type',array(&$this,'lookupPayerType'));
		return $ds;
	}
}
?>
