<?php

include "pagegenerator.class.php";
include "settings.inc.php";

$user = 'ali';
$pwd  = 'log';

session_start();

if(!isset($_SESSION['SESS_MEMBER_ID'])) {
	$page = new pageGenerator($settings,'nodecoration');
	
	if (isset($_POST['user']) && isset($_POST['pwd'])) {
		if ($_POST['user'] == $user && $_POST['pwd'] == $pwd) {
			session_regenerate_id();
			$_SESSION['SESS_MEMBER_ID'] = $user;
			session_write_close();
			header('location: '.$settings['siteURI'].'/edit.php');
			exit();
		}
	}
	
	$page->appBody(
		'<div style="width: 300px; border: solid 1px black; background-color: #dddddd; margin: 50px auto;" />'.
			'<div style="border-bottom: solid 1px black; color: white; background-color: gray; padding: 5px;" />Login</div>'.
			'<form method="post" name="login" submit="'.$settings['siteURI'].'/?login">'.
				'<table style="width: 280px; margin: 10px;">'.
				'<tr><td style="width: 80px;"><label for="user">User</label></td><td><input style="width: 100%;" name="user" type="text" value="" /></td></tr>'.
				'<tr><td><label for="pwd">Password</label></td><td><input style="width: 100%;" name="pwd" type="password" value="" /></td></tr>'.
				'<tr><td colspan="2" style="text-align: center;"><input type="submit" name="login" value="Login" /></td></tr>'.
				'</table>'.
			'</form>'.
		'</div>'
	);
	
	$page->generateAndDisplay();
	exit();
}

if (isset($_GET['logout'])) {
	unset($_SESSION['SESS_MEMBER_ID']);
	header('location: '.$settings['siteURI'].'/index.php');
	exit();
}
/*
if(!isset($_SESSION['SESS_MEMBER_ID'])) {
	header('location: '.$settings['siteURI'].'/index.php');
	exit();
}
*/
if (isset($_GET['sidepan'])) {
	//Site Structure Editor
	$page = new pageGenerator($settings,'nodecoration');
	
	if (isset($_POST['parent']) && isset($_POST['title'])) {
		$date = '2010/12/5';
		$page->db->insertContent($_POST['parent'],0,1,$_POST['title'],'',$date);
		header('location: '.$settings['siteURI'].'/edit.php?sidepan');
		exit();
	}
	
	if (isset($_GET['up'])) {
		$page->db->orderUp($_GET['up']);
		header('location: '.$settings['siteURI'].'/edit.php?sidepan');
		exit();
	}
	
	if (isset($_GET['down'])) {
		$page->db->orderDown($_GET['down']);
		header('location: '.$settings['siteURI'].'/edit.php?sidepan');
		exit();
	}
	
	if (isset($_GET['remove'])) {
		$page->db->removeContent($_GET['remove'],$recursive=true);
		header('location: '.$settings['siteURI'].'/edit.php?sidepan');
		exit();
	}
	
	$page->setTitle("Structure Editor");
	
	$page->includeCssLink($settings['siteURI'].'/css/edit.css');
	
	$page->includeScript("
		function hideTools(id) {
			document.getElementById('tool'+id).className='hidden';
		}
		function showTools(id) {
			document.getElementById('tool'+id).className='';
		}
		function addItem(id) {
			var newListItem = new Element('li',{html: '<form method=\"post\" name=\"editor\" submit=\"?sidepan\" ><input style=\"display: none;\" name=\"parent\" type=\"text\" value=\"'+id+'\" /><input name=\"title\" type=\"text\" value=\"\" />&nbsp;<input type=\"submit\" name=\"save\" value=\"Add\" /></form>'});
			var it = \$\$('#structItem'+id+' ul');
			var w = false;
			if (it.length) {
				newListItem.inject(it[0]);
			} else {
				it = \$\$('#structItem'+id);
				newListItem = new Element('ul',{html: '<li><form method=\"post\" name=\"editor\" submit=\"?sidepan\" ><input style=\"display: none;\" name=\"parent\" type=\"text\" value=\"'+id+'\" /><input name=\"title\" type=\"text\" value=\"\" />&nbsp;<input type=\"submit\" name=\"save\" value=\"Add\" /></form></li>'});
				newListItem.inject(it[0]);
			}
		}
		function confirmDelete(id,title) {
			if (confirm('Are you sure you want to remove \"'+title+'\" ?')) {
				window.location.href = '".$settings['siteURI']."/edit.php?sidepan&remove='+id;
			}
		}
		function moveUp(id) {
			window.location.href = '".$settings['siteURI']."/edit.php?sidepan&up='+id;
		}
		function moveDown(id) {
			window.location.href = '".$settings['siteURI']."/edit.php?sidepan&down='+id;
		}
		function toggleSection(sectionId) {
			if (document.getElementById(sectionId).className == 'section') {
				document.getElementById(sectionId).className = 'hidden';
			} else {
				document.getElementById(sectionId).className = 'section';
			}
		}
	");
	
	function makeSection($name,$title,$content) {
		return	'<div class="section-head" onClick="toggleSection(\''.$name.'\')">'.$title.'</div>'.
				'<div class="section" id="'.$name.'">'.$content.'</div>';
	}
	
	$page->appBody('<div id="toolbox">');
	$page->appBody('<a title="Logout" href="'.$settings['siteURI'].'/edit.php?logout" target="base"><image src="images/logout.png" alt="logout" /></a>');
	$page->appBody('</div>');
	$page->appBody(makeSection('struct','Site Contents',
		'<ul id="struct" >'.
			'<li id="structItem0" >'.
				'<span onmouseover="showTools(\'0\')" onmouseout="hideTools(\'0\')"  style="display: block;">'.
					$settings['siteName'].
					'<span id="tool0" class="hidden">'.
						'<a onClick="addItem(\'0\')"><image src="images/add.png" alt="+" /></a> '.
					'</span>'.
				'</span>'.
					$page->getStructure(
						$parent = 0,
						$depth = 0,
						$list = '<ul>{list}</ul>',
						$item = '<li id="structItem{id}" >'.
								'<span onmouseover="showTools(\'{id}\')" onmouseout="hideTools(\'{id}\')"  style="display: block;">'.
									'<a href="'.$settings['siteURI'].'/edit.php?id={id}" target="content">{title}</a>'.
									'<span id="tool{id}" class="hidden">'.
										'<a onClick="moveUp(\'{id}\')"><image src="images/arrow_up.png" alt="^" /></a>'.
										'<a onClick="moveDown(\'{id}\')"><image src="images/arrow_down.png" alt="v" /></a> '.
										'<a onClick="addItem(\'{id}\')"><image src="images/add.png" alt="+" /></a> '.
										'<a onClick="confirmDelete(\'{id}\',\'{title}\')"><image src="images/delete.png" alt="x" /></a>'.
									'</span>'.
								'</span>'.
								'{sublist}'.
							   '</li>',
						$subListMark = '',
						$first = true,
						$firstSubListMark = '',
						$firstList ='<ul>{list}</ul>',
						$includeDisabled = true
					).
			'</li>'.
		'</ul>'
	));
	$page->appBody(makeSection('admin','Administration',
		'<p>Admin Stuff</p>'
	));
	
	$page->generateAndDisplay();
	exit();
}

if (isset($_GET['id'])) {
	//Page Editor
	$page = new pageGenerator($settings,'nodecoration');
	
	$id = $_GET['id'];
	
	$status = "";
	if (isset($_POST['title']) && isset($_POST['content'])) {
		$enabled = 0;
		if (isset($_POST['enabled'])) { $enabled = 1; }
		if ($page->db->updateContent($id,$_POST['title'],$_POST['content'],$enabled)) {
			$status = "saved";
		} else {
			$status = "cannot save";
		}
	}
	
	if ($content = $page->db->getContent($id,true)) {
		$page->setTitle("Page Editor");

		$page->includeScriptLink("scripts/tiny_mce/tiny_mce.js");
		$page->includeScript(file_get_contents("scripts/tiny_mce_init.js"));
		
		$page->includeCssLink('css/edit.css');

		$page->appBody('<form method="post" name="editor" submit="'.$settings['siteURI'].'/edit.php" >');
		$page->appBody('<div id="toolbox">');
		$page->appBody('<a title="Save" onClick="document.editor.submit()"><input type="submit" style="display:none;" value="Save" /><image src="images/save.png" alt="save" /></a> ');
		$page->appBody('<a title="View Page" href="'.$settings['siteURI'].'/index.php?id='.$id.'"><image src="images/view.png" alt="view page" /></a>');
		$page->appBody('<span class="seperator"></span>');
		$page->appBody('<a title="Toggle" href="#" onClick="if (tinyMCEon) {tinyMCE.get(\'content\').hide();tinyMCEon = false;} else {tinyMCE.get(\'content\').show();tinyMCEon = true;}"><image src="images/editor.png" alt="toggle editor" /></a> ');
		//$page->appBody('<input type="submit" name="save" value="" style="background: url(\'images/save.png\') no-repeat scroll center center transparent;" />  ');	
		$page->appBody('<span class="seperator"></span>');
		$page->appBody("<span id=\"status\">$status</span>");
		$page->appBody('</div>');
		$page->appBody('<div class="section-head">');
		$page->appBody('Editing: <span class="">'.$page->getBreadCrumbTrail($id,true,true,'<a href="'.$settings['siteURI'].'/edit.php?id={id}">{title}</a>').'</span>');
		$page->appBody('</div>');
		$page->appBody('<div style="padding: 2px;">');
		$page->appBody('<label for="title">Title</label><input name="title" type="text" value="'.htmlspecialchars($content['title']).'" />');
		$page->appBody(' | <label for="enabled">Enabled</label><input type="checkbox" name="enabled" value="1" ');
		if ($content['enabled'] == 1) {$page->appBody('checked');}
		$page->appBody('/>');
		$page->appBody('</div>');
		$page->appBody('<textarea name="content" id="content" rows="15" style="width: 650px;;height: 480px;">');
		$page->appBody(htmlentities($content['content']));
		$page->appBody('</textarea>');
		$page->appBody('</form>');
	} else {
		$page->setBody('<h1>Not Found</h1><p>Requested page not found.</p>');
	}
	$page->generateAndDisplay();
	exit();
}

?>
<html>
	<head>
		<title>Editor</title>
	</head>
	<frameset cols="300px,*" name="base">
	   <frame src="<?php echo $settings['siteURI']; ?>/edit.php?sidepan" name="structure"/>
	   <frame src="" name="content"/>
	</frameset> 
</html>



