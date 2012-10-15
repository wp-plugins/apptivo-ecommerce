<?php
/**
 * Apptivo Ecommerce Checkout
  */
class apptivo_ecommerce_checkout {
	
	var $posted;
	var $billing_fields;
	var $shipping_fields;
	var $checkout_account_fields;
	var $must_create_account;
	var $creating_account;
	var $account_username;
	var $google_paypal_fields;
	
	/** constructor */
	function __construct () {
		
		add_action('apptivo_ecommerce_checkout_billing',array(&$this,'checkout_form_billing')); 
		add_action('apptivo_ecommerce_checkout_shipping',array(&$this,'checkout_form_shipping'));
		add_action('apptivo_ecommerce_paypal_checkout_shipping',array(&$this,'checkout_paypalform_shipping'));	
		add_action('apptivo_ecommerce_login_account',array(&$this,'checkout_form_login_account'));
		add_action('apptivo_ecommerce_google_paypal_register',array(&$this,'google_paypal_form_register')); 
		
			
		$this->must_create_account = true;
		
		if (is_apptivo_user_logged_in()) $this->must_create_account = false;
		
		// Define billing fields in an array. This can be hooked into and filtered if you wish to change/add anything.
		$this->billing_fields = apply_filters('apptivo_ecommerce_billing_fields', array(
			'billing_first_name' => array( 
				'label' 		=> __('First Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-first') 
				),
			'billing_last_name' => array( 
				'label' 		=> __('Last Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last') 
				),
			'billing_company' 	=> array( 
				'label' 		=> __('Company', 'apptivo_ecommerce')
				),
			'billing_address' 	=> array( 
				'label' 		=> __('Address', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'billing_address-2' => array( 
				'label' 		=> __('Address 2', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-last'), 
				'label_class' 	=> array('hidden') 
				),
			'billing_city' 		=> array( 
				'label' 		=> __('City', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'billing_postcode' 	=> array( 
				'label' 		=> __('Zip code', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-last update_totals_on_change') 
				),
			'billing_country' 	=> array( 
				'type'			=> 'country', 
				'label' 		=> __('Country', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first update_totals_on_change'), 
				'rel' 			=> 'billing_state' 
				),
			'billing_state' 	=> array( 
				'type'			=> 'state', 
				'label' 		=> __('State', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last update_totals_on_change'), 
				'rel' 			=> 'billing_country' 
				),
			'billing_email' 	=> array( 
				'label' 		=> __('Email Address', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'billing_phone' 	=> array( 
				'label' 		=> __('Phone', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-last') 
				)
		));
		
		// Define billing fields in an array. This can be hooked into and filtered if you wish to change/add anything.
		$this->google_paypal_fields = apply_filters('apptivo_ecommerce_billing_fields', array(
			'register_first_name' => array( 
				'label' 		=> __('First Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-first') 
				),
			'register_last_name' => array( 
				'label' 		=> __('Last Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last') 
				),
			'register_company' 	=> array( 
				'label' 		=> __('Company', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-first')				 
				),
			'register_phone' 	=> array( 
				'label' 		=> __('Phone', 'apptivo_ecommerce'),  
				'class' 		=> array('form-row-last') 
				),				
			'billing_address' 	=> array( 
				'label' 		=> __('Address', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'register_address-2' => array( 
				'label' 		=> __('Address 2', 'apptivo_ecommerce'),
				'class' 		=> array('form-row-last'), 
				'label_class' 	=> array('hidden') 
				),
			'register_city' 		=> array( 
				'label' 		=> __('City', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'register_postcode' 	=> array( 
				'label' 		=> __('Zip code', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-last update_totals_on_change') 
				),
			'register_country' 	=> array( 
				'type'			=> 'country', 
				'label' 		=> __('Country', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first update_totals_on_change'), 
				'rel' 			=> 'register_state' 
				),
			'register_state' 	=> array( 
				'type'			=> 'state', 
				'label' 		=> __('State', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last update_totals_on_change'), 
				'rel' 			=> 'register_country' 
				),
			
			));
		$account_username= apply_filters('apptivo_ecommerce_account_username_label','Email Address');
		$this->account_username = $account_username;
		$this->checkout_account_fields = apply_filters('apptivo_ecommerce_checkout_account_fields', array(			
		 	'notes' => array( 
				'type' => 'notes', 
				'label' => 'Create an account by entering the information below. If you are a returning customer please login with your username at the top of the page'		          
			 ),
		    'account_username' => array( 
				'type' => 'text', 
				'label' =>$account_username. __('<span class="required">*</span>', 'apptivo_ecommerce')
			 ),
			'account_password' => array( 
				'type' => 'password', 
				'label' => __('Password <span class="required">*</span>', 'apptivo_ecommerce'), 
				'class' => array('form-row-first')
				),
			'account_password-2' => array( 
				'type' => 'password', 
				'label' => __('Confirm Password <span class="required">*</span>', 'apptivo_ecommerce'), 
				'class' => array('form-row-last')//,'label_class' => array('hidden')
				)	
			));
			
		// Define shipping fields in an array. This can be hooked into and filtered if you wish to change/add anything.
		$this->shipping_fields = apply_filters('apptivo_ecommerce_shipping_fields', array(
			'shipping_first_name' => array( 
				'label' 		=> __('First Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'shipping_last_name' => array( 
				'label' 		=> __('Last Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last') 
				),
			'shipping_company' 	=> array( 
				'label' 		=> __('Company', 'apptivo_ecommerce') 
				),
			'shipping_address' 	=> array( 
				'label' 		=> __('Address', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'shipping_address-2' => array( 
				'label' 		=> __('Address 2', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-last'), 
				'label_class' 	=> array('hidden') 
				),
			'shipping_city' 	=> array( 
				'label' 		=> __('City', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'shipping_postcode' => array(
				'label' 		=> __('Zip code', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last update_totals_on_change') 
				),
			'shipping_country' 	=> array( 
				'type'			=> 'country', 
				'label' 		=> __('Country', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first update_totals_on_change'), 
				'rel' 			=> 'shipping_state' 
				),
			'shipping_state' 	=> array( 
				'type'			=> 'state', 
				'label' 		=> __('State', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last update_totals_on_change'), 
				'rel' 			=> 'shipping_country' 
				),
			'shipping_email' 	=> array( 
				'label' 		=> __('Email Address', 'apptivo_ecommerce'),  
				'required' 		=> true, 
				'class' 		=> array('form-row-first') 
				),
			'shipping_phone' 	=> array( 
				'label' 		=> __('Phone', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-last') 
				)
		));
	}
		
	/** Output the billing information form */
	function checkout_form_billing() {
		global $apptivo_ecommerce;
		
		echo apply_filters('apptivo_ecommerce_billing_address_label','<h3>Billing Address</h3>');
		
		// Output billing form fields
		do_action('apptivo_ecommerce_before_checkout_billing_form', $this);
		foreach ($this->billing_fields as $key => $field) :
			$this->checkout_form_field( $key, $field );
			if( $field['name'] == 'billing_postcode')
			{
				 do_action('apptivo_ecommerce_after_checkout_billing_postcode', $this);
			}
		endforeach;
		
		do_action('apptivo_ecommerce_after_checkout_billing_form', $this);
	}
	
	function google_paypal_form_register()
	{
		global $apptivo_ecommerce;
		echo apply_filters('apptivo_ecommerce_customer_address_label','<h3>Customer Address</h3>');
		do_action('apptivo_ecommerce_before_google_paypal_register_form', $this);
		foreach ($this->google_paypal_fields as $key => $field) :
			$this->checkout_form_field( $key, $field );
	        if( $field['name'] == 'register_postcode')
			{
				 do_action('apptivo_ecommerce_after_google_paypal_register_postcode', $this);
			}
		endforeach;
		
		do_action('apptivo_ecommerce_after_google_paypal_register_form', $this);
	}//End google_paypal_form_register()
	
	function checkout_form_login_account()
	{
		
		// Registration Form Fields
		if (!is_apptivo_user_logged_in()) :
			echo '<div class="new-account">';
				foreach ($this->checkout_account_fields as $key => $field) :
					$this->checkout_form_field( $key, $field );
			    endforeach;			
			echo '</div>';
							
		endif;
		
	}//End checkout_form_login_account()
	
	/** Output the shipping information form */
	function checkout_form_shipping() {
		 global $apptivo_ecommerce;
		   //Shipping Details
			if (!isset($_POST) || !$_POST) $shiptobilling = apply_filters('apptivo_ecommerce_shiptobilling_default', 1); else $shiptobilling = $this->get_checkout_postvalue('shiptobilling');

			echo '<p class="form-row" id="shiptobilling"><input class="input-checkbox"  type="checkbox"  name="shiptobilling" value="1" id="shipto_billing"  /> <label for="shipto_billing" class="checkbox">'.apply_filters('apptivo_ecommerce_shipto_same_address_label','Ship to same address?').'</label></p>';
			
			echo apply_filters('apptivo_ecommerce_shipping_address_label','<h3>Shipping Address</h3>');
			
			echo'<div class="shipping_address">';
				// Output shipping form fields
				do_action('apptivo_ecommerce_before_checkout_shipping_form', $this);
				foreach ($this->shipping_fields as $key => $field) :
					$this->checkout_form_field( $key, $field );
				      if( $field['name'] == 'shipping_postcode')
						{
							 do_action('apptivo_ecommerce_after_checkout_shipping_postcode', $this);
						}
				endforeach;
				do_action('apptivo_ecommerce_after_checkout_shipping_form', $this);
			echo '</div>';
			
			/* Gift Notes */
			if( get_option('apptivo_ecommerce_enable_gift') == 'yes') 
			{
			$this->checkout_form_field( 'gift_notes', array( 
				'type' => 'textarea', 
				'class' => array('notes'), 
				'label' => apply_filters('apptivo_ecommerce_giftnote_label','Gift Note') 
				));
			do_action('apptivo_ecommerce_after_gift_notes', $this);
			}	
		
		
	}//End checkout_form_shipping()
	
	
/** Output the shipping information form */
	function checkout_paypalform_shipping() {
		  global $apptivo_ecommerce;
		
			echo apply_filters('apptivo_ecommerce_shipping_address_label','<h3>Shipping Address</h3>');
			echo'<div class="shipping_address">';
				// Output shipping form fields
				do_action('apptivo_ecommerce_before_checkout_shipping_form', $this);
				foreach ($this->shipping_fields as $key => $field) :
					$this->checkout_form_field( $key, $field );
	                  if( $field['name'] == 'shipping_postcode')
						{
							 do_action('apptivo_ecommerce_after_checkout_shipping_postcode', $this);
						}
				endforeach;
				do_action('apptivo_ecommerce_after_checkout_shipping_form', $this);
			echo '</div>';
		
		if( get_option('apptivo_ecommerce_enable_gift') == 'yes') 
		{
		$this->checkout_form_field( 'gift_notes', array( 
			'type' => 'textarea', 
			'class' => array('notes'), 
			'label' => apply_filters('apptivo_ecommerce_giftnote_label','Gift Note')
			));
		do_action('apptivo_ecommerce_after_gift_notes', $this);
		}	
		
		
	}	
	/**
	 * Outputs a checkout form field
	 */
	function checkout_form_field( $key, $args ) {
		global $apptivo_ecommerce;
		
		$defaults = array(
			'type' => 'input',
			'label' => '',
			'placeholder' => '',
			'required' => false,
			'class' => array(),
			'label_class' => array(),
			'rel' => '',
			'return' => false
		);
		
		$args = wp_parse_args( $args, $defaults );

		if ($args['required']) $required = ' <span class="required">*</span>'; else $required = '';
		
		if (in_array('form-row-last', $args['class'])) $after = '<div class="clear"></div>'; else $after = '';
		
		$field = '';
		
		switch ($args['type']) :
		   
		    case "notes" :		    	
		    	$field = '<p class="form-row '.implode(' ', $args['class']).'">'.$args['label'];
				$field .= '</p>'.$after;
		    	break;
			case "country" :
					
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<select name="'.$key.'" id="'.$key.'" class="country_to_state" rel="'.$args['rel'].'">
						<option value="">'.__('Select a country&hellip;', 'apptivo_ecommerce').'</option>';
				
				foreach($apptivo_ecommerce->countries->get_countries() as $country) :
				    $selected_value = ($this->get_checkout_postvalue($key) == '')?'US':$this->get_reg_postvalue($key);
					$field .= '<option value="'.$country->countryCode.'" '.selected($selected_value, $country->countryCode, false).'>'.__($country->countryName, 'apptivo_ecommerce').'</option>';
				endforeach;
				
				$field .= '</select></p>'.$after;

			break;
			
			case "state" :
				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>';
					
				$current_cc = $this->get_checkout_postvalue($args['rel']);
				
				$current_r = $this->get_checkout_postvalue($key);
				
				$states = $apptivo_ecommerce->countries->states;	
					
				if (isset( $states[$current_cc][$current_r] )) :
					// Dropdown
					$field .= '<select name="'.$key.'" id="'.$key.'"><option value="">'.__('Select a state&hellip;', 'apptivo_ecommerce').'</option>';
					foreach($states[$current_cc] as $ckey=>$value) :
						$field .= '<option value="'.$ckey.'" '.selected($current_r, $ckey, false).'>'.__($value, 'apptivo_ecommerce').'</option>';
					endforeach;
					$field .= '</select>';
				else :
					// Input
					$field .= '<input type="text" class="input-text" value="'.$current_r.'" placeholder="'.__('State/County', 'apptivo_ecommerce').'" name="'.$key.'" id="'.$key.'" />';
				endif;
	
				$field .= '</p>'.$after;
				
			break;
			
			case "textarea" :				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<textarea name="'.$key.'" class="input-text" id="'.$key.'" placeholder="'.$args['placeholder'].'" cols="5" rows="2">'. esc_textarea( $this->get_checkout_postvalue( $key ) ).'</textarea>
				</p>'.$after;
				
			break;
			
			case "checkbox" :
				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<input type="'.$args['type'].'" class="input-checkbox" name="'.$key.'" id="'.$key.'" value="1" '.checked($this->get_checkout_postvalue( $key ), 1, false).' />
					<label for="'.$key.'" class="checkbox '.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
				</p>'.$after;
				
			break;
			default :
			   $posted_value = $this->get_checkout_postvalue( $key );
			   if($args['type'] == 'password') { $posted_value = ''; }
			   
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<input type="'.$args['type'].'" class="input-text" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" value="'. $posted_value.'" />
				</p>'.$after;
				
			break;
		endswitch;
		
		if ($args['return']) return $field; else echo $field;
	}

	/**
	 * Process the checkout after the confirm order button is pressed
	 */
	function process_paypal_checkout() {
	    
		global $wpdb, $apptivo_ecommerce;
		$validation = &new apptivo_ecommerce_validation();

		if (isset($_POST) && $_POST && !isset($_POST['login']) && !isset($_POST['last_pwd'])) :

			$apptivo_ecommerce->verify_nonce('process_checkout');
            $shoppingCart_Lines = get_baginfo()->shoppingCartLines;
	        $shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines);
	        
			if (empty($shoppingCartLines[0])) :
				$apptivo_ecommerce->add_error( sprintf(__('Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'apptivo_ecommerce'), home_url()) );
			endif;
			
				
			do_action('apptivo_ecommerce_checkout_process');
						
			// Checkout fields (non-shipping/billing)
			
			$this->posted['terms'] 				= isset($_POST['terms']) ? 1 : 0;
			$this->posted['createaccount'] 		= isset($_POST['createaccount']) ? 1 : 0;
			$this->posted['payment_method'] 	= isset($_POST['payment_method']) ? apptivo_ecommerce_clean($_POST['payment_method']) : '';
			$this->posted['default_willcall_pickup_id']     = isset($_POST['default_willcall_pickup_id']) ? apptivo_ecommerce_clean($_POST['default_willcall_pickup_id']) : '';
			
				
			$this->posted['shipping_method']	= isset($_POST['shipping_method']) ? apptivo_ecommerce_clean($_POST['shipping_method']) : '';
		    $this->posted['account_username']	= isset($_POST['account_username']) ? apptivo_ecommerce_clean($_POST['account_username']) : '';
			$this->posted['account_password'] 	= isset($_POST['account_password']) ? apptivo_ecommerce_clean($_POST['account_password']) : '';
			$this->posted['account_password-2'] = isset($_POST['account_password-2']) ? apptivo_ecommerce_clean($_POST['account_password-2']) : '';
			$this->posted['gift_notes']  = trim($_POST['gift_notes']);
					
			// Update customer shipping method to posted method
			$_SESSION['_chosen_shipping_method'] = $this->posted['shipping_method'];
			
			if (is_apptivo_user_logged_in()) :
				$this->creating_account = false;
			elseif (isset($this->posted['guestaccount']) && $this->posted['guestaccount']) :
				$this->creating_account = false;			
			else :
				$this->creating_account = true;  // Default set create_account is true / Jkumar
			endif;

			if($this->creating_account){
				foreach ($this->google_paypal_fields as $key => $field) :
				$this->posted[$key] = isset($_POST[$key]) ? apptivo_ecommerce_clean($_POST[$key]) : '';
				// Hook
				$this->posted[$key] = apply_filters('apptivo_ecommerce_process_register_field_' . $key, $this->posted[$key]);
				// Special handling for validation and formatting
				switch ($key) :
					case "register_postcode" :
						$this->posted[$key] = strtolower(str_replace(' ', '', $this->posted[$key]));						
						if (!$validation->zipcode_isvalid( $this->posted[$key], $_POST['register_country'] )) :
						    $apptivo_ecommerce->add_error( $field['label'] . __('  is not a valid Zip code.', 'apptivo_ecommerce'),$key ); 
						else :
							$this->posted[$key] = $validation->zipcode_format( $this->posted[$key], $_POST['register_country'] );
						endif;
					break;
					
					case "register_phone" :
						if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) )
						{
							$apptivo_ecommerce->add_error( $field['label'] . __('  is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if( $field['required'] ) {	
						 if (!$validation->is_phone( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __('  is not a valid number.', 'apptivo_ecommerce'),$key ); endif;
						}
						}
					break;
					
					case "register_email" :
						if (!$validation->is_email( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __('  is not a valid email address.', 'apptivo_ecommerce'),$key ); endif;
					break;
					
				endswitch;
				if( $key != 'register_phone') {
				// Required
				if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) $apptivo_ecommerce->add_error( $field['label'] . __('  is a required field.', 'apptivo_ecommerce'),$key );
				}
				
		endforeach;
			}
	
			if( ($_SESSION['apptivo_checkout_shipping_type'] != 'NO')) {				
			foreach ($this->shipping_fields as $key => $field) :				
				$this->posted[$key] = apptivo_ecommerce_clean($_POST[$key]);					

				switch ($key) :
						case "shipping_postcode" :
							$this->posted[$key] = strtolower(str_replace(' ', '', $this->posted[$key]));
							
							if (!$validation->zipcode_isvalid( $this->posted[$key], $_POST['shipping_country'] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid postcode/ZIP.', 'apptivo_ecommerce'),$key ); 
							else :
								$this->posted[$key] = $validation->zipcode_format( $this->posted[$key], $_POST['shipping_country'],$key );
							
							endif;
						break;
					case "shipping_phone" :
						if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __('  (shipping) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if( $field['required'] ) {
						if (!$validation->is_phone( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid number.', 'apptivo_ecommerce'),$key ); endif;
						}
						}
					break;
					case "shipping_email" :
			            if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __('  (shipping) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if (!$validation->is_email( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid email address.', 'apptivo_ecommerce'),$key ); endif;
						}
					break;
					endswitch;
					
					if( $key != 'shipping_phone' && $key != 'shipping_email') {
					// Required
					if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is a required field.', 'apptivo_ecommerce'),$key );
					}
					
			endforeach;				
			}
						
			if ($this->creating_account) :
			
			    $_POST['account_username'] = trim($_POST['account_username']);
			    
				if ( empty($_POST['account_username']) ) {
				     $error_message_ae125 = apptivo_ecommerce_error_message('AE-125');
					$apptivo_ecommerce->add_error( $error_message_ae125,'account_username' );
				}else {
			    if ( !$validation->is_email( $_POST['account_username'] ) ) :
			        $error_message_ae126 = apptivo_ecommerce_error_message('AE-126');
					$apptivo_ecommerce->add_error( $error_message_ae126,'account_username' );
				endif;
				}
				
				$error_message_ae123 = apptivo_ecommerce_error_message('AE-123');
				$error_message_ae124 = apptivo_ecommerce_error_message('AE-124');
				$error_message_ae107 = apptivo_ecommerce_error_message('AE-107');
				$error_message_ae116 = apptivo_ecommerce_error_message('AE-116');
				/* Password */
				if ( empty($this->posted['account_password']) ) 
				{
					$apptivo_ecommerce->add_error( __($error_message_ae123, 'apptivo_ecommerce'),'account_password' );
				}
				/* Confirm Password */
	            if ( empty($this->posted['account_password-2']) ) 
				{
					$error_message_ae130 = apptivo_ecommerce_error_message('AE-130');
					$apptivo_ecommerce->add_error( __($error_message_ae130, 'apptivo_ecommerce'),'account_password' );
				}
				/* To match password and confirm password */
	           if( $this->posted['account_password'] != '' && $this->posted['account_password-2'] != '') {
					if( !$validation->is_password($this->posted['account_password']))
					{   
					$apptivo_ecommerce->add_error( __('Password should be minimum 8 characters.', 'apptivo_ecommerce') );
					}else {
					if ( $this->posted['account_password-2'] !== $this->posted['account_password'] ) $apptivo_ecommerce->add_error( __('Passwords do not match.', 'apptivo_ecommerce') );
					}
		        }
		        
				
			endif; //End ($this->creating_account)
			
			
			
			if ($apptivo_ecommerce->cart->needs_payment()) :
				// Payment Method
				$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
				if (!isset($available_gateways[$this->posted['payment_method']])) :
				    $error_message_ae117 = apptivo_ecommerce_error_message('AE-117');				
					$apptivo_ecommerce->add_error( __($error_message_ae117, 'apptivo_ecommerce') );
				else :
					// Payment Method Field Validation
					$available_gateways[$this->posted['payment_method']]->validate_fields();
				endif;
				
			endif; //End ($apptivo_ecommerce->cart->needs_payment())
			
			
						
			// Terms
			if (!isset($_POST['update_totals']) && empty($this->posted['terms']) && get_option('apptivo_ecommerce_terms_page_id')>0 ) 
			{
				$error_message_ae122 = apptivo_ecommerce_error_message('AE-122');
				$apptivo_ecommerce->add_error( __($error_message_ae122, 'apptivo_ecommerce') );
			}
		
		
	      		
			
			if (!isset($_POST['update_totals']) && $apptivo_ecommerce->error_count()==0) :
				
				//$user_id = get_current_user_id();
				
				while (1) :
					
					// Create customer account and log them in
					if ($this->creating_account && !$user_id) :
				
						$reg_errors = new WP_Error();
						do_action('register_post', $this->posted['billing_email'], $this->posted['billing_email'], $reg_errors);
						$errors = apply_filters( 'registration_errors', $reg_errors, $this->posted['billing_email'], $this->posted['billing_email'] );
				
		                // if there are no errors, let's create the user account
						if ( !$reg_errors->get_error_code() ) :		
			                $user_pass = $this->posted['account_password'];			               
			                $user_registration = apptivo_create_user( $this->posted['account_username'],$user_pass,$this->posted,true);
			                $user_id = $user_registration->return->accountId; //accountId////apptivo account userID
			                if($user_id != '')
			                {
			                	$_SESSION['apptivo_user_account_id'] = $user_id;
			                	$_SESSION['apptivo_user_account_name'] = $user_registration->return->accountName;
			                }else{
			                
			                if ($user_registration == 'E_100') {
			                	$apptivo_ecommerce->add_error( 'Registration Failed,Please try again');
			                    break;
			                }else if($user_registration->return->methodResponse->responseCode == 'AS-004') //An account is already registered.
			                {
			                	$error_message_as004 = apptivo_ecommerce_error_message('AS-004'); 
			                	$apptivo_ecommerce->add_error( __($error_message_as004, 'apptivo_ecommerce') );
			                	breaK;
			                }else if($user_id == '' || empty($user_id))
			                {
			                	$apptivo_ecommerce->add_error('Registration Failed,Please try again');
			                    break;
			                }else{
			                	$apptivo_ecommerce->add_error('Registration Failed,Please try again');
			                	break;
			                }
			                
			                }		                 
						else :
							$apptivo_ecommerce->add_error( $reg_errors->get_error_message() );
		                	break;                    
						endif;
						
					endif;
					
						
						$shipping_first_name = $this->posted['shipping_first_name'];
						$shipping_last_name = $this->posted['shipping_last_name'];
						$shipping_company = $this->posted['shipping_company'];
						$shipping_address_1 = $this->posted['shipping_address'];
						$shipping_address_2 = $this->posted['shipping_address-2'];
						$shipping_city = $this->posted['shipping_city'];							
						$shipping_state = $this->posted['shipping_state'];
						$shipping_postcode = $this->posted['shipping_postcode'];	
						$shipping_country = $this->posted['shipping_country'];
					
				     
			
					if ($apptivo_ecommerce->error_count()>0) break;
						
					//Billing & shipping address.
					$apptivo_bill_ship_address = apptivo_billing_shipping_address($this->posted);	
					$shippingMethodID = $this->posted['shipping_method'];								
					// Process payment
					$apptivo_user_id = is_apptivo_user_logged_in();					
					if ($apptivo_ecommerce->cart->needs_payment()) :					
						$cartSessionId = is_apptivo_cart_sessionId();
						$Editurl =  get_permalink(get_option('apptivo_ecommerce_cart_page_id'));
						// Process Payment
						$result = $available_gateways[$this->posted['payment_method']]->process_payment($apptivo_user_id,$cartSessionId,$Editurl,$shippingMethodID);
						$_SESSION['apptivo_ecommerce_thanks_page_id'] =get_permalink(get_option('apptivo_ecommerce_thanks_page_id')); //set thanks page_id.
						// Redirect to success/confirmation/payment page
						if ($result['result']=='success') :						
							if (is_ajax()) : 
								ob_clean();
								echo json_encode($result);
								exit;
							else :
							    wp_redirect($result['redirect']);//wp_safe_redirect( $result['redirect'] );
								exit;
							endif;
							
						endif;
					
					else :
						
						// Empty the Cart
						$apptivo_ecommerce->cart->emty_shopping_cart();
						
						// Redirect to success/confirmation/payment page
						if (is_ajax()) : 
							ob_clean();
							echo json_encode( array('redirect'	=> get_permalink(get_option('apptivo_ecommerce_thanks_page_id'))) );
							exit;
						else :
							wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_thanks_page_id')) );
							exit;
						endif;
						
					endif;
					
					// Break out of loop
					break;
				
				endwhile;
	
			endif;
			
			// If we reached this point then there were errors
			if (is_ajax()) : 
				ob_clean();
				$apptivo_ecommerce->show_messages();
				exit;
			else :
				$apptivo_ecommerce->show_messages();
			endif;
		
		endif;
}//End process_paypal_checkout()
	

function process_confirm_checkout()
{
	global $wpdb, $apptivo_ecommerce;
    $validation = &new apptivo_ecommerce_validation();
	
    if (isset($_POST) && $_POST  ) :
   		
            $shoppingCart_Lines = get_baginfo()->shoppingCartLines;
	        $shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines);
	        
			if (empty($shoppingCartLines[0])) :
				$apptivo_ecommerce->add_error( sprintf(__('Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'apptivo_ecommerce'), home_url()) );
			endif;

			$this->posted = get_apptivo_billing_shipping_address();
			$this->posted['creditcard_number'] 	= isset($_POST['ccNum']) ? apptivo_ecommerce_clean($_POST['ccNum']) : '';
			$this->posted['cvv2_value'] = isset($_POST['ccId']) ? apptivo_ecommerce_clean($_POST['ccId']) : '';
			$this->posted['creditcard_type'] = isset($_POST['cart_type']) ? apptivo_ecommerce_clean($_POST['cart_type']) : '';
			$this->posted['creditcard_expire_date'] = isset($_POST['expire_month']) ? apptivo_ecommerce_clean($_POST['expire_month']."/".$_POST['expire_year']) : '';
			$this->posted['terms'] = isset($_POST['terms']) ? 1 : 0;
			$this->posted['shipping_method']	= isset($_POST['shipping_method']) ? apptivo_ecommerce_clean($_POST['shipping_method']) : '';
			
			$this->posted['payment_method'] = 'SecureCheckout';
			apptivo_billing_shipping_address($this->posted);
			$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
			
			
 	           $cart_type = trim($_POST['cart_type']);
				$cart_number = trim($_POST['ccNum']);
				$card_validate = checkCreditCard($cart_number,$cart_type);   //Credit card Validation
				
				if( $cart_type == '') :
				$error_message_ae129 = apptivo_ecommerce_error_message('AE-129');
				$apptivo_ecommerce->add_error( __($error_message_ae129, 'apptivo_ecommerce') );
				endif;
				
				if(!$card_validate) :
				$error_message_ae118 = apptivo_ecommerce_error_message('AE-118');
				$apptivo_ecommerce->add_error( __($error_message_ae118, 'apptivo_ecommerce') );
				endif;
				
				
				$expire_year = $_POST['expire_year'];
				$expire_month = $_POST['expire_month'];
				$expiredate_validate = expire_date($expire_year,$expire_month);  //Validate Expire Date.
				
				if(!$expiredate_validate) :
				$error_message_ae119 = apptivo_ecommerce_error_message('AE-119');
				$apptivo_ecommerce->add_error( __($error_message_ae119, 'apptivo_ecommerce') );
				endif;
				
				$cvv2 =  $_POST['ccId'];
				$cvv_validate = cvv2_validate($cart_number,$cvv2,$cart_type);   //Validate CVV2 Code.
				if(!$cvv_validate) :
				$error_message_ae120 = apptivo_ecommerce_error_message('AE-120');
				$apptivo_ecommerce->add_error( __($error_message_ae120, 'apptivo_ecommerce') );
				endif;
			
			
			
			if ( $apptivo_ecommerce->error_count()==0) :
				while (1) :
				
					if ($apptivo_ecommerce->error_count()>0) break;
		
					$shippingMethodID = $this->posted['shipping_method'];
					if(trim($shippingMethodID) == ''):
					$shippingMethodID = NULL;
					endif;
    				
					// Process payment
					$apptivo_user_id = is_apptivo_user_logged_in();					
					if ($apptivo_ecommerce->cart->needs_payment()) :					
						$cartSessionId = is_apptivo_cart_sessionId();
						$Editurl =  get_permalink(get_option('apptivo_ecommerce_cart_page_id'));
						// Process Payment
						$result = $available_gateways['SecureCheckout']->process_payment($apptivo_user_id,$cartSessionId,$Editurl,$shippingMethodID);

						$_SESSION['apptivo_ecommerce_thanks_page_id'] =get_permalink(get_option('apptivo_ecommerce_thanks_page_id')); //set thanks page_id.
						// Redirect to success/confirmation/payment page
						if ($result['result']=='success') :						
							if (is_ajax()) : 
								ob_clean();
								echo json_encode($result);
								exit;
							else :
							    wp_redirect($result['redirect']);//wp_safe_redirect( $result['redirect'] );
								exit;
							endif;
							
						endif;
					
					else :
					
							
						// Empty the Cart
						$apptivo_ecommerce->cart->emty_shopping_cart();
						
						// Redirect to success/confirmation/payment page
						if (is_ajax()) : 
							ob_clean();
							echo json_encode( array('redirect'	=> get_permalink(get_option('apptivo_ecommerce_thanks_page_id'))) );
							exit;
						else :
							wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_thanks_page_id')) );
							exit;
						endif;
						
					endif;
					
					// Break out of loop
					break;
				
				endwhile;
	
			endif;
			
			// If we reached this point then there were errors
			if (is_ajax()) : 
				ob_clean();
				$apptivo_ecommerce->show_messages();
				exit;
			else :
				$apptivo_ecommerce->show_messages();
			endif;
		
		endif;
}//End process_confirm_checkout()


	/**
	 * Process the checkout after the confirm order button is pressed
	 */
	function process_checkout() {
	    
		global $apptivo_ecommerce;
		$validation = &new apptivo_ecommerce_validation();
		
		$confirm_page = get_option('apptivo_ecommerce_enable_a_net_confirm');
	
		if (isset($_POST) && $_POST && !isset($_POST['login']) && !isset($_POST['last_pwd'])) :
		
			$apptivo_ecommerce->verify_nonce('process_checkout');
            $shoppingCart_Lines = get_baginfo()->shoppingCartLines;
	        $shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines);
	        
			if (empty($shoppingCartLines[0])) :
				$apptivo_ecommerce->add_error( sprintf(__('Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'apptivo_ecommerce'), home_url()) );
			endif;
			
			do_action('apptivo_ecommerce_checkout_process');
						
			
			// Checkout fields (non-shipping/billing)
			$this->posted['shiptobilling'] 		= isset($_POST['shiptobilling']) ? 1 : 0;
			$this->posted['terms'] 				= isset($_POST['terms']) ? 1 : 0;
			$this->posted['createaccount'] 		= isset($_POST['createaccount']) ? 1 : 0;
			$this->posted['payment_method'] 	= isset($_POST['payment_method']) ? apptivo_ecommerce_clean($_POST['payment_method']) : '';
			$this->posted['default_willcall_pickup_id']     = isset($_POST['default_willcall_pickup_id']) ? apptivo_ecommerce_clean($_POST['default_willcall_pickup_id']) : '';
			
			$this->posted['creditcard_number'] 	= isset($_POST['ccNum']) ? apptivo_ecommerce_clean($_POST['ccNum']) : '';
			$this->posted['cvv2_value'] 	= isset($_POST['ccId']) ? apptivo_ecommerce_clean($_POST['ccId']) : '';
			$this->posted['creditcard_type'] 	= isset($_POST['cart_type']) ? apptivo_ecommerce_clean($_POST['cart_type']) : '';
			$this->posted['creditcard_expire_date'] 	= isset($_POST['expire_month']) ? apptivo_ecommerce_clean($_POST['expire_month']."/".$_POST['expire_year']) : '';
			
			$this->posted['shipping_method']	= isset($_POST['shipping_method']) ? apptivo_ecommerce_clean($_POST['shipping_method']) : '';
			$this->posted['gift_notes'] 	= isset($_POST['gift_notes']) ? apptivo_ecommerce_clean($_POST['gift_notes']) : '';
			$this->posted['account_username']	= isset($_POST['account_username']) ? apptivo_ecommerce_clean($_POST['account_username']) : '';
			$this->posted['account_password'] 	= isset($_POST['account_password']) ? apptivo_ecommerce_clean($_POST['account_password']) : '';
			$this->posted['account_password-2'] = isset($_POST['account_password-2']) ? apptivo_ecommerce_clean($_POST['account_password-2']) : '';
			
			if ($apptivo_ecommerce->cart->ship_to_billing_address()) $this->posted['shiptobilling'] = 1;
			
			// Update customer shipping method to posted method
			$_SESSION['_chosen_shipping_method'] = $this->posted['shipping_method'];
			
				
			
			// Billing Information
			foreach ($this->billing_fields as $key => $field) :				
				$this->posted[$key] = isset($_POST[$key]) ? apptivo_ecommerce_clean($_POST[$key]) : '';
				// Hook
				$this->posted[$key] = apply_filters('apptivo_ecommerce_process_checkout_field_' . $key, $this->posted[$key]);
				// Special handling for validation and formatting
				switch ($key) :
					case "billing_postcode" :
						$this->posted[$key] = strtolower(str_replace(' ', '', $this->posted[$key]));
						
						if (!$validation->zipcode_isvalid( $this->posted[$key], $_POST['billing_country'] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is not a valid postcode/ZIP.', 'apptivo_ecommerce'),$key ); 
						else :
							$this->posted[$key] = $validation->zipcode_format( $this->posted[$key], $_POST['billing_country'] );
						endif;
					break;
					case "billing_phone" :
						if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if( $field['required'] ) {		
						if (!$validation->is_phone( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is not a valid number.', 'apptivo_ecommerce'),$key ); endif;
						}
						}
					break;
					case "billing_email" :
	                    if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if (!$validation->is_email( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is not a valid email address.', 'apptivo_ecommerce'),$key); endif;
						}
					break;
				endswitch;
				
				if( $key != 'billing_email' && $key != 'billing_phone' ) {
				// Required
				if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) $apptivo_ecommerce->add_error( $field['label'] . __(' (billing) is a required field.', 'apptivo_ecommerce'),$key );
				}
				
			endforeach;
			
				// Shipping Information  
			if (empty($this->posted['shiptobilling']) &&($_POST['pg_type'] != 'willcall') ) : 
				$this->posted[shiptobilling] = $this->posted['shiptobilling'];
				foreach ($this->shipping_fields as $key => $field) :				
				$this->posted[$key] = apptivo_ecommerce_clean($_POST[$key]);					
					// Hook
						switch ($key) :
						case "shipping_postcode" :
							$this->posted[$key] = strtolower(str_replace(' ', '', $this->posted[$key]));
							
							if (!$validation->zipcode_isvalid( $this->posted[$key], $_POST['shipping_country'] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid postcode/ZIP.', 'apptivo_ecommerce'),$key ); 
							else :
								$this->posted[$key] = $validation->zipcode_format( $this->posted[$key], $_POST['shipping_country'],$key );
							
							endif;
						break;
					case "shipping_phone" :
						if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if( $field['required'] ) {		
						if (!$validation->is_phone( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid number.', 'apptivo_ecommerce'),$key ); endif;
						}
						}
					break;
					case "shipping_email" :
	                   if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) 
						{
							$apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if (!$validation->is_email( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is not a valid email address.', 'apptivo_ecommerce'),$key ); endif;
						}
					break;
					endswitch;
					
					if( $key != 'shipping_email' && $key != 'shipping_phone' ) {
					// Required
					if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) $apptivo_ecommerce->add_error( $field['label'] . __(' (shipping) is a required field.', 'apptivo_ecommerce'),$key );
					}
					
				endforeach;				
				
		else :			
		endif; 
		
			if (is_apptivo_user_logged_in()) :
				$this->creating_account = false;
			elseif (isset($this->posted['guestaccount']) && $this->posted['guestaccount']) :
				$this->creating_account = false;			
			else :
				$this->creating_account = true;  
			endif;

			
			if ($this->creating_account) :
			    $_POST['account_username'] = trim($_POST['account_username']);
				if ( empty($_POST['account_username']) ) {
				     $error_message_ae125 = apptivo_ecommerce_error_message('AE-125');
					$apptivo_ecommerce->add_error( $error_message_ae125,'account_username' );
				} else {
			    if ( !$validation->is_email( $_POST['account_username'] ) ) :
			        $error_message_ae126 = apptivo_ecommerce_error_message('AE-126');
					$apptivo_ecommerce->add_error( $error_message_ae126,'account_username' );
				endif;
				}
				
				$error_message_ae123 = apptivo_ecommerce_error_message('AE-123');
				$error_message_ae124 = apptivo_ecommerce_error_message('AE-124');
				$error_message_ae107 = apptivo_ecommerce_error_message('AE-107');
				$error_message_ae116 = apptivo_ecommerce_error_message('AE-116');
				
	            /* Password */
				if ( empty($this->posted['account_password']) ) 
				{
					$apptivo_ecommerce->add_error( __($error_message_ae123, 'apptivo_ecommerce'),'account_password' );
				}
				/* Confirm Password */
	            if ( empty($this->posted['account_password-2']) ) 
				{
					$error_message_ae130 = apptivo_ecommerce_error_message('AE-130');
					$apptivo_ecommerce->add_error( __($error_message_ae130, 'apptivo_ecommerce'),'account_password' );
				}
				/* To match password and confirm password */
	           if( $this->posted['account_password'] != '' && $this->posted['account_password-2'] != '') {
					if( !$validation->is_password($this->posted['account_password']))
					{   
					$apptivo_ecommerce->add_error( __('Password should be minimum 8 characters.', 'apptivo_ecommerce') );
					}else {
					if ( $this->posted['account_password-2'] !== $this->posted['account_password'] ) $apptivo_ecommerce->add_error( __('Passwords do not match.', 'apptivo_ecommerce') );
					}
		        }
				
				
			endif;
			/* Steps 2 in authorize.net checkout */
			if( $confirm_page == 'no' || $confirm_page == '')
			{
			$this->posted['payment_method'] = 'SecureCheckout';
			if ($apptivo_ecommerce->cart->needs_payment()) :
				// Payment Method
				$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
				if (!isset($available_gateways[$this->posted['payment_method']])) :
				    $error_message_ae117 = apptivo_ecommerce_error_message('AE-117');				
					$apptivo_ecommerce->add_error( __($error_message_ae117, 'apptivo_ecommerce') );
				else :
					// Payment Method Field Validation
					$available_gateways[$this->posted['payment_method']]->validate_fields();
				endif;
				
			endif;
 
			//Processing For Credit card validation , CVV Validation in Secure Checkout.
			if($this->posted['payment_method'] == 'SecureCheckout')
			{
				$cart_type = trim($_POST['cart_type']);
				$cart_number = trim($_POST['ccNum']);
				$card_validate = checkCreditCard($cart_number,$cart_type);   //Credit card Validation
				
				if( $cart_type == '') :
				$error_message_ae129 = apptivo_ecommerce_error_message('AE-129');
				$apptivo_ecommerce->add_error( __($error_message_ae129, 'apptivo_ecommerce') );
				endif;
				
				if(!$card_validate) :
				$error_message_ae118 = apptivo_ecommerce_error_message('AE-118');
				$apptivo_ecommerce->add_error( __($error_message_ae118, 'apptivo_ecommerce') );
				endif;
				
				
				$expire_year = $_POST['expire_year'];
				$expire_month = $_POST['expire_month'];
				$expiredate_validate = expire_date($expire_year,$expire_month);  //Validate Expire Date.
				
				if(!$expiredate_validate) :
				$error_message_ae119 = apptivo_ecommerce_error_message('AE-119');
				$apptivo_ecommerce->add_error( __($error_message_ae119, 'apptivo_ecommerce') );
				endif;
				
				$cvv2 =  $_POST['ccId'];
				$cvv_validate = cvv2_validate($cart_number,$cvv2,$cart_type);   //Validate CVV2 Code.
				if(!$cvv_validate) :
				$error_message_ae120 = apptivo_ecommerce_error_message('AE-120');
				$apptivo_ecommerce->add_error( __($error_message_ae120, 'apptivo_ecommerce') );
				endif;
			}
		
			} //$confirm_page == 'no' ||  $confirm_page == ''
			
			// Terms and Conditions
			if (!isset($_POST['update_totals']) && empty($this->posted['terms']) && get_option('apptivo_ecommerce_terms_page_id')>0 ) 
			{
				$error_message_ae122 = apptivo_ecommerce_error_message('AE-122');
				$apptivo_ecommerce->add_error( __($error_message_ae122, 'apptivo_ecommerce') );
			}
		
			
			if (!isset($_POST['update_totals']) && $apptivo_ecommerce->error_count()==0) :
				
				while (1) :
					// Create customer account and log them in
					if ($this->creating_account && !$user_id) :
				
						$reg_errors = new WP_Error();
						do_action('register_post', $this->posted['billing_email'], $this->posted['billing_email'], $reg_errors);
						$errors = apply_filters( 'registration_errors', $reg_errors, $this->posted['billing_email'], $this->posted['billing_email'] );
				
		                // if there are no errors, let's create the user account
						if ( !$reg_errors->get_error_code() ) :		
			                $user_pass = $this->posted['account_password'];			               
			                $user_registration = apptivo_create_user( $this->posted['account_username'], $user_pass,$this->posted);
			                $user_id = $user_registration->return->accountId; //accountId////apptivo account userID
			                if($user_id != '')
			                {
			                	$_SESSION['apptivo_user_account_id'] = $user_id;
			                	$_SESSION['apptivo_user_account_name'] = $user_registration->return->accountName;
			                
			                }else{
			                
			                if ($user_registration == 'E_100') {
			                	$apptivo_ecommerce->add_error( sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... ', 'apptivo_ecommerce')));
			                    break;
			                }else if($user_registration->return->methodResponse->responseCode == 'AS-004')
			                {
			                	$error_message_as004 = apptivo_ecommerce_error_message('AS-004');
			                	$apptivo_ecommerce->add_error( __($error_message_as004, 'apptivo_ecommerce') );
			                	breaK;
			                }else if($user_id == '' || empty($user_id))
			                {
			                	$apptivo_ecommerce->add_error( sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... ', 'apptivo_ecommerce')));
			                    break;
			                }else{
			                	break;
			                }
			                }		                 
						else :
							$apptivo_ecommerce->add_error( $reg_errors->get_error_message() );
		                	break;                    
						endif;
						
					endif;
					
						

					// Get Culling and Shipping Address
					if ( !empty($this->posted['shiptobilling']) ) 
					{					
						$shipping_first_name = $this->posted['billing_first_name'];
						$shipping_last_name = $this->posted['billing_last_name'];
						$shipping_company = $this->posted['billing_company'];
						$shipping_address_1 = $this->posted['billing_address'];
						$shipping_address_2 = $this->posted['billing_address-2'];
						$shipping_city = $this->posted['billing_city'];							
						$shipping_state = $this->posted['billing_state'];
						$shipping_postcode = $this->posted['billing_postcode'];	
						$shipping_country = $this->posted['billing_country'];
					}else {
						$shipping_first_name = $this->posted['shipping_first_name'];
						$shipping_last_name = $this->posted['shipping_last_name'];
						$shipping_company = $this->posted['shipping_company'];
						$shipping_address_1 = $this->posted['shipping_address'];
						$shipping_address_2 = $this->posted['shipping_address-2'];
						$shipping_city = $this->posted['shipping_city'];							
						$shipping_state = $this->posted['shipping_state'];
						$shipping_postcode = $this->posted['shipping_postcode'];	
						$shipping_country = $this->posted['shipping_country'];
					}
					
					if ($apptivo_ecommerce->error_count()>0) break;
	
						
					//Billing & shipping address.
					$apptivo_bill_ship_address = apptivo_billing_shipping_address($this->posted);	
					$shippingMethodID = $this->posted['shipping_method'];
					if(trim($shippingMethodID) == ''):
					$shippingMethodID = NULL;
					endif;
					
					if( $confirm_page == 'yes' )
					{  $_SESSION['apptivo_ecommerce_confirm_page'] = 'yes';
			           echo "1000";
                       exit;
					}
													
					// Process payment
					$apptivo_user_id = is_apptivo_user_logged_in();					
					if ($apptivo_ecommerce->cart->needs_payment()) :					
						$cartSessionId = is_apptivo_cart_sessionId();
						$Editurl =  get_permalink(get_option('apptivo_ecommerce_cart_page_id'));
						// Process Payment
						$result = $available_gateways[$this->posted['payment_method']]->process_payment($apptivo_user_id,$cartSessionId,$Editurl,$shippingMethodID);

						$_SESSION['apptivo_ecommerce_thanks_page_id'] =get_permalink(get_option('apptivo_ecommerce_thanks_page_id')); //set thanks page_id.
						// Redirect to success/confirmation/payment page
						if ($result['result']=='success') :						
							if (is_ajax()) : 
								ob_clean();
								echo json_encode($result);
								exit;
							else :
							    wp_redirect($result['redirect']);//wp_safe_redirect( $result['redirect'] );
								exit;
							endif;
							
						endif;
					
					else :
							
						// Empty the Cart
						$apptivo_ecommerce->cart->emty_shopping_cart();
						
						// Redirect to success/confirmation/payment page
						if (is_ajax()) : 
							ob_clean();
							echo json_encode( array('redirect'	=> get_permalink(get_option('apptivo_ecommerce_thanks_page_id'))) );
							exit;
						else :
							wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_thanks_page_id')) );
							exit;
						endif;
						
					endif;
					
					// Break out of loop
					break;
				
				endwhile;
	
			endif;
			
			// If we reached this point then there were errors
			if (is_ajax()) : 
				ob_clean();
				$apptivo_ecommerce->show_messages();
				exit;
			else :
				$apptivo_ecommerce->show_messages();
			endif;
		
		endif;
}//End  process_checkout()

/** Gets the value either from the posted data, or from the users meta data */
function get_checkout_postvalue( $input ) {
		if (isset( $this->posted[$input] ) && !empty($this->posted[$input])) :
			return $this->posted[$input];
		else:
		   return '';
		endif;
	}	
}//End get_checkout_postvalue()