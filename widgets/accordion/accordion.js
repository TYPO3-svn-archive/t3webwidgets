/***************************************************************
 * extJS for TCEforms
 *
 * $Id$
 *
 * Copyright notice
 *
 * (c) 2009 Steffen Kamper <info@sk-typo3.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
Ext.namespace('T3WIDGETS.accordion');  

T3WIDGETS.accordion = function (){ 
 	return {
 		init: function() {
			var appWidth = T3WIDGETS.settings.accordion.width;
			var appHeight = T3WIDGETS.settings.accordion.height;
			if (appWidth < 250) {
				appWidth = 250;
			}
			if (appHeight < 250) {
				appHeight = 250;
			}
			var parentObj = Ext.fly(T3WIDGETS.settings.accordion.div);
			var accItems = [];
			var div;
			Ext.each(parentObj.select('h1').elements, function(el){
			   div = Ext.fly(el).next('div');
			   accItems.push({title: el.innerHTML, html: div.dom.innerHTML});
			   Ext.fly(el).remove();
			   div.remove();
			});
			
			var tabs = new Ext.Panel({
				renderTo: T3WIDGETS.settings.accordion.div,
				width: appWidth,
				height: appHeight,
				title: T3WIDGETS.settings.accordion.title,
				layout:'accordion',
				defaults: {
					bodyStyle: 'padding:15px'
				},
				layoutConfig: {
					titleCollapse: false,
					animate: true,
					activeOnTop: true
				},
			    items: accItems
			});

			

		}
	} 
}();


Ext.onReady(T3WIDGETS.accordion.init,T3WIDGETS.accordion); 