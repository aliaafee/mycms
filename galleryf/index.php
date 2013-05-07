<?php
// TeeneeWeenee Gallery v0.1
// 	(c) Ali Aafee
// 	Free to modify and distrubute, nice if you give
// 	credit
//
// 	No GD version.
//
//Gallery Settings
	//include '../config.inc.php';

	$gallerySetting['albumName'] = 'Gallery';
	$gallerySetting['galleryPath'] = '/Users/ali/Public/mycms/galleryf';
	$gallerySetting['galleryURI'] = '/~ali/mycms/galleryf';
	$gallerySetting['displayLables'] = true;
	$gallerySetting['startDirectory'] = '';
	$gallerySetting['cacheThumbnails'] = true;
	$gallerySetting['columns'] = 5;
	$gallerySetting['popUpMode'] = false;
	$gallerySetting['tinyMCEURI'] = "/ali/myCMS/scripts/tiny_mce";
	
function placeGalleryHeader() {
	global $gallerySetting;
	if (!$gallerySetting['popUpMode']) {
		//placePageHeaderD('Gallery'); 
		//echo '<h1>Gallery</h1>';
		echo '<html><head><style type="text/css">img { border: none; } a:hover { background: silver; }</style></head><body><h1>Gallery</h1>';
	} else {
		echo '<html><head>';
		echo '<script language="javascript" type="text/javascript" src="'.$gallerySetting['tinyMCEURI'].'/tiny_mce_popup.js"></script>';
		echo '<script language="javascript" type="text/javascript">';
		echo '
		var FileBrowserDialogue = {
			init : function () {
				// Here goes your code for setting your custom things onLoad.
			},
			selectImage : function (URL) {
				var size = "normal";
				if (URL.indexOf("?") < 0) {
					URL = URL + "?size=" + size;
				}
				else {
					URL = URL + "size=" + size;
				}
				
				var win = tinyMCEPopup.getWindowArg("window");

				// insert information now
				win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

				// are we an image browser
				if (typeof(win.ImageDialog) != "undefined") {
					// we are, so update image dimensions...
					if (win.ImageDialog.getImageData)
						win.ImageDialog.getImageData();

					// ... and preview if necessary
					if (win.ImageDialog.showPreviewImage)
						win.ImageDialog.showPreviewImage(URL);
				}

				// close popup window
				tinyMCEPopup.close();
			}
		}

		tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
		';
		
		echo '</script>';
		echo '</head><body>';
		echo '<form name="sizeSelector">Size: ';
		echo '<input type="radio" name="size" value="small" />Small';
		echo '<input type="radio" name="size" value="medium" />Medium';
		echo '<input type="radio" name="size" value="large" />Large';
		echo '</from>';
		
	}
	return true;
}

function placeGalleryFooter() {
	global $gallerySetting;
	if (!$gallerySetting['popUpMode']) {
		//placePageFooterD();
		echo "</body></html>";
	} else {
		echo "</body></html>";
	}
	return true;
}

function escapeFileName($fileName) {
	return trim(str_replace(':',' ',str_replace('..','',escapeshellcmd(urldecode($fileName)))));
}

function isValidFile($fileName) {
	if (strtoupper(substr($fileName,-4,4)) == '.JPG') {
		return true;
	}
	return false;
}


function getFolderContents($folderName) {
	if ($handle = @opendir($folderName)) {
		$photoList = array();
		$folderList = array();
		while ($file = readdir($handle)) {
			if (is_dir($folderName.'/'.$file)) {
				if ($file != '.') {
					$folderList[] = $file;
				}
			} elseif (isValidFile($file)  && substr($file,0,3)!='tn_'  && substr($file,0,3)!='ss_')  {
				$photoList[] = $file;
			} 
    		}
		closedir($handle);
		if (count($photoList)>1) {sort($photoList);}
		if (count($folderList)>1) {sort($folderList);}

		return array('photos' => $photoList, 'folders' => $folderList);
	} else {
		echo "error ". $folderName;
		return false;
	}
}

function placeImageLink($href,$imagesrc,$imagealt,$lable="",$onclick="") {
	global $gallerySetting;
	if ($gallerySetting['popUpMode']) {
		$href .= "&popup";
	}
	if ($onclick == "") {
		echo '<a href="'.$href.'">';
	} else {
		echo '<a href="#" onClick="'.$onclick.'">';
	}
	echo '<img src="'.$imagesrc.'" alt="'.$imagealt.'" />';
	if ($lable != "") {
		echo '<div style="text-align:center;">'.$lable.'</div>';
	}
	echo '</a>';
}

if (isset($_GET['p'])) {
	$currentDirectoryRelative = escapeFileName($_GET['p']);
	if ($currentDirectoryRelative == '/') { $currentDirectoryRelative = '';}
} else {
	$currentDirectoryRelative = $gallerySetting['startDirectory'];
}

$currentDirectory = $gallerySetting['galleryPath'].$currentDirectoryRelative;
$currentURI = $gallerySetting['galleryURI'].$currentDirectoryRelative;

if (isset($_GET['tn'])) {
	//Generate thumbnails
	$sourceFileName = $currentDirectory.'/'.escapeFileName($_GET['tn']);
	$thumbnailFileName = $currentDirectory.'/tn_'.escapeFileName($_GET['tn']);

	if (isValidFile($thumbnailFileName)) {
		if (!isset($_GET['x'])) {
			header ("Content-type: image/jpeg");
		}
		readfile($thumbnailFileName);
	}
} elseif (isset($_GET['fl'])) {
	//Generate Thumbnails for folders
	$sourceFolderName = escapeFileName($_GET['fl']);
	$thumbnailFileName = $currentDirectory.'/'.$sourceFolderName.'/tn_'.$sourceFolderName.'_folder.jpg';
	
	if (isValidFile($thumbnailFileName)) {
		if (!isset($_GET['x'])) {
			header ("Content-type: image/jpeg");
		}
		readfile($thumbnailFileName);
	}
} elseif (isset($_GET['vw'])) {
	//Photoviewer
	$sourceFileName = escapeFileName($_GET['vw']);
	$sourceFileURI = $currentURI.'/'.$sourceFileName;
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\r\n";
	echo '	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\r\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\r\n";
	echo '<head><title>Gallery</title></head>'."\r\n";
	echo '<html>'."\r\n";
	echo '<div id="photoViewer" style="text-align:center">'."\r\n";
	if ($folderContents = getFolderContents($currentDirectory)) {
		if (($currentPhoto = array_search($sourceFileName,$folderContents['photos'])) !== false) {
			$nextPhoto = $currentPhoto + 1;
			$previousPhoto = $currentPhoto - 1;
			echo '<div id="navigationBar" style="position:fixed; width: 176px; left: -88px; margin-left: 50%;text-align: center; background-color: #c9ddfe; border: solid 1px #84a8fe; padding: 3px; filter:alpha(opacity=80); -moz-opacity:0.8; -khtml-opacity: 0.8; opacity: 0.8;" left="0" top="0">'."\r\n";
			if (isset($folderContents['photos'][$previousPhoto])) {
				echo '		<a href="./index.php?p='.urlencode($currentDirectoryRelative).'&vw='.$folderContents['photos'][$previousPhoto].'" title="Previous">';
				echo '<img style="border: none;" src="../images/back.gif" alt="Previous" /></a>'."\r\n";
			} else {
				echo '		';
			}
			
			echo '		&nbsp;&nbsp;&nbsp;<a href="./index.php?p='.urlencode($currentDirectoryRelative).'" title="Back To Gallery"><img style="border: none;" src="../images/gallery.gif" alt="To Gallery" /></a>&nbsp;&nbsp;&nbsp;'."\r\n";
			if (isset($folderContents['photos'][$nextPhoto])) {
				echo '		<a href="./index.php?p='.urlencode($currentDirectoryRelative).'&vw='.$folderContents['photos'][$nextPhoto].'" title="Next">';
				echo '<img style="border: none;" src="../images/next.gif" alt="Forward" /></a>'."\r\n";
			} else {
				echo '		';
			}
			echo '	</div>'."\r\n";
			echo '	<div style="height: 30px">&nbsp</div>';
			echo '	<img src="'.htmlspecialchars($sourceFileURI).'" alt="'.$sourceFileName.'"/>'."\r\n";
		} else {
			echo '<p>Photo <b>'.$sourceFileName.'</b>  Not Found in Gallery <b>'.$currentDirectoryRelative.'</b></p>';
			echo '<code>';
			print_r($folderContents);
			echo '</code>';
		}
	} else {
		echo '<p>Gallery <b>'.$currentDirectoryRelative.'</b>  Not Found</p>';
	}
	echo '</div>'."\r\n";
	echo '</html>';
} else {
	//Check if tiny MCE popup
	if (isset($_GET['popup'])) {
		$gallerySetting['popUpMode'] = true;
	}
	
	placeGalleryHeader();
	
	//Gallery Brownser
	if ($folderContents = getFolderContents($currentDirectory)) {
		echo '<table id="gallery" style="margin: auto;">';
	
		$column = 1;
	
		if ($currentDirectoryRelative == '') {
			unset($folderContents['folders'][0]);
		}
		
		$folders = $folderContents['folders'];
		$photos = $folderContents['photos'];
	
		$cellCounter = 0;
		echo '<tr>';
		foreach ($folders as $key => $folder) {
			echo '<td>';
			if ($folder == '..') {
				$t = explode('/',$currentDirectoryRelative);
				unset($t[count($t)-1]);
				placeImageLink(
					$href = $gallerySetting['galleryURI'].'/index.php?p='.urlencode(implode($t,'/')),
					$imagesrc = "../images/back.png",
					$imagealt = "Back",
					$lable = ""
				);
			} else {
				placeImageLink(
					$href = $gallerySetting['galleryURI'].
								'/index.php?p='.urlencode($currentDirectoryRelative.'/'.$folder),
					$imagesrc = $gallerySetting['galleryURI'].
								'/index.php?p='.urlencode($currentDirectoryRelative).'&fl='.urlencode($folder),
					$imagealt = "Album",
					$lable = $folder
				);
			}
			echo '</td>';
			$cellCounter++;
			if ($cellCounter == $gallerySetting['columns']) {
				$cellCounter = 0;
				echo '</tr><tr>';
			}
		}
		
		if (count($folderContents['photos']) != 0) {
			foreach ($photos as $key => $photo) {
				echo '<td>';
				if (!$gallerySetting['popUpMode']) {
					placeImageLink(
						$href = './index.php?p='.urlencode($currentDirectoryRelative).'&vw='.urlencode($photo),
						$imagesrc = $gallerySetting['galleryURI'].'/index.php?p='.urlencode($currentDirectoryRelative).'&tn='.urlencode($photo),
						$imagealt = $photo,
						$lable = ""
					);
				} else {
					placeImageLink(
						$href = "",
						$imagesrc = $gallerySetting['galleryURI'].'/index.php?p='.urlencode($currentDirectoryRelative).'&tn='.urlencode($photo),
						$imagealt = $photo,
						$lable = "",
						$onclick = "FileBrowserDialogue.selectImage('".$gallerySetting['galleryURI'].$currentDirectoryRelative.'/'.$photo."')"
					);				
				}
				echo '</td>';
				$cellCounter++;
				if ($cellCounter == $gallerySetting['columns']) {
					$cellCounter = 0;
					echo '</tr><tr>';
				}
			}
			
		}
		
		if ($cellCounter != 0) {
			echo '</tr>';
		}	
	
		echo '</table>';
	
	} else {
		echo '<p>Gallery <b>'.$currentDirectoryRelative.'</b>  Not Found</p>';
	}
	
	placeGalleryFooter();
} ?>

