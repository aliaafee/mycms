<?php
error_reporting(E_ALL);
$settings = array (
	'siteName'	=> 'MSA&nbsp;Nepal',
	'siteURI'	=> '/~ali/mycms',
	'sitePath'	=> '/Users/ali/Public/mycms',
	'template'	=> 'templates/basic',
	'database'	=> array (
		'type' => 'sqlite',
		'file' => '/Users/ali/Public/mycms/db.sqlite'
	),
	'breadcrumbsep'=> '&nbsp;&raquo; ',
	'titlesep'	=> ' - '
);

$settings['preMenu'] = array (
	'home'	=> array('Home',$settings['siteURI'].'/'),
	'news'	=> array('News',$settings['siteURI'].'/news/')
);

$settings['postMenu'] = array (
	'gallery'	=> array('Gallery',$settings['siteURI'].'/gallery/')
);
?>
