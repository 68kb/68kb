<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<rss version="2.0" 
	xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <channel>
    
    <title><?php echo $site_title; ?></title>
    <link><?php echo base_url(); ?></link>
    <description><?php echo $site_description ?></description>
    <dc:language>en</dc:language>
	<dc:rights>Copyright <?php echo date("Y"); ?></dc:rights>
    <dc:date><?php echo gmdate("Y-m-d\TH:i:s\Z", time()); ?></dc:date>
    <admin:generatorAgent rdf:resource="http://68kb.com/" />
    
		<?php foreach($items as $row): ?>

			<item>
				<title><?php echo xml_convert($row['article_title']); ?></title>
				<link><?php echo site_url('article/'.$row['article_uri']); ?></link>
				<guid><?php echo site_url('article/'.$row['article_uri']); ?></guid>
				<description><?php echo xml_convert($row['article_description']); ?></description>
				<dc:date><?php echo gmdate("Y-m-d\TH:i:s\Z", $row['article_date']); ?></dc:date>
			</item>

		<?php endforeach; ?>

	</channel>
</rss>