<div id="seo_score_tips_container" data-score="$Score" <% if $Tips.Count == 0 %>class="hide"<% end_if %>>
	<h4>SEO Tips</h4>
	<ul id="seo_score_tips" class="seo-tips-list">
		<% if $Tips %>
		<% loop $Tips %>
		<li>$Tip</li>
		<% end_loop %>
		<% end_if %>
	</ul>
</div>