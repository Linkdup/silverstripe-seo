<?php

/* 
 * A model to manage and report on SEO ranks of pages
 * 
 * @package seo
 */

class SeoModelAdmin extends ModelAdmin {
	
    /**
     * Hide the import form for SEO admin
     *
	 * @config
	 * @var boolean
     **/
    public $showImportForm = false;

    /**
     * The main menu title
     *
	 * @config
	 * @var string
     **/
    private static $menu_title = 'SEO';

    /**
     * The CMS SEO admin URL segment
     *
	 * @config
	 * @var string
     **/
    private static $url_segment = 'seo-admin';

    /**
     * The main menu icon
     *
	 * @config
	 * @var string
     **/
    private static $menu_icon = 'seo/images/seo.png';

    /**
     * Default none as they are set later
     *
	 * @config
	 * @var array|string
     **/
    private static $managed_models = array();

    /**
     * Disable model imports in SEO admin
     *
	 * @config
	 * @var string
     **/
    private static $model_importers = null; 
	
    /**
     * Update the managed models array with objects listed in the YML config files
     *
     * @return void
     **/
    public function init()
    {
        $models = Config::inst()->get($this->class, 'models');
        Config::inst()->update($this->class, 'managed_models', $models);
        parent::init();
    }
	
    /**
     * Get the CMS edit for for the models
     * 
     * @param mixed $id
     * @param mixed $fields
     *
     * @return FieldList
     **/
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        $class = new $this->modelClass;
		
		// Update the grid
        $grid = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
		$config = $grid->getConfig();
        $config->removeComponentsByType('GridFieldAddNewButton');
		$config->removeComponentsByType('GridFieldDeleteAction');
		$config->removeComponentsByType('GridFieldPrintButton');
		$config->removeComponentsByType('GridFieldExportButton');
		$config->removeComponentsByType('GridFieldEditButton');
		
		// Specify the fields to display
		$dataColumns = $config->getComponentByType('GridFieldDataColumns');
		$dataColumns->setDisplayFields(array(
			'Title' => 'Title',
			'URLSegment'=> 'URL',
			'SEOPageSubject' => 'SEO Page Subject',
			'SEOPageScore' => 'SEO Score'
		));
		
		// Add link to title
		$dataColumns->setFieldFormatting(array(
			"Title" => function($value, $item) {
				$url = "/admin/pages/edit/show/" . $item->ID;
				$link = '<a href="' . $url . '" class="cms-panel-link action external-link">' . $item->Title . '</a>';
				return $link;
			}
		));

        $this->extend('updateEditForm',  $grid);
        
        return $form;
    }
}