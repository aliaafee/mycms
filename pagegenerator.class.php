<?php
/*
	mycms
	pageGenerator
*/

include 'dbconnection.class.php';

class pageGenerator {
	protected $setting;
	protected $templateName;
	protected $content;
	private $generatedPage;
	private $templateCache;
	private $searchQuery;
	public $db;
	public $rootId;
	public $rootTitle;
	
	public function __construct($settings,$templateName='base') {
		$this->setting = $settings;
		$this->templateName = $templateName;
		$this->content = array (
			'scripts'	=> '',
			'css' 	=> '',
			'homepage' => $this->setting['siteURI'].'/',
			'title'	=> $this->setting['siteName'],
			'subtitle' => '',
			'description'	=> '',
			'keywords'	=> '',
			'author'		=> '',
			'head'	=> $this->setting['siteName'],
			'breadcrumbs'	=> '',
			'sidebar'	=> '',
			'body'	=> '',
			'menu'	=> '',
			'searchbox'	=> '',
			'footer'	=> ''
		);
		$generatedPage = '';
		$this->db = new dbConnection($this->setting);
		$this->db->connect();
	}
	
	public function __destruct() {
		
	}
	
	public function setTitle($title) {
		if ($title != "") {
			$this->content['title'] = $title . $this->setting['titlesep'] .$this->setting['siteName'];
		} else {
			$this->content['title'] = $this->setting['siteName'];
		}
	}
	
	public function setSubTitle($subtitle) {
		$this->content['subtitle'] = $subtitle;
	}
	
	public function includeScriptLink($path) {
		$this->content['scripts'] .= '<script src="'.$path.'" type="text/javascript"></script>';
	}
	
	public function includeScript($script) {
		$this->content['scripts'] .= '<script type="text/javascript"><!--'."\r\n".$script."\r\n".'//--></script>';
	}
	
	public function includeCssLink($path) {
		$this->content['css'] .= '<link rel="stylesheet" type="text/css" href="'.$path.'" />';
	}
	
	public function includeCss($css) {
		$this->content['css'] .= '<style type="text/css">'."\r\n".$css."\r\n".'</style>';
	}
	
	public function setMetaData($author,$description,$keywords) {
		$this->content['author'] = $author;
		$this->content['description'] = $description;
		$this->content['keywords'] = $keywords;
	}
	
	public function setSideBar($sidebar) {
		$this->content['sidebar'] = $sidebar;
	}
	
	public function setBody($body) {
		$this->content['body'] = $body;
	}
	
	public function appBody($body) {
		$this->content['body'] .= $body;
	}
	
	public function fetchBody($id) {
		if ($content = $this->db->getContent($id)) {
			$this->generateBreadCrumbs($id,$content['parent'],$content['title']);
			$this->generateSideBarMenu($id);
			$this->setTitle($content['title']);
			$this->setBody('<h1>'.$content['title'].'</h1>');
			/*
			if ($content['date'] != '') {
				$this->appBody('<div class="date">'.$content['date'].'</div>');
			}
			*/
			$this->appBody($content['content']);
			$this->setMetaData('Author','Description','Keywords');
		} else {
			$this->setBody('<h1>Not Found</h1><p>Requested page not found. Try using search.</p>');
		}
	}
	
	public function hilightBody($string) {
		$phrase = explode(' ',$string);
		foreach($phrase as $key => $word) {
			if ($word != "") {
				$this->content['body'] = str_replace($word,
						'<span style="background-color: #FFFF00">'.$word.'</span>',
						$this->content['body']);
			}
		}
	}
	
	public function fetchSearchResults($query) {
		$result = $this->db->search($query);
		$this->setMetaData('Webmaster','Search results for: &ldquo;'.htmlentities($query).'&rdquo;','Keywords');
		$this->setBody("<h1>Search Results</h1><p>Query &ldquo;".htmlentities($query)."&rdquo; returned ".count($result)." results.</p>");
		foreach ($result as $key => $row) {
			$record = array(
				'result-link' => $this->setting['siteURI'].'/?id='.$row[0],
				'result-title' => $row[1],
				'result-breadcrumbs' => $this->getBreadCrumbTrail($row[0],false,false,
										'<a href="'.$this->setting['siteURI'].'/?id={id}">{title}</a>'),
				'result-body' => htmlentities($this->chopString(strip_tags($row[2]),200)),
			);
			$this->appBody($this->fillTemplate('searchresult',$record));
		}
		$this->generateBreadCrumbsCustom(Array(
			Array('Search: &ldquo;'.htmlentities($query).'&rdquo;' ,'?q='.urlencode($query))
		));
		$this->rootId = 'search';
		$this->rootTitle = 'Search Results';
		$this->searchQuery = $query;
	}
	
	private function chopString($string,$length) {
		if (strlen($string) > $length) {
			return substr(($string=wordwrap($string,$length,"$$\r\n")),0,strpos($string,"$$\r\n")).'...';
		}
		return $string;
	}
	
	public function generate() {
		$this->generateMenu();
		$this->generateSearchBox();
		$this->generateFooter();
		$this->generatedPage = $this->fillTemplate($this->templateName,$this->content);
		$this->generatedPage = str_replace('{template-path}',$this->setting['siteURI'].'/'.$this->setting['template'],$this->generatedPage);
	}
	
	public function display() {
		echo $this->generatedPage;
	}
	
	public function generateAndDisplay() {
		$this->generate();
		$this->display();
	}
	
	public function getBreadCrumbTrail($id,$showRoot=false,$showSelf=false,$link='<a href="?id={id}">{title}</a>') {
		$info = $this->db->getInfo($id);
		$title = $info['title'];
		$parent = $info['parent'];
		if (!$showRoot) {
			if ($parent == 0) { return ''; }
		}
		if ($showSelf) {
			$trail = $this->fillTemplateString($link,Array('id'=>$id,'title'=>$title));
		} else {
			$trail = '';
		}
		while ($parent != 0) {
			$info = $this->db->getInfo($parent);
			$id = $info['id'];
			$title = $info['title'];
			$parent = $info['parent'];
			$trail = $trail = $this->fillTemplateString($link,Array('id'=>$id,'title'=>$title)) .
					$this->setting['breadcrumbsep'].
					$trail;
		}
		$this->rootId = $id;
		$this->rootTitle = $title;
		return $trail;
	}
	
	public function generateBreadCrumbs($id,$parent,$title) {
		$this->content['breadcrumbs'] = 	'<a href="'.$this->content['homepage'].'">'.$this->setting['siteName'].'</a>' .
									$this->setting['breadcrumbsep'].
									$this->getBreadCrumbTrail($id,true,false,'<a href="'.$this->setting['siteURI'].'/?id={id}">{title}</a>');
	}
	
	public function generateBreadCrumbsCustom($linkList,$showHome=true) {
		if ($showHome) {
			$this->content['breadcrumbs'] = '<a href="'.$this->content['homepage'].'">'.$this->setting['siteName'].'</a>';
		}
		foreach ($linkList as $index => $item) {
			$this->content['breadcrumbs'] .= $this->setting['breadcrumbsep'];
			if ($index != 0) {
				$this->content['breadcrumbs'] .= '<a href="'.$item[1].'">'.$item[0].'</a>';
			}
		}
	}
	
	private function generateMenu() {
		//Generate main menu
		$list = $this->getStructure(
			$parent = 0,
			$depth = 0, // if depth is set to 0 then everything is displayed
			$list = '<ul style="visibility: hidden;opacity: 0;">{list}</ul>',
			$item = '<li id="menuItem{id}"><a href="'.$this->setting['siteURI'].'/?id={id}">{title}{sublistmark}</a>{sublist}</li>',
			$subListMark = '<span class="sublistmark"></span>',
			$first = true,
			$firstSubListMark = '<span class="rootsublistmark"></span>',
			$firstList = '{list}',
			$includeDisabled = false,
			$selectedItem = '<li id="menuItem{id}" class="selected"><a href="'.$this->setting['siteURI'].'/?id={id}">{title}{sublistmark}</a>{sublist}</li>',
			$selectedItemId = $this->rootId
		);
		//Generate Pre Menu Items
		$preMenu = '';
		foreach ($this->setting['preMenu'] as $id => $item) {
			$preMenu .= '<li id="menuItem'.$id.'" ';
			if ($this->rootId == $id) { $preMenu .= 'class="selected"'; }
			$preMenu .= '><a href="'.$item[1].'">'.$item[0].'</a></li>';
		}
		$list = $preMenu.$list;
		//Generate Post Menu Items
		foreach ($this->setting['postMenu'] as $id => $item) {
			$list .= '<li id="menuItem'.$id.'" ';
			if ($this->rootId == $id) { $list .= 'class="selected"'; }
			$list .= '><a href="'.$item[1].'">'.$item[0].'</a></li>';
		}
		
		$this->content['menu'] = 
			'<ul id="menu">'.
				$list.
			'</ul>';
	}
	
	private function generateSideBarMenu($id) {
		$menu = $this->getStructure(
			$parent = $this->rootId,
			$depth = 0, // if depth is set to 0 then everything is displayed
			$list = '<ul >{list}</ul>',
			$item = '<li id="sidebar-menu-item{id}"><a href="'.$this->setting['siteURI'].'/?id={id}">{title}</a>{sublist}</li>',
			$subListMark = '',
			$first = true,
			$firstSubListMark = '',
			$firstList = '<ul id="sidebar-menu">{list}</ul>',
			$includeDisabled = false,
			$selectedItem = '<li id="side-barmenu-item{id}" class="selected"><a href="'.$this->setting['siteURI'].'/?id={id}">{title}</a>{sublist}</li>',
			$selectedItemId = $id
		);
		if ($menu != '') {
			$this->content['sidebar'] = '<div class="sidebaritem"><h4><a href="'.$this->setting['siteURI'].'/?id='.$this->rootId.'">'.
									$this->rootTitle.'</a><span class="hidden">: Outline</span></h4>'.
									$menu.
									'<hr class="hidden" /></div>';
		}
	}
	
	public function getStructure($parent,$depth,$list,$item,$subListMark='',$first=false,
							$firstSubListMark='',$firstList='',$includeDisabled=false,
							$selectedItem='',$selectedItemId = 0,$currentDepth=0,$extraItems=0) {
		$currentDepth += 1;
		if ($currentDepth > $depth && $depth > 0) {
			return '';
		}
		if ($menu = $this->db->getSubMenu($parent,$includeDisabled)) {
			$generatedMenu = '';
			foreach ($menu as $key => $menuItem) {
				$subMenu = $this->getStructure($menuItem['id'],$depth,$list,$item,$subListMark,false,'','',$includeDisabled,$selectedItem,$selectedItemId,$currentDepth);
				if ($selectedItemId != 0) {
					if ($selectedItemId == $menuItem['id']) {
						$a = str_replace('{id}',$menuItem['id'],$selectedItem);
					} else {
						$a = str_replace('{id}',$menuItem['id'],$item);
					}
				} else {
					$a = str_replace('{id}',$menuItem['id'],$item);
				}
				$a = str_replace('{title}',$menuItem['title'],$a);
				$a = str_replace('{sort}',$menuItem['sort'],$a);
				if ($subMenu != '') {
					if ($first) {
						$a = str_replace('{sublistmark}',$firstSubListMark,$a);
					} else {
						$a = str_replace('{sublistmark}',$subListMark,$a);
					}
				} else {
					$a = str_replace('{sublistmark}','',$a);
				}
				$a = str_replace('{sublist}',$subMenu,$a);
				$generatedMenu .= $a;
			}
			$currentDepth -= 1;
			if ($first) {
				return str_replace('{list}',$generatedMenu,$firstList);
			}
			return str_replace('{list}',$generatedMenu,$list);
		}
		return '';
	}
	
	private function generateSearchBox() {
		$searchBox = array (
			'form-name'	=> 'search',
			'method'	=> 'get',
			'action'	=> $this->setting['siteURI'].'/search.php',
			'textbox-name'	=> 'q',
			'search-query'	=> $this->searchQuery
		);
		$this->content['searchbox'] = $this->fillTemplate('searchbox',$searchBox);
	}
	
	private function generateFooter() {
		$this->content['footer'] = '&copy; 2010';
	}
	
	private function getTemplate($templateName='') {
		if ($templateName != '') {
			if (!isset($this->templateCache[$templateName])) {
				$this->templateCache[$templateName] = file_get_contents($this->setting['sitePath'].'/'.$this->setting['template'].'/'.$templateName.'.html');
			}
			return $this->templateCache[$templateName];
		}
		return file_get_contents($this->setting['sitePath'].'/'.$this->setting['template'].'/'.$this->templateName.'.html');
	}
	
	public function fillTemplate($templateName,$content) {
		$template = $this->getTemplate($templateName);
		foreach ($content as $key => $value) {
			$template = str_replace('{'.$key.'}',$value,$template);
		}
		return $template;
	}
	
	public function fillTemplateString($template,$content) {
		foreach ($content as $key => $value) {
			$template = str_replace('{'.$key.'}',$value,$template);
		}
		return $template;
	}
}
?>
