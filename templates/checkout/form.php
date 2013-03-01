<?php
/*
 * Secure Checkout Form
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */ 
?>
<?php
$baginfo = get_baginfo();
do_action('apptivo_ecommerce_before_authorize_checkout_form');//Login Form
global $apptivo_ecommerce;
$get_checkout_url = $apptivo_ecommerce->cart->secure_checkout_url(); ?>
<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">
<input type="hidden" name="pg_method" id="pg_method" value="secure" />
	<div class="col2-set" id="customer_details">
	<?php if( ($_SESSION['apptivo_checkout_shipping_type'] == 'NO')) : ?>	
	<input type="hidden" name="pg_type" id="pg_type" value="willcall" />
	<div class="col-11">
			<?php do_action('apptivo_ecommerce_checkout_billing'); ?>
	</div>
	<?php else:  ?>	
		<div class="col-1">
			<?php do_action('apptivo_ecommerce_checkout_billing'); ?>						
		</div>
		<div class="col-2">		
			<?php do_action('apptivo_ecommerce_checkout_shipping'); ?>					
		</div>
	<?php  endif; ?>	
	</div>
	<?php $confirm_page = get_option('apptivo_ecommerce_enable_a_net_confirm'); ?>	     
	<?php do_action('apptivo_ecommerce_login_account'); ?>
	
	<span id="app_order_heading"></span>
	<?php 
	  if( $confirm_page == 'no' || $confirm_page == '')
	  {
	  	do_action('apptivo_ecommerce_order_review'); 
	  } else { ?>
	
	<div id="payment" style=" background: none repeat scroll 0 0 transparent;" >
	<div class="form-row">
		       <?php 
		       $available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
				if ($available_gateways) :
				$secure_checkout_gateway = false;
				
					foreach ($available_gateways as $gateway ) :
					   if($gateway->id == 'SecureCheckout') :
					   $secure_checkout_gateway = true;
					   endif;
				   endforeach;
				   endif;
				?>	   
			<?php $apptivo_ecommerce->nonce_field('process_checkout')?>
			
			<?php do_action( 'apptivo_ecommerce_review_order_before_submit' ); ?>
			
			<?php  if( $secure_checkout_gateway) :  ?>
			<input type="submit" class="btn alt" name="place_order" id="place_order" value="<?php echo apply_filters('apptivo_ecommerce_proceed_to_checkout_button','Proceed To Checkout'); ?>" />
			<span id="payment_load" style="float:right;margin-right:50px;"></span>
			<?php if (get_option('apptivo_ecommerce_terms_page_id')>0) : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php echo 'I agree with '.$_SERVER['HTTP_HOST'].','; ?> <a href="<?php echo esc_url( get_permalink(get_option('apptivo_ecommerce_terms_page_id')) ); ?>" target="_blank"><?php _e('Terms and Conditions.', 'apptivo-ecommerce'); ?></a></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php if (isset($_POST['terms'])) echo 'checked="checked"'; ?> id="terms" />
			</p>
			<?php endif; ?>			
			<?php endif; ?>			
			<?php do_action( 'apptivo_ecommerce_review_order_after_submit' );?>			
		</div>
		</div>	
		<?php } ?>
</form>
<?php  do_action('apptivo_ecommerce_after_authorize_checkout_form'); ?>