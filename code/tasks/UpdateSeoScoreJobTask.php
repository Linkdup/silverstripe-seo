<?php
/**
 * Creates a task in /dev/tasks which can be called manually to 
 * create Queued jobs for the website
 * 
 * @package seo
 */
class UpdateSeoScoreJobTask extends BuildTask {
	/**
	 * @var string $description Describe the implications the task has,
	 * and the changes it makes. Accepts HTML formatting.
	 */
	protected $description = "Update the SEO score for pages based on new rules";
	
	/**
	 * Run the task
	 */
    public function run($request) {	
		$pages = SiteTree::get()->filter(array(
			'ClassName' => 'Page'
		))->where('SEOPageSubject IS NOT NULL');
		$pageCount = $pages->count();
		$pagesUpdated = 0;
		foreach($pages as $page) {
			$pageSeoValidation = $page->getSeoValidator();
			$score = $pageSeoValidation->getScore();
			if($score != $page->SEOPageScore) {
				$page->SEOPageScore = $score;
				$page->write();
				$pagesUpdated++;
			}
		}
		echo "$pagesUpdated of $pageCount have been updated.";
	}
}