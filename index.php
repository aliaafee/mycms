<?php

include "pagegenerator.class.php";
include "settings.inc.php";

if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$page = new pageGenerator($settings,'base');
	$page->fetchBody($id);
	if (isset($_GET['hl'])) {
		$page->hilightBody($_GET['hl']);
	}
	$page->generateAndDisplay();
	exit();
}

$page = new pageGenerator($settings,'base-plain');
$page->rootId = 'home';
$page->setMetaData('Webmaster','Description of home page','Keywords');
/*
    * fade
    * crossFade
    * fadeThroughBackground
    * pushLeft, pushRight, pushUp, pushDown
    * blindLeft, blindRight, blindUp, blindDown
    * blindLeftFade, blindRightFade, blindUpFade, blindDownFade
*/
$page->setSubTitle(
'
<ul id="slideshow" class="large">
	<li class="transition:fade duration:1000">
		<span class="image">
			<img alt="fish1" src="galleryf/Test/DSC01395.JPG?size=normal" />
		</span>
		<span class="desc">
			<span class="desc-title">Todays Top Story</span> 
			<span class="desc-body">
				Details about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story.[<a href="">more</a>]
			</span>
		</span>
	</li>
	<li class="transition:fade duration:1000">
		<span class="image">
			<img alt="fish2" src="galleryf/Test/DSC01396.JPG?size=normal" />
		</span>
		<span class="desc">
			<span class="desc-title">Todays Second Story</span> 
			<span class="desc-body">
				Details about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story about todays top story.[<a href="">more</a>]
			</span>
		</span>
	</li>
</ul>
'
);
$page->appBody($page->fillTemplate('homebody',array(
	'sidebar'	=> '<h1>About Us</h1><p>Paragrah descibing us in extreme detail</p>',
	'body-top'	=> '<h1>Recent News</h1><ul><li>Item1</li><li>Item2</li></ul>',
	'body-bottom'	=> ''
)));

$page->generateAndDisplay();
?>
