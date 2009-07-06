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
Ext.namespace('T3WIDGETS.twitter_###INDEX###');  

T3WIDGETS.twitter_###INDEX### = function (){ 
 	return {
 		init: function() {
			var appWidth = T3WIDGETS.settings.twitter_###INDEX###.width;
			var appHeight = T3WIDGETS.settings.twitter_###INDEX###.height;
			var reloadInterval = T3WIDGETS.settings.twitter_###INDEX###.interval;
			if (appWidth < 250) {
				appWidth = 250;
			}
			if (appHeight < 250) {
				appHeight = 250;
			}
			var msgWidth = appWidth - 185;
			
			
			
			// create the Data Store
			var store = new Ext.data.JsonStore({
				idProperty: 'id',
				root: 'results',
				fields: [
					'id',
					'text',
					'from_user',
					'from_user_id',
					'source',
					'iso_language_code',
					'to_user_id',
					'profile_image_url',
					'created_at'
				],
				proxy: new Ext.data.ScriptTagProxy({
					url: T3WIDGETS.settings.twitter_###INDEX###.url
				})
			});
			// create the grid
			var grid = new Ext.grid.GridPanel({
				store: store,
				columns: [
					{ header: "User", width: T3WIDGETS.settings.twitter_###INDEX###.imageWidth + 4, dataIndex: 'profile_image_url', sortable: false, renderer: userpic },
					{ header: "Message", width: msgWidth, dataIndex: 'text', sortable: false,renderer: theMessage },
					{ header: "at", width: 90, dataIndex: 'created_at', renderer: msgAt, sortable: false }
				],
				enableColumnHide: false,
				enableColumnMove: false,
				enableColumnResize: false,
				enableHdMenu : false,

				renderTo: T3WIDGETS.settings.twitter_###INDEX###.div,
				width: appWidth,
				height: appHeight,
				loadMask: true
			});

			function theMessage(val, p, record) {
				var txt = val+" ";
				txt =  replaceLinks(txt);
				txt =  replaceHashes(txt);
				txt =  replaceUser(txt);
				return String.format('<span class="user-from"><a href="http:\/\/www.twitter.com\/{0}" target=\"_blank\">{0}<\/a></span><br /><span class="theMessage">{1}</span>', record.data['from_user'], txt);
			}
			function userpic(val, p, record) {
				return String.format('<img src="{0}" width="70" height="70" />', val);
			}
			function msgAt(val, p, record) {
				return String.format('{0}<br />{1}', Ext.util.Format.date(val, 'd.m.y H:i'),  Ext.util.Format.htmlDecode(record.data['source']));
			}

			function replaceUser(str) {
				return str.replace(/[<=^|\s]+[@]+([\w]+)/g, function(u) {								  
					var username = u.trim().replace("@","");
					return ' <a href="http:\/\/www.twitter.com\/'+username+'" target=\"_blank\">@'+username+'<\/a>';
				});

			}
			function replaceHashes(str) {
				return str.replace(/[<=^|\s]+[#]+([\w]+)/g, function(h) {
					var hashtag = h.trim().replace("#","");
					return ' <a href="http:\/\/www.hashtags.org\/tag\/'+hashtag+'" target=\"_blank\">'+h+'<\/a>';
				});
			}
			function replaceLinks(str) {
				return str.replace(/http:\/\/(.+?)[ ]/g, function(h) {
					h = h.trim();
					return ' <a href="'+h+'" target=\"_blank\">'+h+'<\/a>';
				});
			}
			
			
			var loadingTask = {
				run: function(){
					store.reload();
				},
				interval: reloadInterval
			};
			// start the Data store load
			store.load();
			if (reloadInterval > 0) {
				Ext.TaskMgr.start(loadingTask);
			}
		}
	} 
}();


Ext.onReady(T3WIDGETS.twitter_###INDEX###.init,T3WIDGETS.twitter_###INDEX###); 