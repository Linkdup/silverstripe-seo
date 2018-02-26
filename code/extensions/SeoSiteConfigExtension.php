<?php

/**
 * Seo site config extension
 * 
 * SeoSiteConfigExtension adds site-wide settings for SEO
 * 
 * @package seo
 */
class SeoSiteConfigExtension extends DataExtension
{
    /**
     * Database fields
     *
     * @config
	 * @var array
     **/	
    private static $db = array(
		'TwitterSite' => 'Varchar(255)',
		'TwitterCreator' => 'Varchar(255)'
    );
	
	/**
	 * Database has one relationship
	 *
	 * @return array
	 */
	private static $has_one = array(
		'SocialMediaShareImage' => 'Image'
	);	

    /**
     * Update Silverstripe CMS Fields for SEO Module
     *
     * @param FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {	
		$fields->addFieldsToTab("Root.SEO", array(
			UploadField::create("SocialMediaShareImage","Default share image"),
			TextField::create("TwitterSite","Twitter Site"),
			TextField::create("TwitterCreator","Twitter Creator"),
			UploadField::create(
				"SocialMediaShareImage",
				"Default share image"
			)
		));	
    }
}
