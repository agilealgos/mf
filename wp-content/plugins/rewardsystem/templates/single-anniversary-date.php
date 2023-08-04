<?php
/**
 * This template is used for Display Single Anniversary Date.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/single-anniversary-date.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p class="<?php echo esc_attr( $classname ); ?> rs-single-anniversary-field">
	<label>
		<?php
		echo wp_kses_post( $field_name );
		if ( 'yes' == get_option( 'rs_enable_mandatory_custom_anniversary_point' ) ) :
			?>
			<span class="required">*</span>
		<?php endif; ?>
	</label>
	<?php srp_get_datepicker_html( $args ); ?>
	<span><em><?php echo wp_kses_post( $field_desc ); ?></em></span>
</p>
<?php
