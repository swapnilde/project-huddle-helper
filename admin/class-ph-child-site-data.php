<?php

/**
 *
 */
class PH_Child_Site_Data {

	/**
	 * ID of the project
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Public API Key
	 *
	 * @var string
	 */
	public $api_key = '';

	/**
	 * Access Token
	 *
	 * @var string
	 */
	public $access_token = '';

	/**
	 * Parent URL
	 *
	 * @var string
	 */
	public $parent_url = '';

	/**
	 * Signature Key
	 *
	 * @var string
	 */
	public $signature = '';

	/**
	 * Child Site URL
	 *
	 * @var string
	 */
	public $child_url = '';

	/**
	 * Store everthing on construct
	 *
	 * @param integer $id (required)
	 * @param string $access
	 * @param string $signature
	 * @param string $token
	 * @param string $email
	 * @param string $username
	 */
	public function __construct( $id ) {
		$this->id           = $id;
		$this->parent_url   = apply_filters( 'ph_child_website_parent_url', get_home_url() );
		$this->api_key      = get_post_meta( $id, 'ph_website_api', true );
		$this->access_token = ph_get_post_access_token( $id );
		$this->signature    = ph_post_signature_key( $id );
		$this->child_url    = get_post_meta( $id, 'website_url', true );
	}
}