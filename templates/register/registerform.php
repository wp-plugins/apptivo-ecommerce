<?php 
/*
 * Register Form.
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
global $apptivo_ecommerce;
do_action('apptivo_ecommerce_before_register_form'); ?>
<form name="register" method="post" class="register" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<!-- Register Form Fields -->	
	<div class="col2-set" id="customer_details">
			<?php do_action('apptivo_ecommerce_register'); ?>						
	</div>
	
<!-- Register reCaptcha -->


<?php if( get_option( 'apptivo_ecommerce_recaptcha_mode' ) == 'yes' ) { 

	$reCaptcha['theme'] = get_option('apptivo_ecommerce_recaptcha_theme');
	$reCaptcha['lang'] = get_option('apptivo_ecommerce_recaptcha_language');
	$reCaptcha['public_key'] = get_option( 'apptivo_ecommerce_recaptcha_publickey' );
	?>

 <script type="text/javascript">
	     var RecaptchaOptions = {
		    theme : '<?php echo $reCaptcha['theme']; ?>',
			lang : '<?php echo $reCaptcha['lang']; ?>'
	     };
	 </script>
	 
<p><?php require_once( 'recaptchalib.php' ); 		
		 echo recaptcha_get_html( $reCaptcha['public_key'] ); ?></p>
		 
<?php }  ?>		 


							
 <!-- Register Submit Fields. --> 
	<p class="form-row form-row-captcha register_user_p">
		<input type="submit" value="Submit" id="register_user" name="register_user" class="btn alt">
	</p>
	

								
</form>
<?php  do_action('apptivo_ecommerce_after_register_form'); ?>