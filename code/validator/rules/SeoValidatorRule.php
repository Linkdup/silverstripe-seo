<?php

/**
 * Base interface for all rules that can be added to SeoValidator.
 *
 * @package seo
 */
interface SeoValidatorRule {
	public function getTip();
	public function valid();
}

/**
 * Default structure of SEO validator rule
 */
class SeoValidatorBase extends Object implements SeoValidatorRule {
	
	/**
	 * Initalise SEO validator
	 * 
	 * @param string $tip
	 */
	public function __construct($tip) {
		$this->tip = $tip;
	}
	
	/**
	 * Message
	 * 
	 * @var string 
	 */
	protected $tip = "";

	/**
	 * Get the tip
	 */
	public function getTip() {
		return $this->tip;
	}
	
	/**
	 * Set the tip
	 * 
	 * @param string $tip
	 */
	public function setMessage($tip) {
		$this->tip = $tip;
	}
	
	/**
	 * Validate the rule
	 * 
	 * @return boolean
	 */
	public function valid() {
		user_error("Needs to be implemented on rule", E_USER_ERROR);
	}	
}

/**
 * Checks that the length of the text is within the excepted range.
 */
class SeoValidatorRuleTextLength extends SeoValidatorBase {
	
	/**
	 * Operator constants
	 */
	const OPERATOR_GREAT_THAN = "OPERATOR_GREAT_THAN";
	const OPERATOR_GREAT_THAN_OR_EQUAL = "OPERATOR_GREAT_THAN_OR_EQUAL";
	const OPERATOR_EQUAL = "OPERATOR_EQUAL";
	const OPERATOR_LESS_THAN = "OPERATOR_LESS_THAN";
	const OPERATOR_LESS_THAN_OR_EQUAL = "OPERATOR_LESS_THAN_OR_EQUAL";
	
	/**
	 * Operator for validation
	 * 
	 * @var string 
	 */
	protected $operator;
	
	/**
	 * Length of text
	 * 
	 * @var int
	 */
	protected $textLength;
	
	/**
	 * Value to compare
	 * 
	 * @var int 
	 */
	protected $value;
	
	/**
	 * @param string $tip
	 * @param string $text
	 * @param string $operator
	 * @param int $value
	 */
	public function __construct($tip, $text, $operator, $value) {
		$this->operator = $operator;
		$this->textLength = strlen($text);
		$this->value = $value;
		parent::__construct($tip);
	}
	
	/**
	 * Validate the rule
	 * 
	 * @return boolean
	 */	
	public function valid() {
				
		switch($this->operator) {
			case self::OPERATOR_GREAT_THAN:
				if($this->textLength > $this->value) {
					return true;
				} else {
					return false;
				}
				break;
			case self::OPERATOR_GREAT_THAN_OR_EQUAL:
				if($this->textLength >= $this->value) {
					return true;
				} else {
					return false;
				}
				break;
			case self::OPERATOR_EQUAL:
				if($this->textLength == $this->value) {
					return true;
				} else {
					return false;
				}
				break;
			case self::OPERATOR_LESS_THAN:
				if($this->textLength < $this->value) {
					return true;
				} else {
					return false;
				}
				break;		
			case self::OPERATOR_LESS_THAN_OR_EQUAL:
				if($this->textLength <= $this->value) {
					return true;
				} else {
					return false;
				}
				break;				
			default:
				user_error("Please specify an operator", E_USER_WARNING);
		}
		return false;
	}	
}

/**
 * Checks if the text contains all the words in the subject
 */
class SeoValidatorRuleContainsAllWords extends SeoValidatorBase {
	
	protected $subject = "";
	protected $text = "";

	/**
	 * @param type $subject
	 * @param type $text
	 */
	public function __construct($tip, $subject, $text) {
		$this->subject = $subject;
		$this->text = $text;
		parent::__construct($tip);
	}
	
	/**
	 * Validate the rule
	 * 
	 * @return boolean
	 */
	public function valid() {
		if($this->subject) {
			$words = explode(" ", strtolower($this->subject));
			$text = strtolower($this->text);
			foreach($words as $word) {
				if (!(strpos($text, $word) !== false)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
	
}

/**
 * Checks if a alt or title exists in images
 */
class SeoValidatorRuleAltTitleInImages extends SeoValidatorBase {

	/**
	 * @param DOMDocument $dom
	 */
	public function __construct($dom) {
		$this->dom = $dom;
		parent::__construct("");
	}
	
	/**
	 * Validate the rule
	 * 
	 * @return boolean
	 */
	public function valid() {
		$tags = $this->dom->getElementsByTagName("img");
		if($tags && $tags->length == 0) {
			$this->setMessage("The content of this page does not have any images.");
			return false;
		}

		$images = array();
		if($tags) {
			foreach($tags as $tag) {
				$src = $tag->getAttribute('alt'); 
				$alt = $tag->getAttribute('alt'); 
				$title = $tag->getAttribute('title'); 
				if(empty($alt) && empty($title)) {
					$images[] = $src;
				}
			}
		}
		if(count($images)) {
			$tip = "Add title or alt tag to the following images " . implode(",", $images);
			return false;
		} else {
			return true;
		}
	}
	
}
