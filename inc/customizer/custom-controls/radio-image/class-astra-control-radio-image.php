<?php
/**
 * Customizer Control: radio-image.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2019, Astra
 * @link        https://wpastra.com/
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Radio Image control (modified radio).
 */
class Astra_Control_Radio_Image extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'ast-radio-image';

	/**
	 * The highlight color.
	 *
	 * @access public
	 * @var string
	 */
	public static $higlight_color = '';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @access public
	 */
	public function enqueue() {
		$css_uri = ASTRA_THEME_URI . 'inc/customizer/custom-controls/radio-image/';
		$js_uri  = ASTRA_THEME_URI . 'inc/customizer/custom-controls/radio-image/';

		wp_enqueue_script( 'astra-radio-image', $js_uri . 'radio-image.js', array( 'jquery', 'customize-base' ), ASTRA_THEME_VERSION, true );
		wp_enqueue_style( 'astra-radio-image', $css_uri . 'radio-image.css', null, ASTRA_THEME_VERSION );

		if ( '' === Astra_Control_Radio_Image::$higlight_color ) {
			Astra_Control_Radio_Image::get_highlight_color();
			?>
			<style type="text/css">.ast-radio-img-svg svg * { fill: <?php echo Astra_Control_Radio_Image::$higlight_color; ?> !important }</style>
			<?php
		}
	}

	/**
	 * Highlight SVG options set from the Admin Color Scheme.
	 *
	 * @return void
	 */
	public static function get_highlight_color() {
		global $_wp_admin_css_colors;

		$current_color = get_user_option( 'admin_color' );

		if ( empty( $current_color ) || ! isset( $_wp_admin_css_colors[ $current_color ] ) ) {
			$current_color = 'fresh';
		}
		Astra_Control_Radio_Image::$higlight_color = $_wp_admin_css_colors[ $current_color ]->colors[2];
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['default'] = $this->setting->default;
		if ( isset( $this->default ) ) {
			$this->json['default'] = $this->default;
		}
		$this->json['value'] = $this->value();

		foreach ( $this->choices as $key => $value ) {
			$this->json['choices'][ $key ]        = $value['path'];
			$this->json['choices_titles'][ $key ] = $value['label'];
		}

		$this->json['link'] = $this->get_link();
		$this->json['id']   = $this->id;

		$this->json['inputAttrs'] = '';
		$this->json['labelStyle'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			if ( 'style' !== $attr ) {
				$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
			} else {
				$this->json['labelStyle'] = 'style="' . esc_attr( $value ) . '" ';
			}
		}

	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see WP_Customize_Control::print_template()
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<label class="customizer-text">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label>
		<div id="input_{{ data.id }}" class="image">
			<# for ( key in data.choices ) { #>
				<input {{{ data.inputAttrs }}} class="image-select" type="radio" value="{{ key }}" name="_customize-radio-{{ data.id }}" id="{{ data.id }}{{ key }}" {{{ data.link }}}<# if ( data.value === key ) { #> checked="checked"<# } #>>
					<label for="{{ data.id }}{{ key }}" {{{ data.labelStyle }}} class="ast-radio-img-svg">
						{{{ data.choices[ key ] }}}
						<span class="image-clickable" title="{{ data.choices_titles[ key ] }}" ></span>
					</label>
			<# } #>
		</div>
		<?php
	}
}
