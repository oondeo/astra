<?php
/**
 * Astra Addon Cache
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2019, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Astra_Cache
 */
class Astra_Cache extends Astra_Cache_Base {

	/**
	 * Member Variable
	 *
	 * @var array instance
	 */
	private static $dynamic_css_files = array();

	/**
	 * Cache directory.
	 *
	 * @since x.x.x
	 * @var String
	 */
	private $cache_dir;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 * @param String $cache_dir Base cache directory in the uploads directory.
	 */
	public function __construct( $cache_dir ) {
		$this->cache_dir = $cache_dir;

		parent::__construct( $cache_dir );

		// Triggers on click on refresh/ recheck button.
		add_action( 'wp_ajax_astra_refresh_assets_files', array( $this, 'addon_refresh_assets' ) );

		add_action( 'save_post', array( $this, 'astra_refresh_assets' ) );
		add_action( 'post_updated', array( $this, 'astra_refresh_assets' ) );

		add_action( 'customize_save', array( $this, 'astra_refresh_assets' ) );
	}

	/**
	 * Create an array of all the files that needs to be merged in dynamic CSS file.
	 *
	 * @since x.x.x
	 * @param array $file file path.
	 * @return void
	 */
	public static function add_css_file( $file ) {
		self::$dynamic_css_files[] = $file;
	}

	/**
	 * Get dynamic CSS
	 *
	 * @since x.x.x
	 * @return String Dynamic CSS
	 */
	protected function get_dynamic_css() {
		$theme_css_data  = apply_filters( 'astra_dynamic_theme_css', '' );
		$theme_css_data .= $this->get_css_from_files( self::$dynamic_css_files );

		return Astra_Enqueue_Scripts::trim_css( $theme_css_data );
	}

	/**
	 * Fetch theme CSS data to be added in the dynamic CSS file.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function setup_cache() {
		$assets_info = $this->get_asset_info( 'theme' );

		if ( ! file_exists( $assets_info['path'] ) && ! $this->inline_assets() ) {
			$theme_css_data = $this->get_dynamic_css();

			// Return if there is no data to add in the css file.
			if ( empty( $theme_css_data ) ) {
				return;
			}

			$this->write_assets( $theme_css_data, 'theme' );
		}

		if ( Astra_Enqueue_Scripts::astra_enqueue_theme_assets() ) {
			// Call enqueue styles function.
			$this->enqueue_styles( 'theme' );
		}

	}

	/**
	 * Refresh Assets.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function astra_refresh_assets() {
		parent::refresh_assets( $this->cache_dir );
	}

	/**
	 * Refresh Assets, called through ajax
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function addon_refresh_assets() {
		parent::ajax_refresh_assets( $this->cache_dir );
	}

}

new Astra_Cache( 'astra' );
