<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">

<channel>
  <title><?php echo $site_url ?> Crawls</title>
  <link><?php echo $site_url ?></link>
  <description>Crawler for SwiftRiver installation at <?php echo $site_url ?></description>
  <item>
    <title>Crawl <?php echo $request_date ?></title>
    <link><?php echo $site_url ?></link>
    <description>New crawl has been requested at <?php echo $request_date ?></description>
  </item>
</channel>

</rss>