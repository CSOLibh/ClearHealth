<?php

$loader->requireOnce('/includes/Datasource_sql.class.php');

class Person_RelatedList_DS extends Datasource_sql
{
	/**
	 * A cache of the relation_type enum
	 *
	 * @var array
	 * @access private
	 * @see _loadRelationTypeList()
	 */
	var $_relationshipTypes = null;
	
	
	/**
	 * Handle instantiation
	 *
	 * @param int
	 */
	function Person_RelatedList_DS($person_id) {
		settype($person_id,'int');

		$labels = array(
			'left_name'     => 'Person',
			'relation_type' => 'Relation Of',
			'right_name'    => 'Relation',
			'guarantor'	=> 'Guarantor?'
		);
		$this->setup(Celini::dbInstance(),
			array(
				'union' => 
				array(
					array(
					'cols' 	=> "
						t.person_person_id, 
						CONCAT_WS(' ',p.first_name, p.last_name) left_name,
						relation_type,
						CONCAT_WS(' ',r.first_name, r.last_name) right_name,
						r.person_id right_id,
						if(guarantor=1,concat('Yes (R of P) #',guarantor_priority+1),'No') guarantor",
					'from' 	=> "
						person_person AS t
						INNER JOIN person AS p ON (p.person_id = t.person_id)
						INNER JOIN person AS r ON (r.person_id = t.related_person_id)",
					'where'	=> "t.person_id = $person_id",
					),
					array(
					'cols' 	=> "
						t.person_person_id, 
						CONCAT_WS(' ',r.first_name, r.last_name) right_name,
						relation_type,
						CONCAT_WS(' ',p.first_name, p.last_name) left_name,
						p.person_id right_id,
						if(guarantor=1,concat('Yes (P of R) #',guarantor_priority+1),'No') guarantor",
					'from' 	=> "
						person_person AS t
						INNER JOIN person AS r ON (p.person_id = t.person_id) 
						INNER JOIN person AS p ON (r.person_id = t.related_person_id)",
					'where'	=> "t.related_person_id = $person_id",
					)
				)
			),
			$labels);

		$this->registerFilter('relation_type',array(&$this,'_humanReadableRelationshipType'));
		$this->registerTemplate('right_name','<a class="dashedLink" title="View dashboard for {$right_name}" href="'.
			Celini::link('view','PatientDashboard').'id={$right_id}">{$right_name}</a>');

	}
	
	
	/**
	 * Changes the relation_type enum into a human readable field.
	 *
	 * @return string
	 * @access protected
	 */
	function _humanReadableRelationshipType($type) {
		$this->_loadRelationshipTypeList();
		return isset($this->_relationshipTypes[$type]) ?
			$this->_relationshipTypes[$type] :
			$type;
	}
	
	/**
	 * Loads the relation_type enum into memory
	 *
	 * @access protected
	 */
	function _loadRelationshipTypeList() {
		if (!is_null($this->_relationshipTypes)) {
			return;
		}
		
		$enum = ORDataObject::factory('Enumeration');
		$this->_relationshipTypes = $enum->get_enum_list('person_to_person_relation_type');
	}
}

