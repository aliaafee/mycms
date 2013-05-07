window.addEvent('domready',function(){
	if ($('menu')) {
		$('menu').dropMenu({
			onOpen: function(el){
				el.fade('in')
				/*el.tween('opacity', '1');*/
			},
			onClose: function(el){
				el.fade('out');
				/*el.tween('opacity', '0');*/
			},
			onInitialize: function(el){
				el.fade('hide').set('tween',{duration:250});
				/*el.set('tween', {duration: '250'});*/
			}
		});
	}
	
	if ($('search-box')) {
		label = 'Search    '
		$('search-textbox').value = label;
		$('search-textbox').addEvent('focus',function(){
			if (this.value === label){this.value = ''};
		});
		$('search-textbox').addEvent('blur',function(){
			if(this.value === ''){this.value = label};
		});
		$('search-button').addEvent('click',function(){
			if($('search-textbox').value!=label){
				document.search.submit();
			}else{
				$('search-textbox').focus();
			}
		});
	}
	
	if ($('slideshow')) {
		mySlideShow = new SlideShow('slideshow',{
			delay: 4000,
			transition: 'fade',
			duration: 500,
			autoplay: true
		});
	}
});

