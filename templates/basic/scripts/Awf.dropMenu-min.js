/*
Script: Awf.dropMenu.js
	Drop menu going Nth levels

License:
	MIT-style license.

Author:
	Copyright (c) 2008 Chris Esler, <http://www.chrisesler.com/mootools>
	Simplyfied and almost rewritten by Arian Stolwijk, <http://www.aryweb.nl>
*/

var Awf = $merge({
	DropMenu: new Class({
		version: 1.5,
		
		Implements: [Options,Events],
		
		options: {
			onOpen: function(el){
				// open the menu
				el.set('opacity',1);
			},
			onClose: function(el){
				// close the menu
				el.set('opacity',0);
			},
			onInitialize: function(el){
				// set menu to hide
				el.set('opacity',0);
			},
			mouseoutDelay: 1
		},
		
		initialize: function(menu, options, level){
			this.setOptions(options);
			
			if ($type(level) == 'number') {
				this.menu = menu; //attach menu to object
				this.fireEvent('initialize',menu);
				
				// hook up menu's parent with event to trigger menu
				this.menu.pel.addEvents({
					
					'mouseover': function(){
						// Set the DropDownOpen status to true			
						this.menu.pel.mel.store('DropDownOpen',true);
						
						// Fire the event to open the menu
						this.fireEvent('open',this.menu.pel.mel);
						
						// Clear the timer of the delay
						$clear(this.timer);
					}.bind(this),
					
					'mouseout': function(){
						// Set the DropDownOpen status to false
						this.menu.pel.mel.store('DropDownOpen',false);
						
						// Build a delay before the onClose event get fired
						this.timer = (function(){
							if(!this.menu.pel.mel.retrieve('DropDownOpen')){
								this.fireEvent('close',this.menu.pel.mel);
							}
						}).delay(this.options.mouseoutDelay,this);		
						
					}.bind(this)				
				});
			}
			else {
				level = 0;
				this.menu = $(menu);
			}
			
			// grab all of the menus children - LI's in this case		
			// loop through children
			this.menu.getChildren('li').each(function(item, index){
				var list = item.getFirst('ul'); // Should be an A tag
				// if there is a sub menu UL
				if ($type(list) == 'element') {
					item.mel = list; // pel = parent element
					list.pel = item; // mel = menu element
					new Awf.DropMenu(list, options, level + 1); // hook up the subMenu
				}
			});			
		},
		
		toElement: function(){
			return this.menu
		}
		
	})
},Awf || {});

/* So you can do like this $('nav').dropMenu(); or even $('nav').dropMenu().setStyle('border',1); */
Element.implement({
	dropMenu: function (options){
		new Awf.DropMenu(this,options);
		return this;
	}
});
