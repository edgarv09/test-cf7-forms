/**
 * Admin script for handle settings things
 *
 * @package cf7-summary-and-print
 */

/**
 * Hide / Show message area in setting area
 *
 * @since 1.2
 * @version 1.0
 */
jQuery( document ).ready(
	function () {
		jQuery( "#cf7_sp_message_check" ).click(
			function () {
				if ( jQuery( "#cf7_sp_message_check" ).prop( 'checked' ) == true ) {
					jQuery( '#cf7_sp_summary p.sp_msg' ).removeClass( 'hide' );
				} else {
					jQuery( '#cf7_sp_summary p.sp_msg' ).addClass( 'hide' );
				}
			}
		);
	}
);
