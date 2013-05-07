<?php

include "../pagegenerator.class.php";
include "../settings.inc.php";

$page = new pageGenerator($settings,'base-nosidebar');

$page->setMetaData('Webmaster','Description of gallery','Keywords');
$page->generateBreadCrumbsCustom(Array(
	Array('Gallery',$settings['siteURI'].'/gallery/')
));
$page->rootId = 'gallery';

$page->appBody(
'<iframe src="'.$settings['siteURI'].'/galleryf/" name="Gallery" style="width: 100%; height: 400px; border:none;">
<h1>Gallery</h1>
</iframe>'
);
$page->generateAndDisplay();

?>
