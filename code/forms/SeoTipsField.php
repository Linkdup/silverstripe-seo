<?php

/**
 * This field lets you preview how the page will be previewed in search results.
 *
 * <code>
 * new SeoTipsField (
 *    $name = "seotipsfield",
 *	  $tips = "ArrayList"
 * )
 * </code>
 * 
 * @package seo
 */
class SeoTipsField extends LiteralField {
	
	/**
	 * List of tips to display in the SEO tips field
	 * 
	 * @var ArrayList
	 */
	private $tips;
	
	/**
	 * Create the SEO preview field
	 * 
	 * @param string $name
	 * @param Tips $validator
	 */
	public function __construct($name, $tips) 
	{
		$this->tips = $tips;
		parent::__construct($name, $this->getContent());
	}
	
	/**
	 * Get content
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return Controller::curr()->customise(array(
			"Tips" => $this->tips->filter("Valid", false),
		))->renderWith('SeoTipsField');
	}
		
}


