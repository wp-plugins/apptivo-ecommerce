<?php
/**
 * Authorize.net
 */
class apptivo_ecommerce_securecheckout extends apptivo_ecommerce_payment_gateway {
		
	public function __construct() { 
	    $this->id			= 'SecureCheckout';
        $this->icon         = file_exists(get_stylesheet_directory() . '/apptivo-ecommerce/images/secureCheckout.png') ? get_stylesheet_directory_uri() . '/apptivo-ecommerce/images/secureCheckout.png' : APPTIVO_ECOMMERCE_PLUGIN_BASEURL.'/assets/images/secureCheckout.png';
        $this->has_fields 	= false;
    	$this->init_form_fields();
		$this->init_settings();
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		$this->email 		= $this->settings['email'];
	
	  add_action('apptivo_ecommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
	
    } 
    
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'apptivo_ecommerce' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Authorize.net', 'apptivo_ecommerce' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'apptivo_ecommerce' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during Authorize.net.', 'apptivo_ecommerce' ), 
							'default' => __( 'Authorize.net', 'apptivo_ecommerce' )
						),
			'description' => array(
							'title' => __( 'Description', 'apptivo_ecommerce' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during Authorize.net.', 'apptivo_ecommerce' ), 
							'default' => __("Pay via Authorize.net ", 'apptivo_ecommerce')
						)
			
			);
    
    } 
    public function admin_options() {

    	?>
    	<h3><?php _e('Authorize.net', 'apptivo_ecommerce'); ?></h3>
    	<table class="form-table">
    	<?php  $this->generate_settings_html(); ?>
		</table>
    	<?php
    }
    
    function payment_fields() {
    	if ($this->description) echo wpautop(wptexturize($this->description));
    }
    
    function process_payment($user_id,$cartsessionId,$Editurl,$shippingMethodID) {
		$posted = get_apptivo_billing_shipping_address();
		$cardVerificationValue = $posted['cvv2_value'];   // CVV2 Code
		$creditCartNumber = $posted['creditcard_number']; //Credit Card Number
		$expire_date = $posted['creditcard_expire_date']; //Credit card Expiration Date.
		$expire_date_exp = explode("/",$expire_date);
		$expiryMonth = $expire_date_exp[0];
		$expiryYear = $expire_date_exp[1];
		$cart_type = $posted['creditcard_type'];     //Credit card Type.
		switch($cart_type)
		{
			case 'VI' : //Visa
				$cardType = '002';
				break;
			case 'MC' : //Master Card
				$cardType = '001';
				break;
            case 'AX' : //American Express
            	$cardType = '003';
				break;
			case 'DI' : //Discover.
				$cardType = '004';
				break;					
		}
		$securecheckoutResponse = securecheckout($cardType,$creditCartNumber, $expiryMonth, $expiryYear, $cardVerificationValue,$shippingMethodID,$posted);
		if($securecheckoutResponse->return->orderId != '') // Geting Order Id.
		{
			return array(
			'result' 	=> 'success',
			'redirect'	=> get_permalink(get_option('apptivo_ecommerce_thanks_page_id'))
		);
		} else {
			
			return array(
			'result' 	=> 'Failure',
			'redirect'	=>  'E_100'
		);
			
		}
		
	}
		
}
function apptivo_ecommerce_securecheckout_gateway( $methods ) {
	$methods[] = 'apptivo_ecommerce_securecheckout'; 
	return $methods;
}
add_filter('apptivo_ecommerce_payment_gateways', 'apptivo_ecommerce_securecheckout_gateway' );