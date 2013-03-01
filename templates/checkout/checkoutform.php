<?php 
/*
 * PayPal And Google Checkout Form
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
?>
<?php 
do_action('apptivo_ecommerce_before_checkout_form'); //Login Form
global $apptivo_ecommerce;
// filter hook for include new pages inside the payment method
$get_checkout_url = $apptivo_ecommerce->cart->checkout_url(); ?>
<form name="paypalcheckout" method="post" class="paypalcheckout" action="<?php echo esc_url( $get_checkout_url ); ?>">
 	<input type="hidden" name="pg_method" id="pg_method" value="<?php echo $_GET['gw']; ?>" />
 
	<div class="col2-set" id="customer_details">
	<?php if( ($_SESSION['apptivo_checkout_shipping_type'] == 'NO')) : ?>	
	<input type="hidden" name="pg_type" id="pg_type" value="willcall" />
	<?php if (!is_apptivo_user_logged_in()) {?>
	<div class="col-11">
			<?php do_action('apptivo_ecommerce_google_paypal_register'); ?>
	</div>
	<?php } ?>
	<?php else:  ?>	
	<?php if (!is_apptivo_user_logged_in()) {?>
		<div class="col-1">
			<?php do_action('apptivo_ecommerce_google_paypal_register'); ?>						
		</div>
		<div class="col-2">		
			<?php do_action('apptivo_ecommerce_paypal_checkout_shipping'); ?>					
		</div>
		<?php } else { ?>
		
		<div class="col-11">
			<?php do_action('apptivo_ecommerce_paypal_checkout_shipping'); ?>
	    </div>
	
		<?php } ?>
	<?php  endif; ?>	
	</div>	
	
  <?php do_action('apptivo_ecommerce_login_account'); ?>
  
	<h3 id="app_order_heading"><?php echo apply_filters('apptivo_ecommerce_your_order','Your order'); ?></h3>	
	
  <?php do_action('apptivo_ecommerce_checkout_order_review'); ?>
	
		
</form>
<?php  do_action('apptivo_ecommerce_after_checkout_form'); ?>