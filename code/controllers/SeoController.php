<?php

class SeoController extends Controller {
	
	/**
	 * Allowed action
	 * 
	 * @var array
	 */
	private static $allowed_actions = array(
		'score'
	);
	
	/**
	 * Score the page content
	 * 
	 * @param SS_HTTPRequest $request
	 */
	public function score(SS_HTTPRequest $request) {
		// Check that we have a valid AJAX request
		if(!$request->isAjax()) {		
			return $this->httpError(404);
		}
		
		// Get the page ID
		$pageID = (int)$this->request->postVar("id");
		
		// Get the content array
		$content = $this->request->postVar("content");
		
		if($pageID > 0) {
			$page = Page::get()->byID($pageID);

			// Update the fields required for SEO validator, we need to inject
			// the content from the frontend.
			$page->Title = $content["Title"];
			$page->SEOPageSubject = $content["SEOPageSubject"];
			$page->MetaTitle = $content["MetaTitle"];
			$page->MetaDescription = $content["MetaDescription"];
			$page->URLSegment = $content["URLSegment"];
			$page->Content = $content["Content"];
			
			// Get the SEO validator
			$pageSeoValidation = $page->getSeoValidator();

			// Render the output
			return Controller::curr()->customise(array(
				"Score" => $pageSeoValidation->getScore(),
				"Tips" => $pageSeoValidation->getTips()->filter("Valid", false),
			))->renderWith('SeoTipsField');
		}
		
		return "";
	}
}