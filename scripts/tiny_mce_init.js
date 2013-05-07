tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	plugins : "table,advimage,advlink,emotions,iespell,inlinepopups,preview,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,wordcount,advlist",

	// Theme options
	theme_advanced_buttons1 : "fullscreen,|,undo,redo,,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,tablecontrols,|,preview",
	theme_advanced_buttons2 : "formatselect,removeformat,|,bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,|,link,unlink,anchor,|,image,|,hr,|,sub,sup,|,charmap,nonbreaking,emotions,iespell",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,

	// Example content CSS (should be your site CSS)
	content_css : "{template-path}/css/screen.css",

	// Drop lists for link/image/media/template dialogs
	external_link_list_url : "lists/link-list.php",
	external_image_list_url : "lists/image-list.php",
	
	//File browser call back
	file_browser_callback : "fileBrowserCallBack",
});

function fileBrowserCallBack(field_name, url, type, win) {
	var cmsURL = "galleryf/?popup";
	if (cmsURL.indexOf("?") < 0) {
		//add the type as the only query parameter
		cmsURL = cmsURL + "?type=" + type;
	}
	else {
		//add the type as an additional query parameter
		// (PHP session ID is now included if there is one at all)
		cmsURL = cmsURL + "&type=" + type;
	}

	tinyMCE.activeEditor.windowManager.open({
		file : cmsURL,
		title : 'Gallery Browser',
		width : 555,  // Your dimensions may differ - toy around with them!
		height : 400,
		resizable : "yes",
		inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
		close_previous : "no",
		popup_css : false
	}, {
		window : win,
		input : field_name
	});
	return false;
};

var tinyMCEon = true;
