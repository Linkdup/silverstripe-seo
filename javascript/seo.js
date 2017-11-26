(function($) {

	var $editFormID = "Form_EditForm",
		$altFormID = "Form_ItemEditForm",
		scoreTimeoutID = -1;

	$.entwine('ss', function($){

		/**
		 * Register CMS field events
		 */
		$('input[name="SEOPageSubject"]').entwine({
			onmatch: function(){
				setEditFormID();
			},
			onkeyup : function() {
				delaySeoScoreCall();
			},
			onchange : function() {
				setEditFormID();
				delaySeoScoreCall();
			}
		});
		
		/**
		 * Handle preview tab click events
		 */
		$(document.body).on("click", '#tab-Root_SEO, #tab-Options_Preview', function(e){
			updatePreviewView();
		});	
		
		/**
		 * Handle tips tab click events
		 */
		$(document.body).on("click", "#tab-Root_SEO, #tab-Options_Tips", function(e){
			getSeoScore();
		});

		/**
		 * Set the edit form
		 * 
		 * @returns {undefined}
		 */
		function setEditFormID() {
			if (!$("#" + $editFormID ).length) {
				$editFormID = $altFormID;
			}
		}
		
		/**
		 * Get the score for the page SEO from server
		 * 
		 * @returns {undefined}
		 */
		function getSeoScore() {
			clearTimeout(scoreTimeoutID);
			var $scoreTipContainer = $( "#seo_score_tips_container" ).fadeOut();
			$.ajax({
				url: "/admin/seo/score",
				// dataType: "jsonp",
				type: "POST",
				data: {
                    id: $("#Form_EditForm_ID").val(),
                    content: getSeoContentValues()
                 },
				success: function( data ) {
					$scoreTipContainer.replaceWith(data).fadeIn();
					$scoreTipContainer = $( "#seo_score_tips_container" );
					updateScore($scoreTipContainer.data("score"));
				}
			});
		}
		
		/**
		 * Delay the score call
		 * 
		 * @returns {undefined}
		 */
		function delaySeoScoreCall() {
			clearTimeout(scoreTimeoutID);
			scoreTimeoutID = setTimeout(getSeoScore, 500);
		}
		
		/**
		 * Update the score
		 * 
		 * @returns {undefined}
		 */
		function updateScore(score) {
			var $scoreField = $('#Form_EditForm_SeoScore');
			var scoreLabel = "SEO Score  " + score + "/100";
			$scoreField.text(scoreLabel);
		}			
		
		/**
		 * Update preview tab
		 * 
		 * @returns {void}
		 */
		function updatePreviewView() {
			var $seoDescriptionField = $("#seo_preview").find(".seo-description"),
				content = getSeoContentValues(),
				title = (content.MetaTitle.length > 0) ? content.MetaTitle : content.Title,
				description = (content.MetaDescription.length > 0) ? content.MetaDescription : $seoDescriptionField.data("default-description");
			console.log("test d:" + description);
			$("#seo_preview").find(".seo-title").text(title);
			$("#seo_preview").find(".seo-link").text(content.Link);
			$seoDescriptionField.text(description);
		}

		/**
		 * Get text field value
		 * 
		 * @param {string} fieldName
		 * @param {string} inputType (input or textarea)
		 * @returns {string}
		 */
		function getTextFieldValue(fieldName, inputType) {
			inputType = (inputType) ? inputType : "input"; 
			var $field = $(inputType + '[name="' + fieldName + '"]');
			if($field.length) {
				return $.trim($field.val());
			}
			return "";
		}
		
		/**
		 * Get the page URL
		 * 
		 * @returns {String}
		 */
		function getPageURL() {
			var $urlSegmentNode = $('input[name="URLSegment"]');
			if($urlSegmentNode) {
				return $urlSegmentNode.attr('data-prefix').toLowerCase()+ $urlSegmentNode.val().toLowerCase();
			}
			
		}		
		
		/**
		 * Get editor content
		 * 
		 * @returns {String}
		 */
		function getEditorContent() {
			if(tinyMCE.getInstanceById($editFormID + "_Content") != undefined) {
				return tinyMCE.getInstanceById($editFormID + "_Content").getContent();
			} else {
				return getTextFieldValue("Content", "textarea");
			}
		}
		
		/**
		 * Get SEO content values
		 * 
		 * @returns {Object}
		 */
		function getSeoContentValues() {
			return {
				SEOPageSubject: getTextFieldValue("SEOPageSubject"),
				Title: getTextFieldValue("Title"),
				Content: getEditorContent(),
				URLSegment: getTextFieldValue("URLSegment"),
				Link: getPageURL(),
				MetaTitle: getTextFieldValue("MetaTitle"),
				MetaDescription: getTextFieldValue("MetaDescription","textarea")
			};
		}

	});
	
})(jQuery);