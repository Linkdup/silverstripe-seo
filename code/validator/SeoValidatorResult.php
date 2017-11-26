<?php

/* 
 * SeoValidatorResult provides the result from a SeoValidator test.
 */

class SeoValidatorResult extends ViewableData {
	
	/**
	 * @var array
	 * @config
	 */
	private static $casting = array(
		'Valid' => 'Boolean',
		'Tip' => 'Varchar'
	);

	/**
	 * @param boolean $valid
	 * @param string $tip
	 */
	public function __construct($valid, $tip) {
		parent::__construct();
		$this->Valid = $valid;
		$this->Tip = $tip;
	}
	
}