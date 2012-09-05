<?php
/*
 * PayPal 
 */
class apptivo_ecommerce_paypal extends apptivo_ecommerce_payment_gateway {
		
	public function __construct() { 
		global $apptivo_ecommerce;
	    $this->id			= 'paypal';
        $this->icon 		= file_exists(get_stylesheet_directory() . '/apptivo-ecommerce/images/paypal.png') ? get_stylesheet_directory_uri() . '/apptivo-ecommerce/images/paypal.png' :$apptivo_ecommerce->plugin_url() . '/assets/images/icons/paypal.png';
    	$this->init_form_fields();
		$this->init_settings();
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		add_action('apptivo_ecommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
    } 
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'apptivo_ecommerce' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable PayPal', 'apptivo_ecommerce' ), 
							'default' => 'no'
						), 
			'title' => array(
							'title' => __( 'Title', 'apptivo_ecommerce' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during PayPal.', 'apptivo_ecommerce' ), 
							'default' => __( 'PayPal', 'apptivo_ecommerce' )
						),
			'description' => array(
							'title' => __( 'Description', 'apptivo_ecommerce' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during PayPal.', 'apptivo_ecommerce' ), 
							'default' => __("Pay via PayPal", 'apptivo_ecommerce')
						)			
			);
    
    } 
   	public function admin_options() {

    	?>
    	<h3><?php _e('PayPal', 'apptivo_ecommerce'); ?></h3>
    	<table class="form-table">
    	<?php $this->generate_settings_html(); ?>
		</table>
    	<?php
    } 
    
    function payment_fields() {
    	if ($this->description) echo wpautop(wptexturize($this->description));
    }

    function process_payment($user_id,$cartsessionId,$Editurl,$shippingMethodID) {
		$posted = get_apptivo_billing_shipping_address();
	   if( $shippingMethodID == '' )
		{
			$shippingMethodID = $posted['default_willcall_pickup_id'];
		}		
		$paypalcheckoutReesponse = setupPaypalCheckout($cartsessionId, $user_id,$Editurl,$shippingMethodID);
		if($paypalcheckoutReesponse->return != '') // Geting Redirect Url.
		{
			return array(
			'result' 	=> 'success',
			'redirect'	=> $paypalcheckoutReesponse->return
		);
		} else {
			return array(
			'result' 	=> 'Failure',
			'redirect'	=>  'E_100'
		);
			
		}
	}
}
function apptivo_ecomerce_paypal_gateway( $methods ) {
	$methods[] = 'apptivo_ecommerce_paypal'; return $methods;
}
add_filter('apptivo_ecommerce_payment_gateways', 'apptivo_ecomerce_paypal_gateway' );