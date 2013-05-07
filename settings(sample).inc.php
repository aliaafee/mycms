<?php
error_reporting(E_ALL);
$settings = array (
	'siteName'	=> 'Example Site',
	'siteURI'	=> 'uri/to/site',
	'sitePath'	=> '/path/to/site',
	'template'	=> 'templates/basic',
	'database'	=> array (
		'type' => 'sqlite',
		'file' => '/path/to/dbfile'
	),
	'breadcrumbsep'=> '&nbsp;&raquo; ',
	'titlesep'	=> ' - '
);

$settings['preMenu'] = array (
	'home'	=> array('Home',$settings['siteURI'].'/'),
	'sample1'	=> array('Sample 1',$settings['siteURI'].'/sample1/')
);

$settings['postMenu'] = array (
	'sample2'	=> array('Sample 2',$settings['siteURI'].'/sample2/')
);
?>
