<?xml version="1.0" encoding="{encoding}"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel>
		<title>{title}</title>
		<link>{link}</link>
		<description>{description}</description>
<!-- BEGIN item -->
	        <item>
			<title>{subject}</title>
			<link>{item_link}</link>
			<pubDate>{pub_date}</pubDate>
			<description><![CDATA[<p><b>{teaser}</b></p>{content}]]></description>
		</item>
<!-- END item -->
	</channel>
</rss>
