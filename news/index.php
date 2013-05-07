<?php

include "../pagegenerator.class.php";
include "../settings.inc.php";

$page = new pageGenerator($settings,'base-nosidebar');

$page->setMetaData('Webmaster','Description of gallery','Keywords');
$page->generateBreadCrumbsCustom(Array(
	Array('News',$settings['siteURI'].'/news/')
));
$page->rootId = 'news';

$page->appBody('
<h1>News</h1>

');
$page->generateAndDisplay();

?>
