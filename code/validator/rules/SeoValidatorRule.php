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
class SeoValidatorRuleBase extends Object implements SeoValidatorRule {
	
	/**
	 * Tip
	 * 
	 * @var string 
	 */
	protected $tip = "";
	
	/**
	 * Reference to SEO validator
	 * 
	 * @var SeoValidator 
	 */
	protected $validator = null;
	
	/**
	 * Set the validator
	 * 
	 * @param SeoValidator $validator
	 */
	public function setValidator($validator) {
		$this->validator = $validator;
	}
	
	/**
	 * Get the validator
	 * 
	 * @param SeoValidator $validator
	 */
	public function getValidator() {
		return $this->validator;
	}	

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
	public function setTip($tip) {
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
	
	/**
	 * Helper method to check if all words are contained in subject
	 * 
	 * @param string $subject
	 * @param string $text
	 * @return boolean
	 */
	public function containsAllWords($subject, $text) {
		if(!empty($subject) && !empty($text)) {
			$subject = preg_replace('/[.,]/', '', trim($subject));
			$words = explode(" ", strtolower(trim($subject)));
			$text = strtolower($text);
			foreach($words as $word) {
				if (!(strpos($text, trim($word)) !== false)) {
					return false;
				}
			}
			return true;
		}
		return false;	
	}

	
}

class SeoValidatorRule_PageSubjectInFirstParagraph extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the first paragraph of the content of this page";	
	
	/**
	 * Get first paragraph from content
	 * 
	 * @return string
	 */
	protected function getFirstParagrahFromContent() {
		$dom = $this->validator->getDom();
		$firstParagraphObject = $dom->getElementsByTagName('p')->item(0);
		if($firstParagraphObject) {
			return $firstParagraphObject->nodeValue;
		}
		return "";
	}
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		$firstParagraph = $this->getFirstParagrahFromContent();	
		return $this->containsAllWords($page->SEOPageSubject, $firstParagraph);
	}
}

class SeoValidatorRule_PageSubjectInContent extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the the content of this page";	
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		$content = "";
		$dom = $this->validator->getDom();
		$tags = $dom->getElementsByTagName("p");
		if($tags && $tags->length > 0) {
			foreach($tags as $tag) {
				$content .= $tag->textContent;
			}
		}	
		return $this->containsAllWords($page->SEOPageSubject, $content);
	}
}

class SeoValidatorRule_PageSubjectInUrl extends SeoValidatorRuleBase {
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the URL of this page";	
		
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		return $this->containsAllWords($page->SEOPageSubject, $page->URLSegment);
	}
}

class SeoValidatorRule_PageSubjectInTitle extends SeoValidatorRuleBase {
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the  title of the page";	
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		return $this->containsAllWords($page->SEOPageSubject, $page->Title);
	}
}

class SeoValidatorRule_PageSubjectInMetaTitle extends SeoValidatorRuleBase {
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the meta title of the page";	
		
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		return $this->containsAllWords($page->SEOPageSubject, $page->MetaTitle);
	}
}

class SeoValidatorRule_PageSubjectInMetaDescription extends SeoValidatorRuleBase {
	/**
	 * @var string 
	 */
	protected $tip = "Page subject is not present in the meta description of the page";	
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		return $this->containsAllWords($page->SEOPageSubject, $page->MetaDescription);
	}
}

class SeoValidatorRule_PageTitleLength extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "The title of the page is not long enough and should have a length of at least 40 characters.";	
	
	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		return (strlen( $page->Title) >= 40) ? true : false;
	}
}

class SeoValidatorRule_PageContentLength extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "The content of this page is too short and does not have enough words. Please create content of at least 300 words based on the Page subject.";	

	/**
	 * @return boolean
	 */
	public function valid() {
		$content = "";
		$dom = $this->validator->getDom();
		$tags = $dom->getElementsByTagName("p");
		if($tags && $tags->length > 0) {
			foreach($tags as $tag) {
				$content .= $tag->textContent;
			}
		}
		$words = explode(" ", $content);
		return (count($words) >= 300) ? true : false;
	}
}

class SeoValidatorRule_PageContentHasSubtitles extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "The content of this page does not have sub titles. Please add heading 2 elements";	

	/**
	 * @return boolean
	 */
	public function valid() {
		$dom = $this->validator->getDom();
		$tags = $dom->getElementsByTagName("h2");
		return ($tags->length) ? true : false;
	}
}

class SeoValidatorRule_PageContentHasLinks extends SeoValidatorRuleBase {
	
	/**
	 * @var string 
	 */
	protected $tip = "The content of this page does not have any links.";	

	/**
	 * @return boolean
	 */
	public function valid() {
		$dom = $this->validator->getDom();
		$tags = $dom->getElementsByTagName("a");
		return ($tags->length) ? true : false;
	}
}

/**
 * Checks if a alt or title exists in images
 */
class SeoValidatorRule_AltTitleInImages extends SeoValidatorRuleBase {

	/**
	 * @return boolean
	 */
	public function valid() {
		$page = $this->validator->getPage();
		$dom = $this->validator->getDom();

		$tags = $dom->getElementsByTagName("img");
		if($tags && $tags->length == 0) {
			$this->setTip("The content of this page does not have any images.");
			return false;
		}

		$images = array();
		if($tags) {
			foreach($tags as $tag) {
				$src = $tag->getAttribute('src'); 
				$alt = $tag->getAttribute('alt'); 
				$title = $tag->getAttribute('title'); 
				if(empty($alt) && empty($title)) {
					$images[] = $src;
				}
			}
		}
		if(count($images)) {
			$this->setTip("Add title or alt tag to the following images " . implode(",", $images));
			return false;
		} else {
			return true;
		}
	}
	
}
