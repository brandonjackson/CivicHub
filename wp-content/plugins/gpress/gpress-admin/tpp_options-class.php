<?php
	
define('TPPO_PATH', dirname(dirname(__FILE__)) );
define('TPPO_URL', GPRESS_URL."/gpress-admin");
define('TPPO_FIELDTYPES_PATH',dirname(__FILE__).'/fieldtypes');

$notforphp4 = array( '4sq.php' );

function initialize_gpress_tppo_classes() {
	
	if ($handle = opendir(TPPO_FIELDTYPES_PATH)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle))) {
			if($file != "." && $file != ".." && !is_dir(TPPO_FIELDTYPES_PATH."/".$file)){
				if(phpversion() >= 5 || !in_array($file,$notforphp4))
					require_once(TPPO_FIELDTYPES_PATH."/".$file);
			}
		}
		closedir($handle);
	}

}
add_action( 'plugins_loaded', 'initialize_gpress_tppo_classes', 1 );

if(!class_exists('TPPOptions')){
	class TPPOptions {
		
		var $tppo, $config, $current_tppo;
		
		var $tppo_id;
		var $tppo_dbversion = '1.2';
		var $tppo_dboptionname = 'TPPO_DBVERSION';
		
		function TPPOptions($config = array()){
			global $wpdb;
			
			$wpdb->tppo = $wpdb->base_prefix . 'tppo';
			$wpdb->tppotoptabs = $wpdb->base_prefix . 'tppotoptabs';
			$wpdb->tpposubtabs = $wpdb->base_prefix . 'tpposubtabs';
			$wpdb->tppodata = $wpdb->base_prefix . 'tppodata';
			
			$currentdb = get_site_option( $this->tppo_dboptionname );
				
			if( empty($currentdb) || $currentdb < $this->tppo_dbversion ){
				$this->setupTPPODataDB();
			}			
			
			$this->set_config($config);
			
			// Upgrade data from old version of TPPO if necessary
			$this->upgrade_tppo_data();
			
			$this->initialize_tppo_config();
		}
		function set_config($config){
			$default_configs = array(
				'name' => 'tpp_options',
				'icon_url' => get_bloginfo('template_url') . '/_inc/tppo/img/tpp_icon.png',
				'tab_name' => 'TPPO Options',
				'page_title' => 'TPPO Framework',
				'debug_view' => false,
				'clear_array' => false,
				'brand_name' => 'ThePremiumPress',
				'brand_url' => 'http://thepremiumpress.com',
				'twitter_url' => 'http://twit.thepremiumpress.com',
				'facebook_url' => 'http://fb.thepremiumpress.com',
				'rss_url' => 'http://feeds.ni-limits.com/premiumpress',
				'show_brand' => true,
				'add_menu' => true
			);
			
			$this->config = array_merge($default_configs,$config);	
		}
		
		// THIS CONTROLS THE ADVANCED OPTIONAL CONFIGURATION
		function initialize_tppo_config($config = false) {
			if($config)
				$this->set_config($config);
			
			extract($this->config);

			$this->updateTPPO($this->config);
			
			if($clear_array == true) {
				$this->clearTPPoptions();
			}
			
			if($add_menu){
				// THE ACTION TO ADD THE MENU AND THE LOCATION OF THE FORM
				add_action('admin_menu', array(&$this, 'add_menu'), 10);
			}
			
			$this->tpp_options_configured = true;
		}
		// END OF CONFIGRATION SETTINGS
		
		function upgrade_tppo_data(){
			global $wpdb;
			
			$tppos = unserialize(get_site_option($this->config['name']));
		
			if(!empty($tppos)){
				
				if($tppo = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tppo} WHERE name LIKE %s",$this->config['name']))){
					$tppo_id = $tppo->id;
					$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tppo} SET meta = %s WHERE id = %d",serialize($this->config),$this->tppo_id));
					$wpdb->query("DELETE FROM {$wpdb->tppotoptabs} WHERE tppo_id = ".$tppo_id);
					$wpdb->query("DELETE FROM {$wpdb->tpposubtabs} WHERE tppo_id = ".$tppo_id);
					$wpdb->query("DELETE FROM {$wpdb->tppodata} WHERE tppo_id = ".$tppo_id);
				}else{
					$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppo} (name,meta) VALUES (%s,%s)",$this->config['name'],serialize($this->config)));
					$tppo_id = $wpdb->insert_id;
				}
				
				
				foreach(array('blogs','users','sitewide') as $type){
					foreach($tppos[$type] as $refid => $options){
							foreach($options as $option_name => $data){
								$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppodata} (tppo_id,type,refid,name,meta) VALUES (%d,%s,%d,%s,%s)",$tppo_id,$type,$refid,$option_name,serialize($data)));
							}
					}
				}
				
				foreach($tppos['top_tabs'] as $toptabid => $data){
					$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppotoptabs} (tppo_id,toptabid,meta) VALUES (%d,%d,%s)",$tppo_id,$toptabid,serialize($data)));
				}
				
				foreach($tppos['sub_tabs'] as $toptabid => $subtab){
					foreach($subtab as $subtabid => $data){
						$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tpposubtabs} (tppo_id,toptabid,subtabid,meta) VALUES (%d,%d,%d,%s)",$tppo_id,$toptabid,$subtabid,serialize($data)));
					}
				}
				
				// Backup old site option
				delete_site_option($this->config['name']);
				update_site_option($this->config['name']."_deprecated",serialize($tppos));
			}
		}
		
		
		function setupTPPODataDB(){
			global $wpdb;
			
			$table_name = $wpdb->tppo;
			$sql = "CREATE TABLE " . $table_name . " (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						name varchar(50) NOT NULL,
						meta longtext NULL,
						PRIMARY KEY (id),
						UNIQUE uk_" . $table_name . "_id (id)
				   );";
			$this->dbDelta($sql);
			
			$table_name = $wpdb->tppotoptabs;
			$sql = "CREATE TABLE " . $table_name . " (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						tppo_id mediumint(9) NOT NULL,
						toptabid mediumint(9) NOT NULL,
						meta longtext NULL,
						PRIMARY KEY (id),
						UNIQUE uk_" . $table_name . "_id (id)
				   );";
			$this->dbDelta($sql);
			
			$table_name = $wpdb->tpposubtabs;
			$sql = "CREATE TABLE " . $table_name . " (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						tppo_id mediumint(9) NOT NULL,
						toptabid mediumint(9) NOT NULL,
						subtabid mediumint(9) NOT NULL,
						meta longtext NULL,
						PRIMARY KEY (id),
						UNIQUE uk_" . $table_name . "_id (id)
				   );";
			$this->dbDelta($sql);
			
			$table_name = $wpdb->tppodata;
			$sql = "CREATE TABLE " . $table_name . " (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						tppo_id mediumint(9) NOT NULL,
						type varchar(15) NOT NULL,
						refid mediumint(9) NOT NULL,
						name varchar(100) NOT NULL,
						meta longtext NULL,
						PRIMARY KEY (id),
						UNIQUE uk_" . $table_name . "_id (id)
				   );";
			$this->dbDelta($sql);
			
			update_site_option( $this->tppo_dboptionname, $this->tppo_dbversion);
		}
		
		
		// NEW CUSTOM FUNCTION TO PUT THEME OPTIONS TO TOP OF SIDEBAR
		function add_menu_page_custom( $page_title, $menu_title, $access_level, $file, $function = '', $icon_url, $pos ) {
			
			global $menu, $admin_page_hooks, $_registered_pages;
		
			$file = plugin_basename( $file );
		
			$admin_page_hooks[$file] = sanitize_title( $menu_title );
		
			$hookname = get_plugin_page_hookname( $file, '' );
			if (!empty ( $function ) && !empty ( $hookname ))
				add_action( $hookname, $function );
		
				/* THESE TWO LINES PUT THE MENU AT VERY TOP */
				//$menu[$pos-1] = array ('','read','separator2','','wp-menu-separator');
				//$menu[$pos] = array ( $menu_title, $access_level, $file, $page_title, 'menu-top menu-top-first ' . $hookname, $hookname, $icon_url );
				
				$menu[55] = array ('','read','separator2','','wp-menu-separator');
				$menu[56] = array ( $menu_title, $access_level, $file, $page_title, 'menu-top menu-top-first ' . $hookname, $hookname, $icon_url );
			
			$_registered_pages[$hookname] = true;
		
			return $hookname;
		}
		
		// THE MENU TO BE ADDED TO THE SIDEBAR
		function add_menu() {
			global $wp_version;
			if ( $wp_version >= 2 ) {
				$level = 'edit_themes';
			} else {
				$level = 10;
			}
			
			$this->add_menu_page_custom($this->config['page_title'], $this->config['tab_name'], 'administrator', 'tpp_options_form', array(&$this, 'tppo_form'), $this->config['icon_url'], -1 );
		}
		
		function tppo_form() {
			require_once( dirname(__FILE__) . '/tpp_options_form.php' );
		}
		
		// USED TO ASCERTAIN THE REQUIRED BLOG AND USER IDs
		function check_blog_id() {
			global $blog_id;
			if(empty($blog_id)) {
				$blog_id = (defined( 'BLOGID_CURRENT_SITE' ) ? constant('BLOGID_CURRENT_SITE') : 1);
			} else {
				$blog_id = $blog_id;
			}
			$tpp_blog_id = $blog_id;
			return $tpp_blog_id;
		}
		
		function check_user_id() {
			if(function_exists("get_current_user_id")){
				return get_current_user_id();
			}else{
				return 1;
			}
		}
		
		/** THE ACTIONS TO ADD THE OPTIONS AND DEFAULT VALUES TO THE ARRAY
		 *
		 *	Available Params
		 *  ================
		 *  - tpp_option_name		(string or array) Note : If array is detected, will automatically assign variables according to key-value pairs 
		 *  - tpp_option_type		(string)
		 *  - tpp_top_tab			(number)
		 *  - tpp_sub_tab			(number)
		 *  - tpp_field_order		(number)
		 *  - tpp_field_type		(string)
		 *  - tpp_field_label		(string)
		 *  - tpp_field_description	(string)
		 *  - tpp_default_value		(string)
		 *  - tpp_linked_options	(array)
		 *  - display				(boolean)
		 *  - empty_value 			(boolean or string) Note : False - leave value as it is, True - use $tpp_default_value if value is empty, String - set value to $empty_value if value is empty
		 **/
		function add_option($tpp_option_name, $tpp_option_type='', $tpp_top_tab=0, $tpp_sub_tab=0, $tpp_field_order=0, $tpp_field_type='', $tpp_field_label='', $tpp_field_description='', $tpp_default_value = "", $tpp_linked_options = false, $display=true, $empty_value=false) {
			
			// if 1st variable is array, use array style variable
			if(is_array($tpp_option_name)){
				extract($tpp_option_name);	
			}
			
			$tpp_blog_id = $this->check_blog_id();
			$tpp_user_id = $this->check_user_id();
			
			if($tpp_option_type == "blogs") {
				$tpp_id = $tpp_blog_id;
			}elseif($tpp_option_type == "users") {
				$tpp_id = $tpp_user_id;
			}elseif($tpp_option_type == "sitewide") {
				$tpp_id = (defined( 'SITE_ID_CURRENT_SITE' ) ? constant('SITE_ID_CURRENT_SITE') : 1);
			}
			
			if(isset($tpp_id)){
				global $wpdb;
				
				$data = new stdClass();
				if($exist = $this->existsTPPOdata($tpp_option_type,$tpp_id,$tpp_option_name)){
					$meta = unserialize($exist->meta);
					$data->value = $meta->value;
				}else{
					$data->value = $tpp_default_value;	
				}
				$data->default_value = $tpp_default_value;
				$data->empty_value = $empty_value;
				$data->option_name = $tpp_option_name;
				$data->option_type = $tpp_option_type;
				$data->top_tab = $tpp_top_tab;
				$data->sub_tab = $tpp_sub_tab;
				$data->field_type = $tpp_field_type;
				$data->field_label = $tpp_field_label;
				$data->field_description = $tpp_field_description;
				$data->field_order = $tpp_field_order;
				$data->linked_options = $tpp_linked_options;
			}
			
			// Add tppodata
			$this->updateTPPOdata($data,$tpp_option_type,$tpp_id,$tpp_option_name);
			
			// Set subtab's is_empty to false
			$this->updateTPPOSubTabs(array('is_empty'=>false),$tpp_top_tab,$tpp_sub_tab);
			
			if(!isset($this->current_tppo[$tpp_option_type]))
				$this->current_tppo[$tpp_option_type] = array();
			if(!isset($this->current_tppo['sub_tabs_fields']))
				$this->current_tppo['sub_tabs_fields'] = array();
			if(!isset($this->current_tppo['sub_tabs_fields'][$tpp_top_tab]))
				$this->current_tppo['sub_tabs_fields'][$tpp_top_tab] = array();
			if(!isset($this->current_tppo['sub_tabs_fields'][$tpp_top_tab][$tpp_sub_tab]))
				$this->current_tppo['sub_tabs_fields'][$tpp_top_tab][$tpp_sub_tab] = array();
			if(!isset($this->current_tppo['fields_display']))
				$this->current_tppo['fields_display'] = array();
			
			$this->current_tppo[$tpp_option_type][] = $tpp_option_name;
			$this->current_tppo['sub_tabs_fields'][$tpp_top_tab][$tpp_sub_tab][] = $tpp_option_name;
			$this->current_tppo['fields_display'][$tpp_option_name] = $display;
			
			$this->updateCurrentTPPoptions($this->current_tppo);
		}
		
		// USED TO NAME THE TOP TABS
		function add_tab($toptab, $label, $display=true) {
			
			$data = new stdClass();
			$data->label = $label;
			$data->is_empty = true;
			$data->display = $display;
			
			// Add toptab data
			$this->updateTPPOTopTabs($data,$toptab);
			
			if(!isset($this->current_tppo['top_tabs']))
				$this->current_tppo['top_tabs'] = array();
			$this->current_tppo['top_tabs'][] = $toptab;
			$this->updateCurrentTPPoptions($this->current_tppo);
		}
		
		// USED TO NAME THE SUB TABS
		function add_sub_tab($toptab, $subtab, $label, $desc = '', $display=true) {
			
			$data = new stdClass();
			$data->label = $label;
			$data->top_tab = $toptab;
			$data->sub_tab = $subtab;
			$data->is_empty = true;
			$data->description = $desc;
			$data->display = $display;
			
			if(!isset($this->current_tppo['sub_tabs']))
				$this->current_tppo['sub_tabs'] = array();
			if(!isset($this->current_tppo['sub_tabs'][$toptab]))
				$this->current_tppo['sub_tabs'][$toptab] = array();				
			$this->current_tppo['sub_tabs'][$toptab][] = $subtab;
			$this->updateCurrentTPPoptions($this->current_tppo);
			
			$this->updateTPPOSubTabs($data,$toptab,$subtab);
			$this->updateTPPOTopTabs(array('is_empty'=>false),$toptab);
		}
		
		// USED TO CONSTRUCT THE DEBUG VIEW
		function view_options_array() {
			
			echo '<div class="debug_div"><span><span class="main_header">Debug-Mode has been Activated</span>';
			
			echo '<span class="header">The present BLOG_ID now in view = '.$this->check_blog_id().'</span>';
			echo '<span class="header">The present USER_ID now in view = '.$this->check_user_id().'</span>';
		
			echo '<div class="pre_pre"><pre>';
				print_r($this->tppo);
			echo '</pre></div>';
			
			echo '<span class="spacer"></div></div>';
			
		}
		
		// Helper function to check current fields settings
		function getCurrentTPPoptions(){
			return maybe_unserialize(get_site_option($this->config['name']."_current"));
		}
		
		function getTPPoptions($fordisplay = false,$justvalue = false,$reload = false){
			global $wpdb;
			
			if($justvalue){
				
				$data->option = array();
				
				$tpp_blog_id = $this->check_blog_id();
				$tpp_user_id = $this->check_user_id();
				
				$tppodata = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->tppodata} WHERE tppo_id = %d AND 
										   		(	( type LIKE 'blogs' AND refid = %d )
												OR	( type LIKE 'users' AND refid = %d )
												OR 	( type LIKE 'sitewide' AND refid = %d ) )", $this->tppo_id, $tpp_blog_id, $tpp_user_id, (defined( 'BLOGID_CURRENT_SITE' ) ? constant('BLOGID_CURRENT_SITE') : 1)));
				
				foreach($tppodata as $thisoption){
					$meta = unserialize($thisoption->meta);
					$data->option[$thisoption->name] = $meta->value;
				}
				
			}else{
				if($reload){
					$default_types = array(
											'blogs' => array(),
											'users' => array(),
											'sitewide'  => array(),
											'top_tabs'  => array(),
											'sub_tabs'  => array()
									);
					
					$data = array();
					
					// Handles blogs, users & sitewide
					$alldata = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->tppodata} WHERE tppo_id = %d", $this->tppo_id));
					foreach($alldata as $thisdata){
						if(!isset($data[$thisdata->type]))
							$data[$thisdata->type] = array();
						if(!isset($data[$thisdata->type][$thisdata->refid]))
							$data[$thisdata->type][$thisdata->refid] = array();
							
						$data[$thisdata->type][$thisdata->refid][$thisdata->name] = unserialize($thisdata->meta);
					}
					
					// Handles toptabs
					$toptabs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->tppotoptabs} WHERE tppo_id = %d", $this->tppo_id));
					foreach($toptabs as $thistoptab){
						if(!isset($data['top_tabs']))
							$data['top_tabs'] = array();
						
						$data['top_tabs'][$thistoptab->toptabid] = unserialize($thistoptab->meta);
					}
					
					// Handles subtabs
					$subtabs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->tpposubtabs} WHERE tppo_id = %d", $this->tppo_id));
					foreach($subtabs as $thissubtab){
						if(!isset($data['sub_tabs']))
							$data['sub_tabs'] = array();
						if(!isset($data['sub_tabs'][$thissubtab->toptabid]))
							$data['sub_tabs'][$thissubtab->toptabid] = array();
						
						$data['sub_tabs'][$thissubtab->toptabid][$thissubtab->subtabid] = unserialize($thissubtab->meta);
					}
					
					$data = array_merge($default_types,$data);
					
					if($fordisplay){
						// Cleans $data for display
						$this->cleanDataForDisplay(&$data);
					}
				}else{
					// Use data during initialize
					$data = $this->tppo;	
					
					if($fordisplay){
						// Cleans $data for display
						$this->cleanDataForDisplay(&$data);
					}
				}
			}	
			
			return $data;
		}
		
		function cleanDataForDisplay($data){
			
			// Check and handles sitewide fields
			if(function_exists("is_site_admin")){
				if(!is_site_admin()){
					$data['sitewide'][(defined( 'BLOGID_CURRENT_SITE' ) ? constant('BLOGID_CURRENT_SITE') : 1)] = array();
				}
			}
			
			$tpp_blog_id = $this->check_blog_id();
			$tpp_user_id = $this->check_user_id();
		
			$this->tppoptions_fields = array_merge(	array('blogs' => $data['blogs'][$tpp_blog_id]),
												array('users' => $data['users'][$tpp_user_id]),
												array('sitewide' => $data['sitewide'][(defined( 'BLOGID_CURRENT_SITE' ) ? constant('BLOGID_CURRENT_SITE') : 1)]));
			
			if(is_array($data['top_tabs'])){
				foreach ($data['top_tabs'] as $this_top_tab_name => $this_top_tab) {
					if(!$this_top_tab->display){
						unset($data['top_tabs'][$this_top_tab_name]);
						unset($data['sub_tabs'][$this_top_tab_name]);
						continue;
					}
					if(is_array($data['sub_tabs'][$this_top_tab_name])){
						foreach ($data['sub_tabs'][$this_top_tab_name] as $this_sub_tab_name => $this_sub_tab) {
							if(!$this_sub_tab->display){
								unset($data['sub_tabs'][$this_top_tab_name][$this_sub_tab_name]);
								if(empty($data['sub_tabs'][$this_top_tab_name])){
									unset($data['top_tabs'][$this_top_tab_name]);
								}
								continue;
							}
							$data['top_tabs'][$this_top_tab_name]->is_empty = true;
							$data['sub_tabs'][$this_top_tab_name][$this_sub_tab_name]->is_empty = true;
						}
					}
					ksort($data['sub_tabs'][$this_top_tab_name]);
				}
				ksort($data['top_tabs']);	
			}
			
			$this->current_tppo = $this->getCurrentTPPoptions();
			
			if(is_array($this->tppoptions_fields)){
				foreach ($this->tppoptions_fields as $tpp_option_ar) {
					if(is_array($tpp_option_ar)){
						foreach($tpp_option_ar as $thisfield){
							if(!$this->current_tppo['fields_display'][$thisfield->option_name]){
								unset($data['sub_tabs'][$thisfield->top_tab][$thisfield->sub_tab]->fields[$thisfield->option_name]);
									
								if(empty($data['sub_tabs'][$thisfield->top_tab][$thisfield->sub_tab]->fields)){
									unset($data['sub_tabs'][$thisfield->top_tab][$thisfield->sub_tab]);
								}
								if(empty($data['sub_tabs'][$thisfield->top_tab])){
									unset($data['top_tabs'][$thisfield->top_tab]);
								}
								continue;	
							}
							if($data['top_tabs'][$thisfield->top_tab]->display){
								$data['top_tabs'][$thisfield->top_tab]->is_empty = false;
							}						
							if($data['sub_tabs'][$thisfield->top_tab][$thisfield->sub_tab]->display){
								$data['sub_tabs'][$thisfield->top_tab][$thisfield->sub_tab]->is_empty = false;
							}
						}
					}
				}
			}
		}
		
		// Update or Create TPPO instance
		function updateTPPO($config){
			global $wpdb;
			
			if($tppo = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tppo} WHERE name LIKE %s",$config['name']))){
				$this->tppo_id = $tppo->id;
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tppo} SET meta = %s WHERE id = %d",serialize($config),$this->tppo_id));
			}else{
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppo} (name,meta) VALUES (%s,%s)",$config['name'],serialize($config)));
				$this->tppo_id = $wpdb->insert_id;
			}
		}
		
		// Update or Create Top Tabs
		function updateTPPOTopTabs($data,$toptab){
			global $wpdb;
			
			$data = (object)$data;
			
			if($thetoptab = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tppotoptabs} WHERE toptabid = %d AND tppo_id = %d",$toptab,$this->tppo_id)) ){
				$ori_data = unserialize($thetoptab->meta);
				$data = (object)array_merge((array)$ori_data, (array)$data);
				
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tppotoptabs} SET meta = %s WHERE id = %d",serialize($data),$thetoptab->id));
			}else{
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppotoptabs} (tppo_id,toptabid,meta) VALUES (%d,%d,%s)",$this->tppo_id,$toptab,serialize($data)));
			}
			
			if(!isset($this->tppo['top_tabs']))
				$this->tppo['top_tabs'] = array();
				
			$this->tppo['top_tabs'][$toptab] = $data;
		}
		function deleteTPPOTopTabs($toptab){
			global $wpdb;
			$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->tppotoptabs} WHERE toptabid = %d AND tppo_id = %d",$toptab,$this->tppo_id));
			unset($this->tppo['top_tabs'][$toptab]);
		}
		
		
		// Update or Create Sub Tabs
		function updateTPPOSubTabs($data,$toptab,$subtab){
			global $wpdb;
			
			$data = (object)$data;
				
			if($thesubtab = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tpposubtabs} WHERE toptabid = %d AND subtabid = %d AND tppo_id = %d",$toptab,$subtab,$this->tppo_id)) ){
				$ori_data = unserialize($thesubtab->meta);
				
				if(isset($data->fields)){
					$data->fields = array_merge((array)$ori_data->fields, (array)$data->fields);
				}
				$data = (object)array_merge((array)$ori_data, (array)$data);
				
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tpposubtabs} SET meta = %s WHERE id = %d",serialize($data),$thesubtab->id));
			}else{
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tpposubtabs} (tppo_id,toptabid,subtabid,meta) VALUES (%d,%d,%d,%s)",$this->tppo_id,$toptab,$subtab,serialize($data)));
			}
			
			if(!isset($this->tppo['sub_tabs']))
				$this->tppo['sub_tabs'] = array();
			if(!isset($this->tppo['sub_tabs'][$toptab]))
				$this->tppo['sub_tabs'][$toptab] = array();
			$this->tppo['sub_tabs'][$toptab][$subtab] = $data;
		}
		
		function deleteTPPOSubTabs($toptab,$subtab){
			global $wpdb;
			$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->tpposubtabs} WHERE toptabid = %d AND subtabid = %d AND tppo_id = %d",$toptab,$subtab,$this->tppo_id));
			unset($this->tppo['sub_tabs'][$toptab][$subtab]);
		}
		
		function deleteTPPOSubTabsFields($toptab,$subtab,$fieldname){
			global $wpdb;
			$thesubtab = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tpposubtabs} WHERE toptabid = %d AND subtabid = %d AND tppo_id = %d",$toptab,$subtab,$this->tppo_id));
			$data = unserialize($thesubtab->meta);
			unset($data->fields[$fieldname]);
			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tpposubtabs} SET meta = %s WHERE id = %d",serialize($data),$thesubtab->id));
			
			unset($this->tppo['sub_tabs'][$toptab][$subtab]->fields[$fieldname]);
		}
		
		
		function existsTPPOdata($type,$refid,$name){
			global $wpdb;
			return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tppodata} WHERE type = %s AND refid = %d AND name = %s AND tppo_id = %d",$type,$refid,$name,$this->tppo_id));
		}
	
		// Update or Create TPPO option data
		function updateTPPOdata($data,$type,$refid,$name){
			global $wpdb;
			
			$data = (object)$data;
			if( $theoption = $this->existsTPPOdata($type,$refid,$name) ){
				$ori_data = unserialize($theoption->meta);
				$data = (object)array_merge((array)$ori_data, (array)$data);
				
				if(empty($data->value)){
					if($data->empty_value === true){
						$data->value = $data->default_value;
					}else if($data->empty_value === false){
						// Do nothing	
					}else{
						$data->value = $data->empty_value;
					}					
				}
				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->tppodata} SET meta = %s WHERE id = %d",serialize($data),$theoption->id));
			}else{
				if(empty($data->value)){
					if($data->empty_value === true){
						$data->value = $data->default_value;
					}else if($data->empty_value === false){
						// Do nothing	
					}else{
						$data->value = $data->empty_value;
					}						
				}
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->tppodata} (tppo_id,type,refid,name,meta) VALUES (%d,%s,%d,%s,%s)",$this->tppo_id,$type,$refid,$name,serialize($data)));
			}
			
			if(!isset($this->tppo[$type]))
				$this->tppo[$type] = array();
			if(!isset($this->tppo[$type][$refid]))
				$this->tppo[$type][$refid] = array();
			$this->tppo[$type][$refid][$name] = $data;
			
			$this->updateTPPOSubTabs(array('fields'=> array($name => $data)),$data->top_tab,$data->sub_tab);
			
		}
		function deleteTPPOdata($type,$refid,$name){
			global $wpdb;
			$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->tppodata} WHERE type = %s AND refid = %d AND name = %s AND tppo_id = %d",$type,$refid,$name,$this->tppo_id));
			unset($this->tppo[$type][$refid][$name]);
		}
		
		function clearTPPoptions(){
			global $wpdb;
			$wpdb->query($wpdb->prepare("DELETE {$wpdb->tppotoptabs} WHERE tppo_id = %d",$this->tppo_id));
			$wpdb->query($wpdb->prepare("DELETE {$wpdb->tpposubtabs} WHERE tppo_id = %d",$this->tppo_id));
			$wpdb->query($wpdb->prepare("DELETE {$wpdb->tppodata} WHERE tppo_id = %d",$this->tppo_id));
		}
		
		function updateCurrentTPPoptions($data){
			update_site_option($this->config['name']."_current",maybe_serialize($data));	
		}
		
		function consolidate_options_db(){
			
			foreach($this->tppo['top_tabs'] as $toptab => $value){
				if(!is_array($this->current_tppo['top_tabs']) || array_search($toptab,$this->current_tppo['top_tabs']) === false){
					$this->deleteTPPOTopTabs($toptab);
				}
			}
			
			foreach($this->tppo['sub_tabs'] as $toptab => $this_subtab){
				foreach($this_subtab as $subtab => $this->tppo_ar){
					
					if(!is_array($this->current_tppo['sub_tabs']) || !isset($this->current_tppo['sub_tabs'][$toptab]) || array_search($subtab,$this->current_tppo['sub_tabs'][$toptab]) === false){
						$this->deleteTPPOSubTabs($toptab,$subtab);
					}else{
						
						foreach($this->tppo['sub_tabs'][$toptab][$subtab]->fields as $ind => $thisfield){
							if(!is_array($this->current_tppo[$thisfield->option_type]) || array_search($thisfield->option_name,$this->current_tppo[$thisfield->option_type]) === false || array_search($thisfield->option_name,$this->current_tppo['sub_tabs_fields'][$toptab][$subtab]) === false){
								$this->deleteTPPOSubTabsFields($toptab,$subtab,$ind);
							}
						}
						uasort($this->tppo['sub_tabs'][$toptab][$subtab]->fields, "tpp_sort_options");
					}
				}
			}
								
			foreach(array('blogs','users','sitewide') as $option_type){
				if(!empty($this->tppo[$option_type]))
				foreach($this->tppo[$option_type] as $refid => $tppo_options_ar){
					foreach($tppo_options_ar as $field_name => $value){
						if(!is_array($this->current_tppo[$option_type]) || array_search($field_name,$this->current_tppo[$option_type]) === false){
							$this->deleteTPPOdata($option_type,$refid,$field_name);
						}
					}
				}
			}
		}
		
		function get_tppo($field_name, $option_type = '', $tpp_id = 0, $key = 'value', $echo = false) {
			
			if(empty($option_type)){
				foreach(array('blogs','users','sitewide') as $option_type){
					if(!is_array($this->current_tppo[$option_type]) || array_search($field_name,$this->current_tppo[$option_type]) !== false){
						break;
					}
				}
			}
			
			if(empty($option_type))
				return "";
				
			if ($tpp_id === 0) {
				$tpp_blog_id = $this->check_blog_id();
				$tpp_user_id = $this->check_user_id();		
				if($option_type == "blogs") {
					$tpp_id = $tpp_blog_id;
				}elseif($option_type == "users") {
					$tpp_id = $tpp_user_id;
				}elseif($option_type == "sitewide") {
					$tpp_id = (defined( 'SITE_ID_CURRENT_SITE' ) ? constant('SITE_ID_CURRENT_SITE') : 1);
				}
			} else {
				$tpp_id = $tpp_id;
			}
			
			// If called after all data initialize, use $this->tppo
			if( isset($this->tppo[$option_type]) && isset($this->tppo[$option_type][$tpp_id]) && isset($this->tppo[$option_type][$tpp_id][$field_name]) ){
				$value = $this->tppo[$option_type][$tpp_id][$field_name]->$key;
			
			// If called before all data initialize, use value from database
			}else{
				global $wpdb;
				
				$optiondata = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->tppodata} WHERE type = %s AND refid = %d AND name = %s AND tppo_id = %d",$option_type,$tpp_id,$field_name,$this->tppo_id));
				$data = unserialize($optiondata->meta);
				$value = $data->value;
			}
			
			if($echo)
				echo $value;
			else
				return $value;
		}
		
		// DB update function extracted from /wp-admin/includes/upgrade.php
		function dbDelta($queries, $execute = true) {
			global $wpdb;
		
			// Separate individual queries into an array
			if( !is_array($queries) ) {
				$queries = explode( ';', $queries );
				if('' == $queries[count($queries) - 1]) array_pop($queries);
			}
		
			$cqueries = array(); // Creation Queries
			$iqueries = array(); // Insertion Queries
			$for_update = array();
		
			// Create a tablename index for an array ($cqueries) of queries
			foreach($queries as $qry) {
				if(preg_match("|CREATE TABLE (?:IF NOT EXISTS )?([^ ]*)|", $qry, $matches)) {
					$cqueries[trim( strtolower($matches[1]), '`' )] = $qry;
					$for_update[$matches[1]] = 'Created table '.$matches[1];
				}
				else if(preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
					array_unshift($cqueries, $qry);
				}
				else if(preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
					$iqueries[] = $qry;
				}
				else if(preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
					$iqueries[] = $qry;
				}
				else {
					// Unrecognized query type
				}
			}
		
			// Check to see which tables and fields exist
			if($tables = $wpdb->get_col('SHOW TABLES;')) {
				// For every table in the database
				foreach($tables as $table) {
					// If a table query exists for the database table...
					if( array_key_exists(strtolower($table), $cqueries) ) {
						// Clear the field and index arrays
						unset($cfields);
						unset($indices);
						// Get all of the field names in the query from between the parens
						preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
						$qryline = trim($match2[1]);
		
						// Separate field lines into an array
						$flds = explode("\n", $qryline);
		
						//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($cqueries, true)."</pre><hr/>";
		
						// For every field line specified in the query
						foreach($flds as $fld) {
							// Extract the field name
							preg_match("|^([^ ]*)|", trim($fld), $fvals);
							$fieldname = trim( $fvals[1], '`' );
		
							// Verify the found field name
							$validfield = true;
							switch(strtolower($fieldname))
							{
							case '':
							case 'primary':
							case 'index':

							case 'fulltext':
							case 'unique':
							case 'key':
								$validfield = false;
								$indices[] = trim(trim($fld), ", \n");
								break;
							}
							$fld = trim($fld);
		
							// If it's a valid field, add it to the field array
							if($validfield) {
								$cfields[strtolower($fieldname)] = trim($fld, ", \n");
							}
						}
		
						// Fetch the table column structure from the database
						$tablefields = $wpdb->get_results("DESCRIBE {$table};");
		
						// For every field in the table
						foreach($tablefields as $tablefield) {
							// If the table field exists in the field array...
							if(array_key_exists(strtolower($tablefield->Field), $cfields)) {
								// Get the field type from the query
								preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
								$fieldtype = $matches[1];
		
								// Is actual field type different from the field type in query?
								if($tablefield->Type != $fieldtype) {
									// Add a query to change the column type
									$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
									$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
								}
		
								// Get the default value from the array
									//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
								if(preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
									$default_value = $matches[1];
									if($tablefield->Default != $default_value)
									{
										// Add a query to change the column's default value
										$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
										$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
									}
								}
		
								// Remove the field from the array (so it's not added)
								unset($cfields[strtolower($tablefield->Field)]);
							}
							else {
								// This field exists in the table, but not in the creation queries?
							}
						}
		
						// For every remaining field specified for the table
						foreach($cfields as $fieldname => $fielddef) {
							// Push a query line into $cqueries that adds the field to that table
							$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
							$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
						}
		
						// Index stuff goes here
						// Fetch the table index structure from the database
						$tableindices = $wpdb->get_results("SHOW INDEX FROM {$table};");
		
						if($tableindices) {
							// Clear the index array
							unset($index_ary);
		
							// For every index in the table
							foreach($tableindices as $tableindex) {
								// Add the index to the index data array
								$keyname = $tableindex->Key_name;
								$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
								$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
							}
		
							// For each actual index in the index array
							foreach($index_ary as $index_name => $index_data) {
								// Build a create string to compare to the query
								$index_string = '';
								if($index_name == 'PRIMARY') {
									$index_string .= 'PRIMARY ';
								}
								else if($index_data['unique']) {
									$index_string .= 'UNIQUE ';
								}
								$index_string .= 'KEY ';
								if($index_name != 'PRIMARY') {
									$index_string .= $index_name;
								}
								$index_columns = '';
								// For each column in the index
								foreach($index_data['columns'] as $column_data) {
									if($index_columns != '') $index_columns .= ',';
									// Add the field to the column list string
									$index_columns .= $column_data['fieldname'];
									if($column_data['subpart'] != '') {
										$index_columns .= '('.$column_data['subpart'].')';
									}
								}
								// Add the column list to the index create string
								$index_string .= ' ('.$index_columns.')';
								if(!(($aindex = array_search($index_string, $indices)) === false)) {
									unset($indices[$aindex]);
									//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
								}
								//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br /><b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
							}
						}
		
						// For every remaining index specified for the table
						foreach ( (array) $indices as $index ) {
							// Push a query line into $cqueries that adds the index to that table
							$cqueries[] = "ALTER TABLE {$table} ADD $index";
							$for_update[$table.'.'.$fieldname] = 'Added index '.$table.' '.$index;
						}
		
						// Remove the original table creation query from processing
						unset($cqueries[strtolower($table)]);
						unset($for_update[strtolower($table)]);
					} else {
						// This table exists in the database, but not in the creation queries?
					}
				}
			}
		
			$allqueries = array_merge($cqueries, $iqueries);
			if($execute) {
				foreach($allqueries as $query) {
					//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
					$wpdb->query($query);
				}
			}
		
			return $for_update;
		}
		
		
	}


	// START HELPER FUNCTIONS
	
	function tpp_sort_options($a, $b){
		return ($a->field_order < $b->field_order) ? -1 : 1;
	}


	function update_tpp_options($obj) {
		
		do_action('before_update_tpp_options',&$obj->tppo);

		if(is_array($_POST['tppo'])){
			foreach($_POST['tppo'] as $option_type => $tpp_id_ar){
				if(is_array($tpp_id_ar)){
					foreach($tpp_id_ar as $tpp_id => $field_ar){
						if(is_array($field_ar)){
							foreach($field_ar as $field_name => $value){
								$obj->updateTPPOdata(array('value' => $value),$option_type,$tpp_id,$field_name);
							}
						}
					}
				}
			}
		}
		
		do_action('after_update_tpp_options',&$obj->tppo);
		
	}


	function tpp_output_field($thisfield,$obj,$value=NULL){
		
		$tpp_blog_id = $obj->check_blog_id();
		$tpp_user_id = $obj->check_user_id();
		
		if($thisfield->option_type == "blogs") {
			$tpp_id = $tpp_blog_id;
		}elseif($thisfield->option_type == "users") {
			$tpp_id = $tpp_user_id;
		}elseif($thisfield->option_type == "sitewide") {
			$tpp_id = 1;
		}
		
		if($value !== NULL){
			$thisfield->value = $value;	
		}
		
		// START OF TEXT INPUT
		if($thisfield->field_type == "text_input") {
		  
		  ?>
			<li>
				<label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
				
				<div class="general-input">
					<input name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>]" type="text" value="<?php echo $thisfield->value; ?>" />
					<span class="help-text"><?php echo $thisfield->field_description; ?></span>                                        
				</div>
				
				<div class="clear"></div>
			</li>
			<?php
		}
		// END OF TEXT INPUT
		
		// START OF TEXT AREA
		else if($thisfield->field_type == "text_area") {
		  
		  ?>
			  <li>
				  <label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
				  
				  <div class="general-input">
					  <textarea name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>]" cols="" rows=""><?php echo $thisfield->value; ?></textarea>
					  <span class="help-text"><?php echo $thisfield->field_description; ?></span>                                        
				  </div>
				  
				  <div class="clear"></div>
			  </li>
		  <?php
		}
		// END OF TEXT AREA
		
		// START OF RADIO BUTTONS
		else if($thisfield->field_type == "radio_button") {
			?>
		  <li>
			  <label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
			  
			  <div class="general-input" title="<?php echo $thisfield->field_description; ?>" >
				<?php
					foreach ($thisfield->linked_options as $option_value => $option_label) {
				?>
					  <input type="radio" alt="<?php echo $thisfield->option_name; ?>_cb" name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>]" id="radio_<?php echo $thisfield->option_type; ?>_<?php echo $tpp_id; ?>_<?php echo $thisfield->option_name; ?>_<?php echo $option_value; ?>" value="<?php echo $option_value; ?>" onfocus="if(this.blur)this.blur()" <?php if($thisfield->value == $option_value) echo "checked"; ?> />
					  <label for="radio_<?php echo $thisfield->option_type; ?>_<?php echo $tpp_id; ?>_<?php echo $thisfield->option_name; ?>_<?php echo $option_value; ?>"><?php echo $option_label; ?></label>
				<?php
					}
				?>
		  
			  </div>
			  
			  <div class="clear"></div>
		  </li>
		<?php
			}
		// END OF RADIO BUTTONS
		
		// START OF DROP DOWN
		else if($thisfield->field_type == "drop_down") {
		  
		?>
		
		  <li>
			  <label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
			  
			  <div class="general-input">
				  <select class="tpp_dropdown" name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>]">
				  <?php
					foreach ($thisfield->linked_options as $option_value => $option_label) {
				  
						echo'
						<option value="'.$option_value.'"';
						
						if($thisfield->value == $option_value) {
							echo ' selected="selected"';
						}
						
						echo '>'.$option_label.'</option>';
					
					}
					?>
				  </select>
				  <span class="help-text"><?php echo $thisfield->field_description; ?></span> 
				  <div class="clear"></div>
			  </div>
			  
			  <div class="clear"></div>
		  </li>
		<?php
		}
		// END OF DROP DOWN
		
		// START OF CHECK BOXES
		else if($thisfield->field_type == "check_box") {
		  
		  ?>
		  <li>
			  <label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
			  <input type="hidden" name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>]" value="" />
			  <div class="general-input" title="<?php echo $thisfield->field_description; ?>">
				<?php
					foreach ($thisfield->linked_options as $option_value => $option_label) {
				?>
					  <input type="checkbox" name="tppo[<?php echo $thisfield->option_type; ?>][<?php echo $tpp_id; ?>][<?php echo $thisfield->option_name; ?>][]" id="check_<?php echo $thisfield->option_type; ?>_<?php echo $tpp_id; ?>_<?php echo $thisfield->option_name; ?>_<?php echo $option_value; ?>" value="<?php echo $option_value; ?>"
						  
						  <?php if(is_array($thisfield->value)) {
							 $temp_array = $thisfield->value;
							 foreach ($temp_array as $key => $value) {
								  if($value == $option_value) {
									  echo ' checked';
								  }
							  }
						  }
							?>/>
						  <label for="check_<?php echo $thisfield->option_type; ?>_<?php echo $tpp_id; ?>_<?php echo $thisfield->option_name; ?>_<?php echo $option_value; ?>"><?php echo $option_label; ?></label>
				<?php
					}
				
				?>
			  </div>
		
			  <div class="clear"></div>
		  </li>
		
		<?php
		}
		// END OF CHECK BOXES
		
		// START OF CUSTOM FIELDTYPES
		else {
			if(function_exists("display_tppo_".$thisfield->field_type)){
				call_user_func("display_tppo_".$thisfield->field_type, $thisfield, $tpp_id);
			}
		}
		// END OF CHECK BOXES
	}

}

?>