<?php

class Oqp {
	static $errors;
	var $options;
	function oqp() {
		self::$errors = new WP_Error();
		$this->options = get_option('oqp_options');

		
		if ($this->options['shortcode']) {
			add_shortcode('oqp_form', array('Oqp_Form','handle_shortcode'));
		}
		
		add_action('transition_post_status', array(__CLASS__,'transition_pending'), 10, 3);
		add_action('transition_post_status', array(__CLASS__,'transition_approved'), 10, 3);
		add_action('transition_post_status', array(__CLASS__,'transition_deleted'), 10, 3);
	}
	function get_errors() {
		return self::$errors->get_error_message();
	}
	function add_error($code=false,$message=false,$data=false) {
		self::$errors->add($code, $message, $data);
	}
	
	//send the author an email if a  OQP post is pending
	function transition_pending($new_status, $old_status, $post) {

		if ($new_status!='pending') return false;
		//if ($old_status!='trash') return false;

		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;
		
		$do_notification = apply_filters('oqp_do_transition_notification_pending',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');
		
		oqp_notification_post_pending($post);
		
		do_action('oqp_transition_pending',$post);
		
	}
	
	//send the author an email if a pending OQP post is approved
	function transition_approved($new_status, $old_status, $post) {

	
		if ($new_status!='publish') return false;
		if ($old_status=='publish') return false; //it is an update
		
		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;

		
		$do_notification = apply_filters('oqp_do_transition_notification_approved',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');

		oqp_notification_post_approved($post);
		
		do_action('oqp_transition_approved',$post);
		
	}

	//send the author an email if an OQP post is trashed
	function transition_deleted($new_status, $old_status, $post) {

		if ($new_status!='trash') return false;
		if (($old_status!='pending') && ($old_status!='publish')) return false;
		
		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;
		
		$do_notification = apply_filters('oqp_do_transition_notification_deleted',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');

		oqp_notification_post_deleted($post);
		
		do_action('oqp_transition_deleted',$post);
		
	}

}

class Oqp_Form {
	static $add_script;
	var $args;
	var $post;
	var $gallery;
	var $user_id;
	var $is_guest;
	var $key;
	

	function oqp_form() {
		if (is_admin()) return false;
		global $oqp;
		

		if ($oqp->options['shortcode']) {
			add_filter('the_posts', array(__CLASS__,'conditionally_add_scripts_and_styles'));
		}

		add_action('wp_footer', array(__CLASS__,'footer_scripts'));
		
		
	//USER ID
		global $current_user;	
		if ($current_user->id) {
			$this->user_id=$current_user->id;
		}else { //user is not logged, check if guest posting is enabled
			$dummy=oqp_get_dummy_user();
			if ($dummy->ID) {
				$this->is_guest=true;
				$this->user_id=$dummy->ID;
			}	
		}
	}
	function handle_shortcode($atts) {
		global $oqp_form;
		global $post;
		$atts=array();
		$atts['form_url']=$post->guid;
		$atts['form_id']=$post->ID;
		$oqp_form = new Oqp_Form;
		$oqp_form->oqp_block_load($atts);
		$oqp_form->oqp_block();
	}
	
	function oqp_block_arg_string_to_array($arg_slug) {
		global $oqp_form;
		$arguments = $oqp_form->args[$arg_slug];
		
		if ((is_array($arguments)) || (empty($arguments))) return false;

		$arguments=htmlspecialchars_decode($arguments); //eventually converts &amp; to &
		$arguments=explode('&',$arguments);


		foreach ($arguments as $key=>$argument) {
			//split each arg
			$arg_settings_args=explode('|',$argument);
			
			$arg_slug=$arg_settings_args[0];
			unset($arg_settings_args[0]);

			//split arg slug VS value
			foreach ($arg_settings_args as $arg_settings_arg_str) {
				$arg_settings_arg_arr=explode('=',$arg_settings_arg_str);
				$arg_settings_arg_slug=$arg_settings_arg_arr[0];
				$arg_settings_arg_values=$arg_settings_arg_arr[1];
				
				//split several values
				$arg_settings_arg_values=explode(',',$arg_settings_arg_values);
				
				if (count($arg_settings_arg_values)==1) //we don't need an array as there is only one value
					$arg_settings_arg_values=$arg_settings_arg_values[0];
					
				$new_argument[$arg_settings_arg_slug]=$arg_settings_arg_values;
				
				

			}

			$new_arguments[$arg_slug]=$new_argument;

		}
		$oqp_form->args[$arg_slug]=$new_arguments;

	}
	
	function oqp_shortcode_build_arg_make_multi($flat,$value) {
		$count=count($flat);

		$multi = array();
		$temp  =& $multi;
		$key=0;
		foreach ($flat as $item)
		{
			$key++;
			if ($key==$count) {
				$temp[$item]=$value;
			}else {
				$temp[$item] = array();
			}
			$temp =& $temp[$item];
			
		}
		return $multi;
	} 
	
	function oqp_shortcode_build_arg_array($main_arg_slug) {
		global $oqp_form;

		$taxonomies = $oqp_form->args[$main_arg_slug];
		
		$new_taxonomies=array();

		foreach ($taxonomies as $taxonomy) {
			$multis=array();
			foreach ($oqp_form->args as $key=>$arg) {
				$needle = $main_arg_slug.'{'.$taxonomy.'}';
				$brackets = explode($needle,$key);
				
				if (!$brackets[1]) continue;
				
				$new_taxonomy=array();
				
				$matchcount = preg_match_all('/\{([^\]]*)\}/', $brackets[1], $matches);
				
				
				
				$nested = $matches[1];

				
				$multi = self::oqp_shortcode_build_arg_make_multi($nested,$arg);
				
				unset($oqp_form->args[$key]);
				
				$multis=array_merge_recursive($multi,$multis);
			}
			$new_taxonomies[$taxonomy]=$multis;
			
			
		}
		
		$oqp_form->args[$main_arg_slug]=$new_taxonomies;

	}
	
	function oqp_block_load($atts=false) {
		global $oqp;

		//add_action('wp_print_styles', array(&$this,'add_styles'));

		global $blog_id;
		
		$default=array(
			//IMPORTANT
			'post_id' => $_REQUEST['oqp-post-id'], //if we need to populate a particular post
			'title' => true, //can't be false if no post_id
			'desc' => true, //can't be false if no post_id
			'post_type'	=> 'post',
			//when the form is outside a post, you need to specify those :
			'form_id'=>false, //unique ID | needed when not using a shortcode
			'form_url'=>false, //needed when not using a shortcode
			'blog_id' => $_REQUEST['oqp-blog-id'],
			//
			'blog_select'=>$oqp->options['blog_select'], //allow to select blog if user is member of multiple blogs
			'guest_posting'=>true,
			//PROCESS
			'taxonomies'=>'category,post_tag',
			//'taxonomies{category}{required}'=>true,
			//'taxonomies{category}{exclude}'=>'10,12',
			'metas'=>'oqp_post,oqp_guest_email',
			'pictures' => true,
			'gallery'=>true
		);
		
		
		//POPULATE ARGS
		//TO FIX
		//better should use $this->args but it do not work
		global $oqp_form;


		$atts = wp_parse_args( $atts, $default);

		
		$oqp_form->args = $atts;

		//TAXONOMIES
		if (!is_array($oqp_form->args['taxonomies']))
			$oqp_form->args['taxonomies']=explode(',',$oqp_form->args['taxonomies']);

		self::oqp_shortcode_build_arg_array('taxonomies');
		//self::oqp_shortcode_build_arg_array('metas');


		//populate key
		if ($_GET['oqp-key']) {
			$this->key=$_GET['oqp-key'];
		}
		
		//populate post
		if ($oqp_form->args['post_id']) {
			oqp_populate_post();
			
		}else {
			unset($oqp_form->args['post_id']);
		}

		//title+desc
		if (!$oqp_form->post) {
			$oqp_form->args['title']=true;
			$oqp_form->args['desc']=true;
		}
		
		//tiny_mce
		$oqp_form->args['tiny_mce']=$oqp->options['tiny_mce'];

 	}
	
	function oqp_block() {
		global $oqp_form;
	
		//only editors and upper roles can use this shortcode
		if ($post) {
			$author_id=$post->post_author;
			if (!oqp_user_can_for_blog('edit_others_posts',$author_id)) return false;
		} else {
			if (!$oqp_form->args['form_id']) { // if ($post); will be automatically the post ID
				_e('Your OQP form needs an unique form_id attribute','oqp');
				return false;
			}
			if (!$oqp_form->args['form_url']) { // if ($post); will be automatically the post ID
				_e('Your OQP form needs an unique form_url attribute','oqp');
				return false;
			}
		}

		//? LOAD SCRIPTS

		if (!empty($oqp_form->args['taxonomies'])) {

		
			foreach($oqp_form->args['taxonomies'] as $tax_slug=>$tax_settings) {
			
				if (($tax_settings['hierarchical']) || (oqp_taxonomy_is_hierarchical($tax_slug))) {
					self::$add_script['tree'] = true;
				} else {
					self::$add_script['autocomplete'] = true;
				}

			}
		}

		if ($oqp_form->args['tiny_mce'])
			self::$add_script['tiny_mce'] = true;
			
		if ($oqp_form->args['pictures'])
			self::$add_script['pictures'] = true;

		if (!oqp_is_multiste()) 
			unset ($oqp_form->args['blog_id']);
	
		//GENERATE FORM
		do_action('oqp_before_template');
		
		$form_file = apply_filters('oqp_get_template_form','oqp/form.php');
		
		echo oqp_get_template_html($form_file);
		do_action('oqp_after_template');	
	}

	
	function conditionally_add_scripts_and_styles($posts){
		if (empty($posts)) return $posts;
		
		$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
		foreach ($posts as $post) {
			if (stripos($post->post_content, 'oqp_form')) {
				$shortcode_found = true; // bingo!
				break;
			}
		}
	 
		if ($shortcode_found) {
			self::enqueue_styles();
			//wp_enqueue_script('my-script', '/script.js');
		}
	 
		return $posts;
	}
	
	function enqueue_styles() {
		//TO FIX files from theme ?
		
		if (!is_admin()) {
		
			wp_enqueue_style('oqp', ONEQUICKPOST_PLUGIN_URL.'/_inc/css/style.css');
			
			wp_enqueue_style('jquery.collapsibleCheckboxTree', ONEQUICKPOST_PLUGIN_URL.'/themes/oqp/_inc/css/jquery.collapsibleCheckboxTree.css');
		
			wp_enqueue_style('oqp-autocomplete', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery-autocomplete/jquery.autocomplete.css');
		}
	}
 
	function footer_scripts() {
		if (is_feed()) return false;
		if (is_admin()) return false;
		
		//not in function enqueue_scripts because depends of the shortcode args

		if (self::$add_script['tree'] ) {
			wp_register_script( 'jquery.collapsibleCheckboxTree', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery.collapsibleCheckboxTree.js',array('jquery'), '1.0.1' );
			wp_print_scripts('jquery.collapsibleCheckboxTree');
		}
		if (self::$add_script['autocomplete'] ) {
			wp_register_script( 'jquery.autocomplete', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery-autocomplete/jquery.autocomplete.pack.js',array('jquery'), '1.1' );
			wp_print_scripts('jquery.autocomplete');
		}
		if (self::$add_script['pictures'] ) {
			//self::$gallery->enqueue_scripts();
			//self::$gallery->footer_scripts();
		}
		
		
		if (self::$add_script['tiny_mce'] ) {
			//TO FIX to check
			//tiny_mce
			wp_register_script( 'tiny_mce', get_bloginfo('wpurl').'/'.WPINC.'/js/tinymce/tiny_mce.js');
			wp_print_scripts('tiny_mce');
			//tiny_mce lang
			wp_register_script( 'tiny_mce_lang', get_bloginfo('wpurl').'/'.WPINC.'/js/tinymce/langs/wp-langs-en.js');
			wp_print_scripts('tiny_mce_lang');
			?>
			<script type="text/javascript">
			//<![CDATA[
			tinyMCE.init({
				mode : "exact",
				elements : "oqp_desc",
				language : "en",
				theme : "advanced",
				theme_advanced_buttons1 : "formatselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,blockquote,outdent,indent,hr,|,link,unlink",
				theme_advanced_buttons2 : "undo,redo,|,removeformat",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left"
			});
			//]]>
			</script>
			<?php
		}
		
		if (!empty(self::$add_script)) {
			wp_register_script( 'oqp', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/oqp.js',array('jquery'), ONEQUICKPOST_VERSION );
			wp_print_scripts('oqp');
		};
		
		//MOVE THIS ELSEWHERE
		/*
		
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready( function() {
					jQuery('.oqp-form ul.expandable').expandableTree();
				});
			//]]>
			</script>
		*/

	}
	




}

function oqp_init_hook() {
	global $oqp;

	$oqp=new Oqp;

}

add_action('init','oqp_init_hook',1);
?>