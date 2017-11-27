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
	 * Page SEO Validator
	 * 
	 * @var SeoValidator 
	 */
	protected $pageSeoValidator = null;

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
     * Database default fields
     *
     * @config
	 * @var array
     **/
    private static $defaults = [
        'SEOPageScore' => 0
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
	 * Save the SEO page score on write
	 */
	public function onBeforeWrite() 
	{
		parent::onBeforeWrite();
		$pageSeoValidation = $this->getSeoValidator();
		if($pageSeoValidation) {
			$this->owner->SEOPageScore = $pageSeoValidation->getScore();
		}
	}

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
	 * Update the Meta Tag with Seo Meta Tags
	 * 
	 * @param HTMLText $tags
	 */
    public function MetaTags(& $tags)
    {
        $tags .= $this->owner->renderWith('SeoMeta');
    }
	
	/**
	 * Create DOM from HTML
	 *
	 * @param HTMLText $html String
	 * @return DOMDocument Object
	 */
	private function createDOMDocumentFromHTML($html) 
	{
		if (empty($html)) {
			return null;
		} else {
			$dom = new DOMDocument();
			@$dom->loadHTML($html);
			return $dom;
		}
	}
	
	/**
	 * Get the SEO validator for this page
	 * 
	 * @return SeoValidator
	 */
	public function getSeoValidator() 
	{
		if($this->pageSeoValidator === null) {
			// Set the default content
			$html = $this->getSeoHTML();

			// Create the validator
			$this->pageSeoValidator = SeoValidator::create(
				$this->owner, 
				$this->createDOMDocumentFromHTML($html )
			);

			// Get the rules for the page
			$rules = $this->owner->config()->get("seo_rules");	
			foreach($rules as $rule) {
				$this->pageSeoValidator->addRule($rule::create());
			}

			// Valiate the rules
			$this->pageSeoValidator->validate();
		}

		return $this->pageSeoValidator;
	}
	
	/*
	*  Get Page Content from theme
	* 
	*  getPageContent
	*  function to get html content of page which SEO score is based on
	*  (we use the same info as gets back from $Layout in template)
	*
	*/
	public function getSeoHTML() 
	{
		Config::inst()->update('SSViewer', 'theme_enabled', true);
		$rendered_layout = $this->renderLayout();
		Config::inst()->update('SSViewer', 'theme_enabled', false);
		return $rendered_layout;
	}
	
	/**
	 *  Mimics the behaviour of $Layout in templates
	 * 
	 * @return HTMLText
	 */
	public function renderLayout() 
	{
		$template = $this->getLayoutTemplate();
		$subtemplateViewer = new SSViewer($template);
		$subtemplateViewer->includeRequirements(false);
		return $subtemplateViewer->process($this->getOwner());
	}
	
	/**
	 * Find the appropriate "$Layout" template for this class
	 * 
	 * @throws Exception
	 * @return string
	 */
	protected function getLayoutTemplate() 
	{
		$theme = Config::inst()->get('SSViewer', 'theme');
		$templateList = array();
		$parentClass = $this->getOwner()->class;
		while($parentClass !== 'SiteTree') {
			$templateList[] = $parentClass;
			$parentClass = get_parent_class($parentClass);
		}
		$templates = SS_TemplateLoader::instance()->findTemplates($templateList, $theme);
		if( ! isset($templates['Layout'])) {
			throw new Exception('No layout found for class: ' . get_class($this->getOwner()));
		}
		return $templates['Layout'];
	}
	
}
