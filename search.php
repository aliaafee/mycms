<?php

include "pagegenerator.class.php";
include "settings.inc.php";

if (isset($_GET['q'])) {
	$q = $_GET['q'];
	$page = new pageGenerator($settings,'base');
	$page->setTitle($q.$settings['titlesep'].'Search Results');
	$page->fetchSearchResults($q);
	$page->generateAndDisplay();
	exit();
}

$page->setMetaData('Webmaster','Search page','Keywords');
$page->appBody('<p>Enter query in the search box</p>');
$page->generateAndDisplay();

?>
