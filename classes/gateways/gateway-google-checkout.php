<?php
/**
 * Google Checkout Payment Gateway (Apptivo Google Checkout)
 */
class apptivo_ecommerce_googlecheckout extends apptivo_ecommerce_payment_gateway {
		
	public function __construct() { 
		$this->id			= 'GoogleCheckout';       
        $this->icon         = 'http://sandbox.google.com/checkout/buttons/checkout.gif?merchant_id=1234567890&amp;w=180&amp;h=46&amp;style=white&amp;variant=text&amp;loc=en_US';
        $this->has_fields 	= false;
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
							'label' => __( 'Enable Google Checkout', 'apptivo_ecommerce' ), 
							'default' => 'no'
						), 
			'title' => array(
							'title' => __( 'Title', 'apptivo_ecommerce' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during Google Checkout.', 'apptivo_ecommerce' ), 
							'default' => __( 'Google Checkout', 'apptivo_ecommerce' )
						),
			'description' => array(
							'title' => __( 'Description', 'apptivo_ecommerce' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during Google Checkout.', 'apptivo_ecommerce' ), 
							'default' => __("Pay via Google checkout", 'apptivo_ecommerce')
						)
					);
    
    } 
    public function admin_options() {
        /*
    	?>
    	<h3><?php _e('Google Checkout', 'apptivo_ecommerce'); ?></h3>
    	<table class="form-table">
    	<?php $this->generate_settings_html(); ?>
		</table>
    	<?php
         */
    } 
    function payment_fields() {
    	
    	if ($this->description) echo wpautop(wptexturize($this->description));
    }
    
  	function process_payment($user_id,$cartsessionId,$Editurl,$shippingMethodID) {

		$posted = get_apptivo_billing_shipping_address();
		$firstName = $posted['shipping_first_name'];		
		$lastName = $posted['shipping_last_name'];
		$companyName = $posted['shipping_company'];
		$address1 = $posted['shipping_address'];
		$address2 = $posted['shipping_address-2'];
		$city = $posted['shipping_city'];
		$provinceAndState = $posted['shipping_state'];
		$postalCode =$posted['shipping_postcode'];
		$giftNote = $posted['gift_notes'];
		$homePhoneNo = $posted['shipping_phone'];
		$country = 'US';		
		$middleName = null;
		$cellNo = null;
		$emailId = null;
		$addressId= null;
		$contactId= null;
		$fax= null;
		$jobTitle= null;
		$associationId= null;
		$countyAndDistrict= null;
		$dateOfBirth= null;
		$primaryContact= null;
		$title= null;
		$type= 'SHIPPING';
		$sameAsBillingAddress = 'N';
		if($user_id == '') //UserId
		{
			$user_id = 0;
		}
		$shippingAmount = '';      // Shipping Amount.
		
		if( $shippingMethodID == '' )
		{
			$shippingMethodID = $posted['default_willcall_pickup_id'];
		}
		
		if( trim($firstName) != '' ) {
		    $contactDetails = array('address1'=>$address1,'address2'=>$address2,'addressId'=>$addressId,'city'=>$city,'firstName'=>$firstName,'lastName'=>$lastName,'type'=>$type,
		                             'postalCode'=>$postalCode,'provinceAndState'=>$provinceAndState,'companyName'=>$companyName,'country'=>$country,'homePhoneNo'=>$homePhoneNo);	
	    }else{
			$contactDetails = NULL;	
		}
		 
		$googleCheckoutResponse = googleCheckout($cartsessionId,$user_id,"/",$Editurl,$shippingMethodID, $shippingAmount, $contactDetails, $giftNote);
		
		if($googleCheckoutResponse->return != '') // Geting Redirect Url.
		{
			return array(
			'result' 	=> 'success',
			'redirect'	=> $googleCheckoutResponse->return
		);
		} else {
			
			return array(
			'result' 	=> 'Failure',
			'redirect'	=>  'E_100'
		);
			
		}
	}
	
}

function apptivo_ecommerce_googlecheckout_gateway( $methods ) {
	$methods[] = 'apptivo_ecommerce_googlecheckout';
	return $methods;
}
// add_filter('apptivo_ecommerce_payment_gateways', 'apptivo_ecommerce_googlecheckout_gateway' );