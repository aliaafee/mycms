<?php

include "../pagegenerator.class.php";
include "../settings.inc.php";

$page = new pageGenerator($settings,'base-nosidebar');

$page->setMetaData('Webmaster','Description of sample 1','Keywords');
$page->generateBreadCrumbsCustom(Array(
	Array('Sample 1',$settings['siteURI'].'/sample1/')
));
$page->rootId = 'sample1';

$page->appBody('
<h1>Sample 1</h1>

<p>This is a sample page</p>

');
$page->generateAndDisplay();

?>
