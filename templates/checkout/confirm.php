<?php
/*
 * Authorize.Net with Apptivo Checkout Form
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */ 
$baginfo = get_baginfo();
do_action('apptivo_ecommerce_before_checkout_form');
global $apptivo_ecommerce;
$get_checkout_url = add_query_arg( 'step', '2', get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id')) );  ?>
<form name="confirm" method="post" class="confirm" action="<?php echo esc_url( $get_checkout_url ); ?>">
<input type="hidden" name="pg_method" id="pg_method" value="secure" />
	     
	<?php do_action('apptivo_ecommerce_login_account'); ?>
	 
	<span id="app_order_heading"></span>	
	<?php do_action('apptivo_ecommerce_order_review'); ?>
	
	
</form>
<?php  do_action('apptivo_ecommerce_after_checkout_form'); ?>