<link rel="canonical" href="$CanonicalLink" />

<meta property="og:locale" content="$Locale"/>
<meta name="Copyright" content="{$Date.Now} {$SiteConfig.Title}" />

<meta property="og:type" content="article"/>
<meta property="og:title" content="<% if $MetaTitle %>$MetaTitle.XML<% else %>$Title<% end_if %>"/>
<% if $MetaDescription %><meta property="og:description" content="$MetaDescription.XML"/><% end_if %>
<meta property="og:url" content="$AbsoluteLink"/>
<meta property="og:site_name" content="$SiteConfig.Title - $SiteConfig.Tagline"/>
<% if $SocialMediaShareImage %><meta property="og:image" content="$SocialMediaShareImage.croppedImage(1200,630).AbsoluteLink" /><% end_if %>

<meta name="twitter:title" content="<% if $MetaTitle %>$MetaTitle.XML<% else %>$Title<% end_if %>" />
<% if $MetaDescription %><meta name="twitter:description" content="$MetaDescription.XML" /><% end_if %>
<% if $SocialMediaShareImage %>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="$SocialMediaShareImage.croppedImage(1200,675).AbsoluteLink">
<% end_if %>
<meta name="twitter:site" content="$TwitterSite" />
<meta name="twitter:creator" content="$TwitterCreator" />

<meta property="article:published_time" content="$Created.Rfc3339" />
<meta property="article:modified_time" content="$LastEdited.Rfc3339" />