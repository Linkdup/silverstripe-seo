<?php
/**
 * Seo site tree extension
 * 
 * SeoSiteTreeExtension extends SiteTree with functionality for helping content authors to 
 * write good content for search engines, it uses the added var SEOPageSubject around
 * which the SEO score for the page is determined.
 * 
 * @package seo
 */
class SeoSiteTreeExtension extends SiteTreeExtension {

	/**
	 * Specify page types that will not include the SEO tab
	 *
	 * @config
	 * @var array
	 */
	private static $excluded_page_types = [];

    /**
     * Database fields
     *
     * @config
	 * @var array
     **/
    private static $db = [
        'MetaTitle' => 'Varchar(255)',
        'SEOPageSubject' => 'Varchar(255)',
        'SEOPageScore' => 'Int'
    ];	
	
    /**
     * Database has one relationships
     *
     * @config
	 * @var array 
     **/
    private static $has_one = [
        'SEOImage' => 'Image'
    ];

	/**
	 * Update the CMS with SEO fields
	 * 
	 * @param FieldList $fields
	 */
    public function updateCMSFields(FieldList $fields) 
    {
        // Exclude SEO tab from some page types
        if (in_array($this->owner->getClassName(), Config::inst()->get("SeoSiteTreeExtension", "excluded_page_types"))) {
            return false;
        }	
		
		// Get the SEO validator
		$pageSeoValidation = $this->getSeoValidator();
		
		// Remove existing meta field
		$fields->removeByName("Metadata");
		
		// Create SEO tabs
        $fields->addFieldToTab("Root.SEO", new TabSet('Options'));
        $fields->findOrMakeTab('Root.SEO.Options.Tips', _t('SEO.Tips', 'Tips'));
        $fields->findOrMakeTab('Root.SEO.Options.Meta', _t('SEO.Meta', 'Meta'));
        $fields->findOrMakeTab('Root.SEO.Options.Preview', _t('SEO.Preview', 'Preview'));
		
		// Add tips field
		$fields->addFieldsToTab('Root.SEO.Options.Tips', array(
			HeaderField::create("SeoScore", _t('SEO.SeoScore', 'SEO Score') . "  " . $pageSeoValidation->getScore() . "/100"),
			GoogleSuggestField::create("SEOPageSubject", _t('SEO.SEOPageSubject', 'Page Subject'))
				->setDescription("This is used to calculate the SEO score."),
			SeoTipsField::create("SeoTips", $pageSeoValidation->getTips())
		));
		
		// Add SEO meta fields
		$fields->addFieldsToTab('Root.SEO.Options.Meta', array(
			TextField::create("MetaTitle", _t('SEO.SeoScore', 'Meta Title')),
			TextareaField::create("MetaDescription",  _t('SEO.SeoScore', 'Meta Description'))
		));
		
		// Add preview fields
		$fields->addFieldsToTab('Root.SEO.Options.Preview', array(
			SeoPreviewField::create("PreviewField",
				(empty($this->owner->MetaTitle)) ? $this->owner->Title : $this->owner->MetaTitle,
				$this->owner->MetaDescription,
				$this->owner->AbsoluteLink()
			)
		));
		
	}	
	
	/**
	 * Get first paragraph from content
	 * 
	 * @param DOMDocument $dom
	 * @return string
	 */
	protected function getFirstParagrahFromContent($dom) {
		$firstParagraphObject = $dom->getElementsByTagName('p')->item(0);
		if($firstParagraphObject) {
			return $firstParagraphObject->nodeValue;
		}
		return "";
	}
	
	/**
	 * Get the SEO validator for this page
	 * 
	 * @return SeoValidator
	 */
	public function getSeoValidator() {
		$seoValidator = SeoValidator::create();

		// Create DOM object for more complicated rules that needs to evaluate
		// objects in the DOM.
		$dom = new DOMDocument();
		@$dom->loadHTML($this->owner->Content);
		
		// Get the first paragraph
		$firstParagraph = $this->getFirstParagrahFromContent($dom);

		// Add rules	
		$seoValidator->addRule(SeoValidatorRuleTextLength::create(
			"Page subject is not defined for page.",
			$this->owner->SEOPageSubject, 
			SeoValidatorRuleTextLength::OPERATOR_GREAT_THAN, 
			0
		));		
		$seoValidator->addRule(SeoValidatorRuleContainsAllWords::create(
			"Page subject is not in the title of this page",
			$this->owner->SEOPageSubject, 
			$this->owner->Title
		));
		$seoValidator->addRule(SeoValidatorRuleContainsAllWords::create(
			"Page subject is not present in the first paragraph of the content of this page",
			$this->owner->SEOPageSubject, 
			$firstParagraph
		));
		$seoValidator->addRule(SeoValidatorRuleContainsAllWords::create(
			"Page subject is not present in the URL of this page",
			$this->owner->SEOPageSubject, 
			$this->owner->Link()
		));		
		$seoValidator->addRule(SeoValidatorRuleContainsAllWords::create(
			"Page subject is not present in the meta title of the page",
			$this->owner->SEOPageSubject, 
			$this->owner->MetaTitle
		));	
		$seoValidator->addRule(SeoValidatorRuleContainsAllWords::create(
			"Page subject is not present in the meta description of the page",
			$this->owner->SEOPageSubject, 
			$this->owner->MetaDescription
		));
		$seoValidator->addRule(SeoValidatorRuleTextLength::create(
			"The title of the page is not long enough and should have a length of at least 40 characters.",
			$this->owner->Title, 
			SeoValidatorRuleTextLength::OPERATOR_GREAT_THAN_OR_EQUAL, 
			40
		));
		$seoValidator->addRule(SeoValidatorRuleTextLength::create(
			"The content of this page is too short and does not have enough words. Please create content of at least 300 words based on the Page subject.",
			$this->owner->Content, 
			SeoValidatorRuleTextLength::OPERATOR_GREAT_THAN_OR_EQUAL, 
			300
		));
		
		$seoValidator->addRule(SeoValidatorRuleAltTitleInImages::create(
			$dom
		));

		// Valiate the rules
		$seoValidator->validate();
		
		return $seoValidator;
	}
	
}
