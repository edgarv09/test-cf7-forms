<?php
/**
 * Handle Settings Fields
 *
 * @package cf7-summary-and-print
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Add CF7 Setting Tab
 *
 * @since 1.0
 * @version 1.2
 */
class CF7_SP_Settings {

	/**
	 * Construct function of a class
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function __construct() {
		add_filter( 'wpcf7_editor_panels', array( $this, 'add_summary_tab' ), 10, 1 );
		add_filter( 'wpcf7_default_template', array( $this, 'set_default_template' ), 10, 2 );
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'add_new_property' ), 10, 2 );
		add_filter( 'wpcf7_save_contact_form', array( $this, 'save_summary_tab' ), 10, 1 );
		add_filter( 'admin_enqueue_scripts', array( $this, 'admin_script_style' ) );
	}

	/**
	 * Handle CSS & JS for backend settings
	 *
	 * @since 1.2
	 * @version 1.0
	 */
	public function admin_script_style() {

		wp_enqueue_style( 'cf7-admin-css', plugins_url( 'assets/css/cf7-admin-sp-style.css', CF7_SP_THIS ), array(), CF7_SP_VERSION );
		wp_enqueue_script( 'cf7-admin-js', plugins_url( 'assets/js/cf7-admin-sp-script.js', CF7_SP_THIS ), array( 'jquery' ), CF7_SP_VERSION, true );
	}

	/**
	 * Register Summary tab in Contact Form 7
	 *
	 * @param array $pannels cf7 tabs.
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function add_summary_tab( $pannels ) {
		$pannels['cf7_sp_summary'] = array(
			'title'    => __( 'Summary & Print', 'CF7_SP' ),
			'callback' => array( $this, 'wpcf7_summary_form' ),
		);

		return $pannels;
	}

	/**
	 * HTML Fields to popuplate in Summary Tab
	 *
	 * @param array $post wp post.
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function wpcf7_summary_form( $post ) {
		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'div'    => array(
				'class' => array(),
				'id'    => array(),
			),
			'p'      => array(
				'class' => array(),
				'id'    => array(),
			),
		);

		wp_nonce_field( 'cf7_summary_print', 'cf7_summary_print_nonce' );
		?>
		<h3><?php echo esc_html( __( 'Form Summary with Print Button', 'CF7_SP' ) ); ?></h3>
		<fieldset>
		<legend><?php echo esc_html( __( 'After submit a form user can view their form summary with a Print button.', 'CF7_SP' ) ); ?></legend>

		<p class="description">
			<input type="checkbox" id="<?php echo 'cf7_sp_enable'; ?>" <?php echo ( isset( $post->prop( 'cf7_sp_summary' )['enable'] ) && '1' === $post->prop( 'cf7_sp_summary' )['enable'] ? 'checked' : '' ); ?> name="<?php echo 'cf7_sp_enable'; ?>" value="1" />
			<label for="<?php echo 'cf7_sp_enable'; ?>"><?php echo esc_html( __( 'Enable Summary & Print', 'CF7_SP' ) ); ?><br />
			</label>
		</p>

		<p class="description">
			<label for="<?php echo 'cf7_sp_title'; ?>"><?php echo esc_html( __( 'Title', 'CF7_SP' ) ); ?><br />
				<input type="text" id="<?php echo 'cf7_sp_title'; ?>" name="<?php echo 'cf7_sp_title'; ?>" class="large-text" size="70" value="<?php echo ( isset( $post->prop( 'cf7_sp_summary' )['title'] ) ? esc_attr( $post->prop( 'cf7_sp_summary' )['title'] ) : '' ); ?>" />
			</label>            
		</p>

		<p class="description">
			<input type="checkbox" id="<?php echo 'cf7_sp_message_check'; ?>" <?php echo ( isset( $post->prop( 'cf7_sp_summary' )['message_check'] ) && '1' === $post->prop( 'cf7_sp_summary' )['message_check'] ? 'checked' : '' ); ?> name="<?php echo 'cf7_sp_message_check'; ?>" value="1" />
			<label for="<?php echo 'cf7_sp_message_check'; ?>"><?php echo esc_html( __( 'Add Message on Summary Page', 'CF7_SP' ) ); ?><br />
			</label>
			<span style="font-size:12px"><?php echo esc_html( __( 'Enable this option if you want to show message on summary page instead of Form data.', 'CF7_SP' ) ); ?></span>
		</p>

		<p class="description sp_msg <?php echo ( isset( $post->prop( 'cf7_sp_summary' )['message_check'] ) && '1' === $post->prop( 'cf7_sp_summary' )['message_check'] ? '' : 'hide' ); ?>" >
			<label for="<?php echo 'cf7_sp_message'; ?>"><?php echo esc_html( __( 'Enter Message to Show on Summary Page', 'CF7_SP' ) ); ?><br />
			</label>
			<textarea  id="<?php echo 'cf7_sp_message'; ?>" rows="5" cols="60" name="<?php echo 'cf7_sp_message'; ?>"><?php echo wp_kses( ( isset( $post->prop( 'cf7_sp_summary' )['message'] ) && '' !== $post->prop( 'cf7_sp_summary' )['message'] ? $post->prop( 'cf7_sp_summary' )['message'] : '' ), $allowed_html ); ?></textarea>
		</p>

		<p class="description">
			<label for="<?php echo 'cf7_sp_print_btn_text'; ?>"><?php echo esc_html( __( 'Print Button Text (Leave empty if you don`t want to show print button)', 'CF7_SP' ) ); ?><br />
				<input type="text" id="<?php echo 'cf7_sp_print_btn_text'; ?>" name="<?php echo 'cf7_sp_print_btn_text'; ?>" class="large-text" size="70" value="<?php echo ( isset( $post->prop( 'cf7_sp_summary' )['print_btn_txt'] ) ? esc_attr( $post->prop( 'cf7_sp_summary' )['print_btn_txt'] ) : '' ); ?>" />
			</label>
		</p>
		<?php

		do_action( 'cf7_sp_summary_setting', $post );
	}

	/**
	 * Set up Default values of setting fields
	 *
	 * @param string $template cf7 template.
	 * @param string $prop cf7 prop.
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function set_default_template( $template, $prop ) {

		if ( 'cf7_sp_summary' === $prop ) {
			$template = $this->default_template();
		}

		return $template;
	}

	/**
	 * Set Default Values of the fields
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function default_template() {

		$template = array(
			'cf7_sp_enable'         => __( '1', 'CF7_SP' ),
			'cf7_sp_title'          => __( 'Form Summary', 'CF7_SP' ),
			'cf7_sp_message_check'  => __( '0', 'CF7_SP' ),
			'cf7_sp_message'        => __( 'Enter Your Message', 'CF7_SP' ),
			'cf7_sp_print_btn_text' => __( 'Print This Form', 'CF7_SP' ),
		);

		return $template;
	}

	/**
	 * Add new property to CF7 to save summary tab fields
	 *
	 * @param parse_arg $properties array.
	 * @param object    $object cf7 object.
	 *
	 * @since 1.0
	 * @version 1.0
	 */
	public function add_new_property( $properties, $object ) {

		$properties = wp_parse_args(
			$properties,
			array(
				'cf7_sp_summary' => array(),
			)
		);

		return $properties;
	}

	/**
	 * Save Summary Tab Fields
	 *
	 * @param array $contact_form post values.
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	public function save_summary_tab( $contact_form ) {

		if ( isset( $_POST['cf7_summary_print_nonce'] ) || wp_verify_nonce( sanitize_key( $_POST['cf7_summary_print_nonce'] ), 'cf7_summary_print' ) ) {
				$properties['cf7_sp_summary']['enable']          = ( isset( $_POST['cf7_sp_enable'] ) ? sanitize_text_field( wp_unslash( $_POST['cf7_sp_enable'] ) ) : '' );
				$properties['cf7_sp_summary']['title']           = ( isset( $_POST['cf7_sp_title'] ) ? sanitize_text_field( wp_unslash( $_POST['cf7_sp_title'] ) ) : '' );
				$properties['cf7_sp_summary']['message_check']   = ( isset( $_POST['cf7_sp_message_check'] ) ? sanitize_text_field( wp_unslash( $_POST['cf7_sp_message_check'] ) ) : '' );
				$properties['cf7_sp_summary']['message']         = ( isset( $_POST['cf7_sp_message'] ) ? wp_kses_post( wp_unslash( $_POST['cf7_sp_message'] ) ) : '' );
				$properties['cf7_sp_summary']['print_btn_txt']   = ( isset( $_POST['cf7_sp_print_btn_text'] ) ? sanitize_text_field( wp_unslash( $_POST['cf7_sp_print_btn_text'] ) ) : '' );
				$properties['cf7_sp_summary']['additional_text'] = '';
				$contact_form->set_properties( $properties );
		}
	}
}

$cf7_sp_settings = new CF7_SP_Settings();
