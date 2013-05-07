<?php

include "../pagegenerator.class.php";
include "../settings.inc.php";

$page = new pageGenerator($settings,'base-nosidebar');

$page->setMetaData('Webmaster','Description of sample 2','Keywords');
$page->generateBreadCrumbsCustom(Array(
	Array('Sample 2',$settings['siteURI'].'/sample2/')
));
$page->rootId = 'sample2';

$page->appBody('
<h1>Sample 2</h1>

<p>This is a sample page</p>

');
$page->generateAndDisplay();

?>
