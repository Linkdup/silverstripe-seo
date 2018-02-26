<link rel="canonical" href="$CanonicalLink" />

<meta property="og:locale" content="$Locale"/>
<meta name="Copyright" content="{$Date.Now} {$SiteConfig.Title.XML}" />

<meta property="og:type" content="article"/>
<meta property="og:title" content="$SeoTitle"/>
<meta property="og:description" content="$SeoDescription.XML"/>
<meta property="og:url" content="$SeoLink"/>
<meta property="og:site_name" content="$SeoSiteName"/>
<% if $SeoImage %><meta property="og:image" content="$SeoImage.croppedImage(1200,630).AbsoluteLink" /><% end_if %>

<meta name="twitter:title" content="$SeoTitle" />
\<meta name="twitter:description" content="$SeoDescription.XML" />\
<% if $SeoImage %>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="$SeoImage.croppedImage(1200,675).AbsoluteLink">
<% end_if %>
<% if $SeoTwitterSite %><meta name="twitter:site" content="$SeoTwitterSite" /><% end_if %>
<% if $SeoTwitterCreator %><meta name="twitter:creator" content="$SeoTwitterCreator" /><% end_if %>

<meta property="article:published_time" content="$Created.Rfc3339" />
<meta property="article:modified_time" content="$LastEdited.Rfc3339" />