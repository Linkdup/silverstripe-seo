<?php

/* 
 * Seo validator used to validate pages SEO scores in SilverStripe
 * 
 * @package seo
 */

class SeoValidator extends SS_Object {
	
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
	 * Page to validate
	 * 
	 * @var SiteTree 
	 */
	protected $page = null;
	
	/**
	 * Set the page DOM
	 * 
	 * @var DOMDocument 
	 */
	protected $dom = null;

	/**
	 * Initialise the class
	 * 
	 * @param SiteTree $page - page used to validate SEO
	 * @param DOMDocument $dom - contains the dom of the SEO content
	 */
	public function __construct($page, $dom) {
		parent::__construct();
		$this->rules = new ArrayList();
		$this->tips = new ArrayList();
		$this->page = $page;
		$this->dom = $dom;
	}
	
	/**
	 * Add the rule
	 * 
	 * @param SeoValidatorRule $rule
	 */
	public function addRule(SeoValidatorRule $rule) {
		$rule->setValidator($this);
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
	 * Get the page
	 * 
	 * @return SiteTree
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * Get the dom
	 * 
	 * @return DOMDocument
	 */
	public function getDom() {
		return $this->dom;
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
		$rules = $this->getRules()->count();
		if($rules > 0) {
			$x = 100/$rules;
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

}
