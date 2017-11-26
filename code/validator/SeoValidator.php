<?php

/* 
 * Seo validator used to validate pages SEO scores in SilverStripe
 * 
 * @package seo
 */

class SeoValidator extends Object {
	
	/**
	 * Get score
	 * 
	 * @var int 
	 */
	protected $score = 0;
	
	/**
	 * SEO validation rules
	 * 
	 * @var ArrayList
	 */
	protected $rules = null;
	
	/**
	 * Hold important tips
	 * 
	 * @var ArrayList 
	 */
	protected $tips = null;

	/**
	 * Initialise the class
	 */
	public function __construct() {
		parent::__construct();
		$this->rules = new ArrayList();
		$this->tips = new ArrayList();
	}
	
	/**
	 * Add the rule
	 * 
	 * @param SeoValidatorRule $rule
	 */
	public function addRule(SeoValidatorRule $rule) {	
		$this->getRules()->add($rule);
	}
	
	/**
	 * Remove rule
	 * 
	 * @param SeoValidatorRule $rule
	 */
	public function removeRule(SeoValidatorRule $rule) {
		$this->getRules()->remove($rule);
	}	
	
	/**
	 * Get the rules
	 * 
	 * @return ArrayList of rules
	 */
	public function getRules() {
		return $this->rules;
	}
	
	/**
	 * Get Score between 0 and 100
	 * 
	 * You need to validate before getting the score
	 * 
	 * @return int
	 */
	public function getScore() {
		return floor($this->score);
	}
	
	/**
	 * Get tips
	 * 
	 * @return ArrayList of tips
	 */
	public function getTips() {
		return $this->tips;
	}
	
	/**
	 * Validate the SEO rules
	 */
	public function validate() {
		$this->score = 0;
		$x = 100/$this->getRules()->count();
		foreach($this->getRules() as $rule) {
			if($rule->valid()) {
				$this->score += $x;
				$this->tips->add(SeoValidatorResult::create(true, $rule->getTip()));
			} else {
				$this->tips->add(SeoValidatorResult::create(false, $rule->getTip()));
			}
		}
	}

}