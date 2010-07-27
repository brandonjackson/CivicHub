(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('gPress4sq');

	tinymce.create('tinymce.plugins.gPress4sq', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mcegPress4sq', function() {
				ed.windowManager.open({
					file : url + '/gpress4sq.php',
					width : 420,
					height : 165,
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('gPress4sq', {
				title : 'gPress4sq.desc',
				cmd : 'mcegPress4sq',
				image : url + '/gpress4sq.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('gPress4sq', n.nodeName == 'IMG');
			});
		}, 

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'gPress Foursquare TinyMCE Plugin',
				author : 'PressBuddies',
				authorurl : 'http://pressbuddies.com',
				infourl : 'http://wordpress.org/extend/plugins/gpress/',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('gPress4sq', tinymce.plugins.gPress4sq);
})();