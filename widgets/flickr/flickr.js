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
Ext.namespace('T3WIDGETS.flickr_###INDEX###');  

T3WIDGETS.flickr_###INDEX### = function (){ 
 	return {
 		init: function() {
			var appWidth = T3WIDGETS.settings.flickr_###INDEX###.width;
			var appHeight = T3WIDGETS.settings.flickr_###INDEX###.height;
			if (appWidth < 250) {
				appWidth = 250;
			}
			if (appHeight < 250) {
				appHeight = 250;
			}
			
			if (T3WIDGETS.settings.flickr_###INDEX###.mode == 0) {
				var storeUrl = 'http://api.flickr.com/services/feeds/groups_pool.gne';
				var bparams = {
					id:  T3WIDGETS.settings.flickr_###INDEX###.keyword,
					lang: 'en-us',
					format: 'json'				
				};
			} else if (T3WIDGETS.settings.flickr_###INDEX###.mode == 1) {
				var storeUrl = 'http://api.flickr.com/services/feeds/photos_public.gne';
				var bparams = {
					tags:  T3WIDGETS.settings.flickr_###INDEX###.keyword,
					lang: 'en-us',
					format: 'json'				
				};
			} else {
				var storeUrl = 'http://api.flickr.com/services/feeds/photos_public.gne';
				var bparams = {
					id:  T3WIDGETS.settings.flickr_###INDEX###.keyword,
					lang: 'en-us',
					format: 'json'				
				};
			}
			
			// create the Data Store
			var store = new Ext.data.JsonStore({
				root: 'items',
				fields: [
					'title','media'
				],
				proxy: new Ext.data.ScriptTagProxy({
					url: storeUrl,
					callbackParam : "jsoncallback"
				}),
				baseParams: bparams


			});
			
			if (T3WIDGETS.settings.flickr_###INDEX###.sryle == 0) {
				var tpl = new Ext.XTemplate(
					'<tpl for=".">',
						'<div class="thumb-wrap" id="{title}">',
						'<div class="thumb"><a class="lightbox" title="{title}" href="{osrc}" target="_blank"><img src="{src}" height="{height}" title="{title}"></a></div>',
						'</div>',
					'</tpl>',
					'<div class="x-clear"></div>'
				);

				var panel = new Ext.Panel({
					renderTo: T3WIDGETS.settings.flickr_###INDEX###.div,
					frame: true,
					width: appWidth,
					autoHeight:true,
					collapsible:true,
					layout:'fit',
					title: T3WIDGETS.settings.flickr_###INDEX###.title,

					items: new Ext.DataView({
						store: store,
						tpl: tpl,
						autoHeight:true,
						overClass: 'x-view-over',
						itemSelector: 'div.thumb-wrap',
						emptyText: 'No images to display',

						prepareData: function(data){
							data.src = data.media.m; 
							data.osrc = data.src.replace('_m.jpg', '.jpg');
							data.height = T3WIDGETS.settings.flickr_###INDEX###.picHeight; 
							return data;
						},
					})
				});
				store.load();
			} else {
				
				car = new Ext.ux.Carousel(T3WIDGETS.settings.flickr_###INDEX###.div, {
					itemSelector: 'img',
					interval: 3,
					autoPlay: true,
					showPlayButton: true,
					pauseOnNavigate: true,
					freezeOnHover: true,
					transitionType: 'fade',
					navigationOnHover: true
				});
				
				
			}
			Ext.ux.Lightbox.register('a.lightbox', true);
			
			store.load();
			console.dir(store.data);
			
			
			
		}
	} 
}();


Ext.onReady(T3WIDGETS.flickr_###INDEX###.init,T3WIDGETS.flickr_###INDEX###); 