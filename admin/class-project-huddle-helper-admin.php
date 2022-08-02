<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.brainstormforce.com
 * @since      1.0.0
 *
 * @package    Project_Huddle_Helper
 * @subpackage Project_Huddle_Helper/admin
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ph-child-site-data.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Project_Huddle_Helper
 * @subpackage Project_Huddle_Helper/admin
 * @author     Brainstorm Force <contact@brainstormforce.com>
 */
class Project_Huddle_Helper_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if( is_multisite() && is_main_site() ) {
			add_filter( 'ph_settings_advanced', array( $this, 'ph_add_multisite_setting' ) );
			add_action( 'wp_ajax_ph_network_sub_sites', array( $this, 'ph_network_sub_sites' ) );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function admin_scripts() {

		if( is_multisite() && is_main_site() ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/project-huddle-helper-admin.js', array( 'jquery' ), $this->version, true );
			wp_localize_script( $this->plugin_name, 'ph_network_vars', array(
				'ajaxurl' => get_admin_url( get_main_site_id(), 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ph-network-vars-nonce' ),
			) );
		}

	}

	/**
     * Add the script required for all sub-sites to communicate with the main site.
     * This script is added on all sub-sites.
     * This script is not added on the main site.
     * 
     * @since    1.0.0
	 * @return void
	 */
	public function frontend_scripts() {

		if( !is_main_site() ) {
			?>
			<script>
                (function(d, t, g) {
                    var ph = d.createElement(t),
                        s = d.getElementsByTagName(t)[0];
                    ph.type = 'text/javascript';
                    ph.async = true;
                    ph.charset = 'UTF-8';
                    ph.src = g + '&v=' + (new Date()).getTime();
                    s.parentNode.insertBefore(ph, s);
                })(document, 'script', '<?php $this->ph_script_api_url((int) get_option('ph_site_post')); ?>');
			</script>
			<?php
		}
	}

	/**
     * This generates the script URL for the main site.
     * This is used to communicate with the sub-sites.
     * 
	 * @param $post_id
	 * @since 1.0.0
	 * @return void
	 */
	public function ph_script_api_url($post_id) {
		// convert post object to id
		if ( is_object( $post_id ) ) {
			$post_id = $post_id->ID;
		}

		// get nonce and API key
		$api_key = $this->ph_generate_api_key( $post_id, false, get_site_url( get_current_blog_id() ) );

		$base_url = home_url('?p=' . $post_id);
		$base_url = str_replace('http:', '', $base_url);
		$base_url = str_replace('https:', '', $base_url);

        // need to remove the site path from the base url so that child site can communicate with the parent site.
		$curPageName = ltrim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
		$new_base = str_replace($curPageName, '', $base_url);

		// echo URL
		echo add_query_arg(
			array(
				'ph_apikey' => esc_html($api_key),
			),
			$new_base
		);
	}

	/**
     * This adds the setting for the multisite.
     * 
	 * @param $settings
	 * @since 1.0.0
	 * @return array
	 */
	public function ph_add_multisite_setting( $settings ) {

		$settings['fields']['multisite_network'] = array(
			'type'        => 'custom',
			'id'          => 'ph_multisite_network_button',
			'label'       => __( 'Add all sub-sites of the network to ProjectHuddle', 'project-huddle' ),
			'description' => '',
			'default'     => '',
			'html'        => '<button class="button button-primary" id="add_all_subsites_to_projecthuddle2">' . __( 'Add Sites', 'project-huddle' ) . '</button>',
		);
		return $settings;
	}

	/**
     * Handles the AJAX request for adding all sub-sites to ProjectHuddle.
     *
     * @since 1.0.0
	 * @return void
	 */
	public function ph_network_sub_sites() {
		check_ajax_referer( 'ph-network-vars-nonce', 'nonce' );

		$job = $_POST['job'];

		if( 'add' === $job && is_multisite() ) {
			$sites = get_sites();
			$current_site = get_current_blog_id();
			$sites_added = array();
			foreach ( $sites as $site ) {
				if( post_exists( get_blog_option($site->blog_id, 'blogname' ),'','','ph-website' ) ) {
					continue;
				}
				// Insert the page into the database
				$page_id = wp_insert_post(
					array(
						'post_title'  => get_blog_option($site->blog_id, 'blogname' ),
						'post_status' => 'publish',
						'post_type'   => 'ph-website',
					)
				);

				// add meta
				update_post_meta( $page_id, 'ph_website_url', get_site_url( $site->blog_id ) );
				update_post_meta( $page_id, 'ph_installed', true );

				// maybe regenerate key
				ph_generate_api_key( $page_id, false, get_site_url( $site->blog_id ) );

				// update post id
				update_blog_option( $site->blog_id, 'ph_site_post', $page_id );

				$data     = new PH_Child_Site_Data($page_id);

				foreach ($data as $key => $value) {
					update_blog_option( $site->blog_id, $key, $value );
				}

				$sites_added[] = array(
					'site_id' => $site->blog_id,
					'post_id' => $page_id,
					'ph_data' => $data,
				);
			}
			
			wp_send_json_success( array(
				'success' => true,
				'message' => 'Sites added successfully',
				'data' => $sites_added
			), 200 );
		} else {
			wp_send_json_error( array(
				'success' => false,
				'message' => 'You are not on a multisite network.'
			), 403 );
		}
	}

	/**
     * This generates the API key for the site.
     *
	 * @param $post_id
	 * @param $post
	 * @param $url
	 * @since 1.0.0
	 * @return false|string
	 */
	public function ph_generate_api_key($post_id, $post = false, $url = false)
	{
		$requested  = isset($_REQUEST['ph_website_url']) ? $_REQUEST['ph_website_url'] : false;
		$url        = $url == false ? $requested : $url;
		$stored_api = get_post_meta($post_id, 'ph_website_api', true);

		// we need a website url
		if (!isset($url) || !$url) {
			return false;
		}

		// if there's no api set yet
		if (!$stored_api) {
			// generate api key based on post id, url and time
			$api_key = md5($post_id . $url . time());

			// update post meta
			update_post_meta($post_id, 'ph_website_api', sanitize_text_field($api_key));
		} else {
			$api_key = $stored_api;
		}

		return isset($api_key) ? sanitize_text_field($api_key) : false;
	}

}
