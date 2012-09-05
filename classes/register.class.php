<?php
/*
 * Apptivo Ecommerce Register Class.
 */
class apptivo_ecommerce_register
{
	var $register_fields;
	var $register_account_fields;
	var $posted;
	function __construct () {
		add_action('apptivo_ecommerce_register',array(&$this,'apptivo_register')); 
		// Define billing fields in an array. This can be hooked into and filtered if you wish to change/add anything.
		$account_username= apply_filters('apptivo_ecommerce_account_username_label','Email Address');
		$this->register_fields = apply_filters('apptivo_ecommerce_register_fields', array(
			'register_first_name' => array( 
				'name'			=>'register_first_name', 
				'label' 		=> __('First Name', 'apptivo_ecommerce'),
				'required' 		=> true, 
				'class'			=> array('form-row-first') 
				),
			'register_last_name' => array( 
				'label' 		=> __('Last Name', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last') 
				),
			
			'register_address' 	=> array( 
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
				'name'			=>'register_state', 
				'label' 		=> __('State', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last update_totals_on_change'), 
				'rel' 			=> 'register_country' 
				),
			'register_company' 	=> array( 
				'label' 		=> __('Company', 'apptivo_ecommerce'), 
				'class' 		=> array('form-row-first')  
				),
			'register_phone' 	=> array( 
				'label' 		=> __('Phone', 'apptivo_ecommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last') 
				),
			'account_username'=> array( 
				'type' => 'text', 
				'label' =>$account_username,
				'required' 		=> true
				),
		    'account_password'=>array( 
				'type' => 'password', 
				'label' => __('Password', 'apptivo_ecommerce'), 
			    'required' 		=> true, 
				'class' => array('form-row-first')
				),
		    'account_password-2'=> array( 
				'type' => 'password', 
				'label' => __('Confirm password', 'apptivo_ecommerce'),
			    'required' 		=> true,  
				'class' => array('form-row-last')				
				)		
			
		));
			
	}
	
function apptivo_register()
	{
	     	global $apptivo_ecommerce;
		    do_action('apptivo_ecommerce_before_register_form_field');
			echo'<div class="register_address">';					
				foreach ($this->register_fields as $key => $field) :
					$this->register_form_field( $key, $field );
				endforeach;
			echo '</div>';
			do_action('apptivo_ecommerce_after_register_form_field');				
			
	}


	/**
	 * Outputs a checkout form field
	 */
	function register_form_field( $key, $args ) {
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
				    $selected_value = ($this->get_reg_postvalue($key) == '')?'US':$this->get_reg_postvalue($key);
					$field .= '<option value="'.$country->countryCode.'" '.selected($selected_value, $country->countryCode, false).'>'.__($country->countryName, 'apptivo_ecommerce').'</option>';
				endforeach;
				
				$field .= '</select></p>'.$after;
			break;
			
			case "state" :				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>';
					
				$current_cc = $this->get_reg_postvalue($args['rel']);
				
				$current_r = $this->get_reg_postvalue($key);
				
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
					$field .= '<input type="text" class="input-text" value="'.$current_r.'" placeholder="'.__('State', 'apptivo_ecommerce').'" name="'.$key.'" id="'.$key.'" />';
				endif;
	
				$field .= '</p>'.$after;				
			break;
			
			case "textarea" :				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<textarea name="'.$key.'" class="input-text" id="'.$key.'" placeholder="'.$args['placeholder'].'" cols="5" rows="2">'. esc_textarea( $this->get_reg_postvalue( $key ) ).'</textarea>
				</p>'.$after;
				
			break;
			
			case "checkbox" :				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<input type="'.$args['type'].'" class="input-checkbox" name="'.$key.'" id="'.$key.'" value="1" '.checked($this->get_reg_postvalue( $key ), 1, false).' />
					<label for="'.$key.'" class="checkbox '.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
				</p>'.$after;
				
			break;
			default :
			   $posted_value = $this->get_reg_postvalue( $key );
			   if($args['type'] == 'password') { $posted_value = ''; }			   
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<input type="'.$args['type'].'" class="input-text" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" value="'. $posted_value.'" />
				</p>'.$after;
				
			break;
		endswitch;
		
		if ($args['return']) return $field; else echo $field;
	}
	
function get_reg_postvalue( $input ) {
		if (isset( $this->posted[$input] ) && !empty($this->posted[$input])) :
			return $this->posted[$input];
		else:
		   return '';
		endif;   	
}	
function process_registerform() {	    
				
		global $wpdb, $apptivo_ecommerce;
		$validation = &new apptivo_ecommerce_validation();
		
		if (isset($_POST) && $_POST && !isset($_POST['login'])) :

		    $this->posted['account_username']	= isset($_POST['account_username']) ? apptivo_ecommerce_clean($_POST['account_username']) : '';
			$this->posted['account_password'] 	= isset($_POST['account_password']) ? apptivo_ecommerce_clean($_POST['account_password']) : '';
			$this->posted['account_password-2'] = isset($_POST['account_password-2']) ? apptivo_ecommerce_clean($_POST['account_password-2']) : '';
			
			
		foreach ($this->register_fields as $key => $field) :
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
						if (!$validation->is_phone( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __('  is not a valid number.', 'apptivo_ecommerce'),$key ); endif;
						}
					break;
					
					case "account_username" :
                        if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) )
						{
							$apptivo_ecommerce->add_error( $field['label'] . __('  is a required field.', 'apptivo_ecommerce'),$key );
						}else {
						if (!$validation->is_email( $this->posted[$key] )) : $apptivo_ecommerce->add_error( $field['label'] . __('  is not a valid email address.', 'apptivo_ecommerce'),$key ); endif;
						}
					break;
					
				endswitch;
				if( $key != 'register_phone' && $key != 'account_username'){
				// Required
				if ( isset($field['required']) && $field['required'] && empty($this->posted[$key]) ) $apptivo_ecommerce->add_error( $field['label'] . __('  is a required field.', 'apptivo_ecommerce'),$key );
				}
				
		endforeach;
			   
		
		        if( $_POST['account_password'] != '' && $_POST['account_password-2'] != '') {
					if( !$validation->is_password($_POST['account_password']))
					{   
					$apptivo_ecommerce->add_error( __('Password should be minimum 8 characters.', 'apptivo_ecommerce') );
					}else {
					if ( $_POST['account_password-2'] !== $_POST['account_password'] ) $apptivo_ecommerce->add_error( __('Passwords do not match.', 'apptivo_ecommerce') );
					}
		        }	
		
			endif;
			
			//reCaptcha
			if( isset($_POST['recaptcha_challenge_field']) && ( get_option( 'apptivo_ecommerce_recaptcha_mode' ) == 'yes' ) )
				{  
					require_once( APPTIVO_ECOMMERCE_PLUGIN_BASEPATH.'/templates/register/recaptchalib.php' );
									
					$privateKey = trim(get_option( 'apptivo_ecommerce_recaptcha_privatekey' ));
				
					$resp       = recaptcha_check_answer (  $privateKey,
															$_SERVER['REMOTE_ADDR'],
															$_POST['recaptcha_challenge_field'],
															$_POST['recaptcha_response_field'] );

					if ( !$resp->is_valid  ) {
						
					$captcha_error = apptivo_ecommerce_error_message('CE-001');
					$apptivo_ecommerce->add_error( __($captcha_error, 'apptivo_ecommerce'),'6_letters_code' );
					
					}
	
					
				}

			
			if( $apptivo_ecommerce->error_count() == 0 && isset($_POST['register_user']) )
			{  
				unset($_SESSION['apptvo_ecommerce_captacha_code']); //unset captcha session.
				$registerUser = apptivo_register_user($this->posted); //Calling Register User				
				if( $registerUser == 'E_100' || $registerUser->return->statusCode == 1005  ) //1005 ->Invalid Keys
				{   
					$registration_failed_error = apply_filters('apptivo_ecommerce_error_registration_falied','Registration failed. please try again.');
					$apptivo_ecommerce->add_error( __($registration_failed_error, 'apptivo_ecommerce') );
				}else if($registerUser->return->methodResponse->responseCode != 'AS-002') {
					$apptivo_ecommerce->add_error( __($registerUser->return->methodResponse->responseMessage, 'apptivo_ecommerce') );
				}else {
					      $apptivo_ecommerce->add_message( __($registerUser->return->methodResponse->responseMessage, 'apptivo_ecommerce') );
					       $user_id = $registerUser->return->accountId; 
			                if($user_id != '')
			                {
			                	$_SESSION['apptivo_user_account_id'] = $user_id;
			                	$_SESSION['apptivo_user_account_name'] = $registerUser->return->accountName;
			                }
					wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')) );
				    exit;
				}
			}
			// If we reached this point then there were errors
			if (is_ajax()) : 
				ob_clean();
				$apptivo_ecommerce->show_messages();
				exit;
			else :
				$apptivo_ecommerce->show_messages();
			endif;
	}
}