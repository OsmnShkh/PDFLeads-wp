<?php
/*
Plugin Name: PDFLeads
Description: Turn PDF content into leads.
Version: 0.6.4
Author: 21funnels
Author Email: hi@pdfleads.com
License:



*/

require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater_854( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

require('inc/table.class.php');

class PDFpage {
	const name = 'PDFLeads';
	const slug = 'pdf_page';
	const pdf_page_ver = '0.6.4';

	private $wpdb;
	static $tprefix;

	private $options = array(
		'form_build'=>array(
			'pdf_file' => array(
				'type'=>'file',
				'label'=>'PDF file'
			),
			'barpos' => array(
				'type'=>'radio',
				'label'=>'Bar Position',
				'options' => array('Top','Bottom')
			)
		)
	);
	/**
	 * Constructor
	 */
	function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		self::$tprefix = $wpdb->prefix;

		//register activation and deactivation hooks for the plugin
		register_activation_hook( __FILE__, array( &$this, 'install_pdf_page' ) );
		register_deactivation_hook(__FILE__, array( &$this, 'delete_pdf_page' ));
		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_pdf_page' ) );


	}

	/**
	 * Runs when the plugin is activated
	 */
	function install_pdf_page() {
		// create table for PDF pages

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE IF NOT EXISTS `{$this->wpdb->prefix}pdf_page` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`pdf_url` varchar(255) NOT NULL,
				`bar_position` enum('Top','Bottom') NOT NULL,
				`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`page_id` bigint(20) unsigned NOT NULL,
				`sh_text` text NOT NULL,
				`title` varchar(255) NOT NULL,
				`sh_link` varchar(255) NOT NULL,
				`social` varchar(255) NOT NULL,
				`bar_text` varchar(255) NOT NULL,
				`textline` enum('0','1') NOT NULL DEFAULT '0',
				`button_1_text` varchar(50) NOT NULL,
				`button_1_link` varchar(255) NOT NULL,
				`button_1_click` smallint(5) unsigned NOT NULL DEFAULT '0',
				`button_2_text` varchar(255) NOT NULL,
				`button_2_link` varchar(255) NOT NULL,
				`button_2_click` smallint(5) unsigned NOT NULL DEFAULT '0',
				`bar_color` varchar(20) NOT NULL,
				`text_color` varchar(20) NOT NULL,
				`button_1_color` varchar(20) NOT NULL,
				`button_2_color` varchar(20) NOT NULL,
				`form` text NOT NULL,
				`noindex` enum('0','1') NOT NULL DEFAULT '0',
				`user_id` bigint(20) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `user_id` (`user_id`),
				KEY `post_id` (`page_id`)
			  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		  dbDelta($sql);

		  add_option('twitter_author','');
	}

	function delete_pdf_page() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "DROP TABLE IF EXISTS `".$this->wpdb->prefix."pdf_page`";
		$this->wpdb->query($sql);
		delete_option('twitter_author');
	}
	/**
	 * Runs when the plugin is initialized
	 */
	function init_pdf_page() {
		if($this->duplicate_page()) {
			die();
		}

		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		$this->register_scripts_and_styles();

		add_action('admin_menu', array( &$this, 'add_admin_menu' ));
		add_action('admin_post_add_pdfpage', array( &$this, 'add_pdf_post' ) );
		add_action('admin_post_update_pdfpage', array( &$this, 'update_pdf_post' ) );

		add_action('delete_post', array( &$this, 'delete_post' ) );

		add_filter('template_include', array( $this, 'page_content_prerender'));

		//ajax function for twitter author saving
		add_action( 'wp_ajax_save_author', array( &$this, 'save_author') );

		//ajax handler for click tracking
		add_action( 'wp_ajax_pdf_click_button', array( &$this, 'click_tracking') );
		add_action( 'wp_ajax_nopriv_pdf_click_button', array( &$this, 'click_tracking') );
	}

	function click_tracking() {
		$page_id = (int)$_POST['page_id'];
		$button = (int)$_POST['button'];
		if(!$page_id || (!$button)) {
			die();
		}
		$button = 'button_'.$button.'_click';

		$this->wpdb->query( 'UPDATE `'.$this->getTable().'` SET `'.$button.'` = `'.$button.'` + 1 WHERE `page_id` = '.$page_id );
		die();
	}

	function duplicate_page( ) {

		if($_SERVER['SCRIPT_NAME'] == "/wp-admin/admin.php" && preg_match("#page=pdf-bar-duplicate&ID=\d+$#", $_SERVER['QUERY_STRING'])) {
			$ID = $_GET['ID'];
			$current_user = wp_get_current_user();

			$page_data = $this->wpdb->get_row('SELECT
												`pdf_url`,`bar_position`,`sh_text`,`title`,`sh_link`,`social`,`bar_text`,`textline`, `text_color`, `bar_color`,
												`button_1_text`,`button_1_link`, `button_1_color`,`button_2_text`,`button_2_link`,`button_2_color`,`form`, `noindex`
											FROM `'.$this->wpdb->base_prefix.'pdf_page` WHERE `id` = '.$ID, 'ARRAY_A');

			$page_data['page_id'] = $this->update_page($page_data['title'], null, 'draft');
			$page_data['user_id'] =  $current_user->ID;


			update_post_meta($page_data['page_id'], 'page_pdf_url', $page_data['pdf_url']);
			$this->wpdb->insert( $this->getTable(), $page_data );

			wp_redirect(admin_url( 'admin.php?page=pdf-bar-list&ID='.$this->wpdb->insert_id ));
			return true;
		}

	}

	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-style-social', '/css/social-style.css' );
			$this->load_file( self::slug . '-style-admin', '/css/admin-style.css' );
		} else {

			$this->load_file( self::slug . '-script-pdf-compatibility', '/js/build/components/compatibility.js', true );
			$this->load_file( self::slug . '-script-pdf', '/js/build/pdf.js', true );
			$this->load_file( self::slug . '-script-pdf-viewer', '/js/build/components/pdf_viewer.js', true );
			$this->load_file( self::slug . '-script-rrssb', '/ext/rrssb/rrssb.min.js', true );
			$this->load_file( self::slug . '-script-pdf_bar', '/js/pdf_bar.js', true );

			$this->load_file( self::slug . '-style-viewer', '/css/pdf_viewer.css' );
			$this->load_file( self::slug . '-style', '/css/style.css' );
			$this->load_file( self::slug . '-style-rrssb', '/ext/rrssb/rrssb.css' );
			//$this->load_file( self::slug . '-style-social', '/css/social-style.css' );
		}
	}


	//Filter for template_include hook, return PDF page template if meta key 'page_pdf_url' exitst
	function page_content_prerender( $template ) {

		$pdfFile = get_post_meta( get_the_ID(), 'page_pdf_url' );
		if(!isset($pdfFile[0])) {
			return $template;
		}

		$file = plugin_dir_path(__FILE__).'inc/pdf_page_front_template.php';
		if( file_exists( $file ) ) {
			wp_localize_script( self::slug . '-script-pdf_bar', 'page_data', array( 'url' => admin_url( 'admin-ajax.php' ), 'page_id'=> get_the_ID()));
			return $file;
		}
	}

	public function add_admin_menu() {
		add_menu_page('PDF List', 'PDF Bar', 'read', 'pdf-bar-list', array(&$this, 'get_page_list'),'dashicons-media-code',11);
		add_submenu_page( 'pdf-bar-list', 'Add PDF page', 'Add New', 'read', 'pdf-bar-add', array(&$this, 'add_pdf_page'));
	}

	//Admin page pdf-bar-list
	public function get_page_list() {

		if(isset($_GET['ID'])) {
			$this->edit_pdf_page($_GET['ID']);
			return;
		}

		$table = new PDF_Pages_Table();
		$table->prepare_items();

		$path = plugin_dir_path( __FILE__ ).'templates/';

		include $path.'admin_pdf_page.php';
	}

	public function add_pdf_page() {
		$page_data = array(
			"id"=>'',
			"pdf_url"=>'',
			"bar_position"=>'',
			"date"=>'',
			"page_id"=>'',
			"sh_text"=>'',
			"title"=>'',
			"sh_link"=>'',
			"social"=>'',
			"bar_color"=>'',
			"bar_text"=>'',
			'textline'=>'',
			"text_color"=>'',
			"button_1_text"=>'',
			"button_1_link"=>'',
			"button_1_color",
			"button_2_text"=>'',
			"button_2_link"=>'',
			"button_2_color",
			"form"=>'',
			'noindex'=>''
		);
		extract($page_data);

		$action = 'add_pdfpage';
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		include plugin_dir_path( __FILE__ ).'templates/add_pdf.php';
	}

	public function edit_pdf_page($ID) {

		$page_data = $this->wpdb->get_row('SELECT * FROM `'.$this->wpdb->base_prefix.'pdf_page` WHERE `id` = '.$ID, 'ARRAY_A');
		$action = 'update_pdfpage';

		extract($page_data);
		$social = json_decode($social, true);
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		include plugin_dir_path( __FILE__ ).'templates/add_pdf.php';
	}

	public function delete_post() {
		$this->wpdb->query("DELETE FROM `{$wpdb->prefix}pdf_page` WHERE id='$page_id'");
	}

	private function preparePostData() {
		$dataField = array(
			'title',
			'bar_position',
			'sh_link',
			'sh_text',
			'bar_text',
			'textline',
			'text_color',
			'bar_color',
			'noindex'
		);

		$socialField = array( "fb","gplus","twitter","linkedin","reddit");

		$extendedOptions = array();

		$socialData = array();
		$post_data = array();

		foreach($dataField as $row) {
			$post_data[$row] = (isset($_POST[$row])) ? $_POST[$row] : '';
		}

		foreach($socialField as $row_soc) {
			$socialData[$row_soc] =  (isset($_POST[$row_soc])) ? 1 : 0;
		}

		if(isset($_POST['button_1']) && !empty($_POST['button_1_text']) && !empty($_POST['button_1_link']) ) {
			$post_data['button_1_text'] = $_POST['button_1_text'];
			$post_data['button_1_link'] = $_POST['button_1_link'];
			$post_data['button_1_color'] = (isset($_POST['button_1_color']) && $_POST['button_1_color']) ? $_POST['button_1_color'] : 'primary';
		} else {
			$post_data['button_1_text'] = '';
			$post_data['button_1_link'] =  '';
		}

		if(isset($_POST['button_2']) && !empty($_POST['button_2_text']) && !empty($_POST['button_2_link']) ) {
			$post_data['button_2_text'] = $_POST['button_2_text'];
			$post_data['button_2_link'] = $_POST['button_2_link'];
			$post_data['button_2_color'] = (isset($_POST['button_2_color']) && $_POST['button_2_color']) ? $_POST['button_2_color'] : 'primary';
		} else {
			$post_data['button_2_text'] = '';
			$post_data['button_2_link'] =  '';
		}

		if(isset($_POST['form']) && !empty($_POST['form_text'])) {
			$post_data['form'] = $_POST['form_text'];
		} else {
			$post_data['form'] = '';
		}

		$post_data['social'] = json_encode($socialData);

		return $post_data;
	}

	function add_pdf_post() {

		$post_data = $this->preparePostData();

		$current_user = wp_get_current_user();

		if($_POST['pdf_link']) {
			$pdf_link = $_POST['pdf_link'];
			$name = basename($pdf_link);

			$upload_path = wp_upload_dir();
			$data = file_get_contents($pdf_link);

			$file_link = $upload_path['url'] .'/'. $name;
			$fh = fopen( $upload_path['path'] .'/'. $name, "w");
			fwrite($fh, $data);
			fclose($fh);
		}
		else {

			$upload_overrides = array( 'test_form' => false,'mimes' => array('pdf' => 'application/pdf' ) );
			$uploaded = wp_handle_upload( $_FILES['pdf_file'], $upload_overrides );

			if(isset($uploaded['error'])) {
				wp_redirect(admin_url('admin.php?page=pdf-bar-add&error='.urlencode($uploaded['error'])  ));
				die();
			}
			$file_link = $uploaded['url'];
		}
		$status = (isset($_POST['draft']) && $_POST['draft']) ? 'draft' : 'publish';
		$page_id = $this->update_page($post_data['title'], null, $status);
		update_post_meta($page_id, 'page_pdf_url', $file_link);

		$post_data['pdf_url'] = $file_link;
		$post_data['user_id'] = $current_user->ID;
		$post_data['page_id'] = $page_id;

		$this->wpdb->insert( $this->getTable(), $post_data );

		wp_redirect(admin_url( 'admin.php?page=pdf-bar-list&ID='.$this->wpdb->insert_id ));
		die();
	}

	function update_pdf_post() {

		$ID = $_POST['id'];
		$postid = $this->wpdb->get_var("SELECT `page_id` FROM `{$this->wpdb->prefix}pdf_page` WHERE id='$ID'");
		$post_data = $this->preparePostData();
		if($_POST['pdf_link']) {
			$pdf_link = $_POST['pdf_link'];
			$name = basename($pdf_link);

			$upload_path = wp_upload_dir();
			$data = file_get_contents($pdf_link);

			$file_link = $upload_path['url'] .'/'. $name;
			$fh = fopen( $upload_path['path'] .'/'. $name, "w");
			fwrite($fh, $data);
			fclose($fh);
			$post_data['pdf_url'] = $file_link;
			update_post_meta($postid, 'page_pdf_url', $file_link);
		}
		elseif(isset( $_FILES['pdf_file']['size']) &&  $_FILES['pdf_file']['size'] > 0) {

			$upload_overrides = array( 'test_form' => false,'mimes' => array('pdf' => 'application/pdf' ) );
			$uploaded = wp_handle_upload( $_FILES['pdf_file'], $upload_overrides );

			if(isset($uploaded['error'])) {
				wp_redirect(admin_url('admin.php?page=pdf-bar-add&error='.urlencode($uploaded['error'])  ));
				die();
			}
			$file_link = $uploaded['url'];
			$post_data['pdf_url'] = $file_link;
			update_post_meta($postid, 'page_pdf_url', $file_link);
		}

		$status = (isset($_POST['draft']) && $_POST['draft']) ? 'draft' : 'publish';
		$current_user = wp_get_current_user();
		$page_id = $this->update_page($post_data['title'], $postid, $status);

		$this->wpdb->update( $this->getTable(), $post_data, array( 'id' => $ID) );

		wp_redirect(admin_url( 'admin.php?page=pdf-bar-list&ID='.$ID ));
		die();
	}

	function update_page($title, $id=null, $status="publish") {
		$current_user = wp_get_current_user();
		$post = array(
			'ID' => $id,
			'comment_status' => 'closed',
			'post_author' => $current_user->ID,
			'post_content' => '',
			'post_status' => $status,
			'post_title' =>	$title,
			'post_name' =>	strtolower(preg_replace('#\s+#','_',$title)),
			'post_type' =>  'page'
		);

		return  wp_insert_post( $post );
	}

	//setting for twitter shating
	function save_author() {
		$name = $_POST['twit_author'];
		update_option('twitter_author', $name);
		die('Twitter account name is saved');
	}

	// Helper functions
	private function getTable($name = 'pdf_page') {
		return $this->wpdb->prefix.$name;
	}

	// Helper function for registering and enqueueing scripts and styles.
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery'), null ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url,  array(), self::pdf_page_ver );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file
} // end class
new PDFpage();

?>