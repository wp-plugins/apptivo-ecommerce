<?php
/**
 * Make API Call via SOAP
 * @package Apptivo ecommerce
 * @author Rajkumar <rmohanasundaram[at]apptivo[dot]com>
 */
/**
 * get http Url.
 *
 * @return unknown
 */	
function getServerURL() {
	$url = (! empty ( $_SERVER ['HTTPS'] )) ? "https://" . $_SERVER ['SERVER_NAME'] : "http://" . $_SERVER ['SERVER_NAME'];
	return $url;
}

function thankspage_id()
{
	return get_option('apptivo_ecommerce_thanks_page_id');
}
/**
 * Google Checkout.
 *
 * @param unknown_type $sessionId
 * @param unknown_type $userId
 * @param unknown_type $returnURL
 * @param unknown_type $editURL
 * @param unknown_type $shippingMethod
 * @param unknown_type $shippingAmount
 * @param unknown_type $contactDetails
 * @return unknown
 */
function googleCheckout($sessionId,$userId,$returnURL,$editURL,$shippingMethod,$shippingAmount, $contactDetails,$giftNote)
{  
    $url = getServerURL();
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY, "arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY, "arg2"=>$sessionId, "arg3"=>$userId,
	                "arg4"=>$url.$returnURL,"arg5"=>$editURL,"arg6"=>$shippingMethod,"arg7"=>$shippingAmount,"arg8"=>$contactDetails,"arg9"=>$giftNote);
	
	$response = ecommerce_soap_call(CART_WSDL,'GoogleCheckout',$params);
	
	if( $response == 'E_100')
	{
		 return '';
	}
	
	return $response;
	
}
/**
 * Calculate Shipping and Taxes.
 *
 * @param unknown_type $sessionId
 * @param unknown_type $shipMethod
 * @param unknown_type $zipcode
 * @return unknown
 */
function estimateShippingAndTaxRate($sessionId,$shipMethod,$zipcode='')
{
	if($zipcode == '')
	{
	if($_SESSION['chosen_shippingandtax_zipcode'] == '' || empty($_SESSION['chosen_shippingandtax_zipcode']))
	{
		return $_SESSION['apptivo_cart_baginfo'];
	}else {
	$zipcode = $_SESSION['chosen_shippingandtax_zipcode'];
	}	
	}	
	
	if($shipMethod == '' || $shipMethod == 'free_shipping')
	{
		$shipMethod =null;
	}
	$accountId = null;
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$zipcode,"arg4"=>$shipMethod,"arg5"=>$accountId);
	$response = ecommerce_soap_call(SHIPPING_WSDL,'estimateShippingAndTaxRate',$params);
	 if( $response == 'E_100')
	 {
	 	return '';
	 }
	  //if wiill call pickup value is > 0 or Initial Update zipcode
			if(!isset($response->return->shippingAmount))
			{
				if(isset($response->return->ratedShipment))
				{
					$ratedshipment = app_convertObjectToArray($response->return->ratedShipment);
					$response->return->shippingAmount = $ratedshipment[0]->totalCharges;
					$response->return->totalPrice     = ($response->return->totalPrice)+($response->return->shippingAmount);
				}
			}	
		
	return $response;
}

function rated_shippingmethods($shipMethod)
{
	$cart_sessionId = $_SESSION['apptivo_cart_sessionId'];
	$response = estimateShippingAndTaxRate($cart_sessionId,$shipMethod,21050);
	$_SESSION['apptivo_cart_baginfo'] = $response;
	$bag_info = $_SESSION['apptivo_cart_baginfo'];	
	if(isset($bag_info) && !empty($bag_info))
	{
	$ratedShipment = app_convertObjectToArray($bag_info->return->ratedShipment);
	return $ratedShipment;
	}else { return false; }
}
/*
 * Shopping cart session Details without return.
 */
function get_baginfo()
{
	if(isset($_SESSION['apptivo_cart_baginfo'])) 
	{
		return $_SESSION['apptivo_cart_baginfo']->return;
	}
	else { return false; }
	
}
/*
 * Shopping cart session Details with return.
 */
function get_shoppingcrt_baginfo()
{
	if(isset($_SESSION['apptivo_cart_baginfo']))
	{
		return $_SESSION['apptivo_cart_baginfo'];
	}
	else { return false; 
	}
	
}
/**
 * Update Shipping method.
 *
 * @param  $shipmethod
 */
function update_shippingmethods($shipmethod)
{
	$cart_info = $_SESSION['apptivo_cart_baginfo'];
	$shoppingCartLines = app_convertObjectToArray($cart_info->return->shoppingCartLines);
	foreach($shoppingCartLines as $TaxLines)
		{
			if($TaxLines->lineTypeCode == 'TAX')
			{
				$taxName = $TaxLines->lineTypeName;
				if($taxName == '')
				{
					$taxName = 'Tax';
				}
				$taxAmount = $TaxLines->effectiveTotalPrice;
			}
		}
		if($taxAmount == '')
		{
			$taxAmount = 0;
		}
		$cart_info->return->taxAmount = 	$taxAmount;
		
	$ratedShipment = app_convertObjectToArray($cart_info->return->ratedShipment);
	$shipping_update = false;
	
	if(!empty($ratedShipment)) {
	foreach($ratedShipment as $shipment) :
	    if($shipment->firmShippingMethodId == $shipmethod ):
		$cart_info->return->shippingOption = $shipment->firmShippingMethodId;
		$cart_info->return->shippingAmount = $shipment->totalCharges;
		$cart_info->return->totalPrice = $cart_info->return->subTotalAmount + $shipment->totalCharges + $cart_info->return->taxAmount - $cart_info->return->totalDiscountAmount;
		$shipping_update = true;
		endif;
	endforeach;	
	}
	
	if(!$shipping_update) :
	 if($_SESSION['apptivo_checkout_shipping_type'] == 'NO'):
	    $cart_info->return->shippingOption =  NULL;
		$cart_info->return->shippingAmount = 0.00;
		$cart_info->return->totalPrice = $cart_info->return->subTotalAmount + $cart_info->return->taxAmount - $cart_info->return->totalDiscountAmount;
	 endif;
	endif;
	$_SESSION['apptivo_cart_baginfo'] = $cart_info;
	return 	$cart_info;
}
/**
 * Enter description here...
 *
 * @return Boolean
 */
function getCartDetails()
 {
   $sessionId = $_SESSION['apptivo_cart_sessionId'];
   $userId = $_SESSION['apptivo_user_account_id']; 
   $userId = ($userId != '')?$userId:0;

   $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$userId);

   $response = ecommerce_soap_call(CART_WSDL,'getShoppingCartDetails',$params);
   
   if( $response == 'E_100')
   {
  	return 'E_100';
   }
   $_SESSION['apptivo_cart_baginfo'] = $response;	  
   return true;   
}

/**
 * Shopping Cart Sub total Amount
 */	
function cart_subtotal()
{	
	$cart_info = $_SESSION['apptivo_cart_baginfo']->return;
	if( isset($cart_info->subTotalAmount)) :
		return apptivo_ecommerce_price($cart_info->subTotalAmount);
	else :
		return false;
	endif;
	
}

/**
 * eCommerce Register User in Checkout Oage.
*/
function apptivo_create_user($accountName,$password,$posted,$shipping=false) //checkout Page Register
{
   $addressId = null;
   $sessionId = null;
    
   if(!$shipping) { 
		$address1 			= $posted['billing_address'];
		$address2 			= $posted['billing_address-2'];
		$city     			= $posted['billing_city'];
		$postalCode 		= $posted['billing_postcode'];
		$provinceAndState 	= $posted['billing_state'];
		$country    		= $posted['billing_country'];
		$firstName  		= $posted['billing_first_name'];
		$lastName   		= $posted['billing_last_name'];
		$homePhoneNo 		= $posted['billing_phone'];
		$companyName		= $posted['billing_company'];	
    } else{	 
		$address1 			= $posted['register_address'];
		$address2 			= $posted['register_address-2'];
		$city     			= $posted['register_city'];
		$postalCode 		= $posted['register_postcode'];
		$provinceAndState 	= $posted['register_state'];
		$country    		= $posted['register_country'];
		$firstName  		= $posted['register_first_name'];
		$lastName   		= $posted['register_last_name'];
		$homePhoneNo 		= $posted['register_phone'];
		$companyName		= $posted['register_company'];	
    }
	$emailId     		= $accountName;	
	
   $primaryContactDetails = array('address1'=>$address1,'address2'=>$address2,'addressId'=>$addressId,'city'=>$city,'firstName'=>$firstName,'lastName'=>$lastName,
	                               'postalCode'=>$postalCode,'provinceAndState'=>$provinceAndState,'companyName'=>$companyName,'country'=>$country,'homePhoneNo'=>$homePhoneNo);									 
    										 
   $accountUser = array('accountName'=>$emailId,'emailId'=>$emailId,'password'=>$password,'primaryContactDetails'=>$primaryContactDetails,'sessionId'=>$sessionId);

   $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY, "arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY, "arg2"=>$accountUser, "arg3"=>$sessionId);
   
   $response = ecommerce_soap_call(USER_WSDL,'registerUser',$params);
   
   return $response;								
}
/*
 *eCommerce register User in Register class 
*/
function apptivo_register_user($posted)
{
    $sessionId = null;
    $addressId = null; 
	$address1 			= $posted['register_address'];
	$address2 			= $posted['register_address-2'];
	$city     			= $posted['register_city'];
	$postalCode 		= $posted['register_postcode'];
	$provinceAndState 	= $posted['register_state'];
	$country    		= $posted['register_country'];
	$firstName  		= $posted['register_first_name'];
	$lastName   		= $posted['register_last_name'];
	$homePhoneNo 		= $posted['register_phone'];
	$companyName		= $posted['register_company'];
	$emailId     		= $posted['account_username'];
	$password           = $posted['account_password'];
	

	
    $primaryContactDetails = array('address1'=>$address1,'address2'=>$address2,'addressId'=>$addressId,'city'=>$city,'firstName'=>$firstName,'lastName'=>$lastName,
	                                'postalCode'=>$postalCode,'provinceAndState'=>$provinceAndState,'companyName'=>$companyName,'country'=>$country,'homePhoneNo'=>$homePhoneNo);									 
    
    $accountUser = array('accountName'=>$emailId,'emailId'=>$emailId,'password'=>$password,'primaryContactDetails'=>$primaryContactDetails);
    
    $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$accountUser,"arg3"=>$sessionId);
    
    $response = ecommerce_soap_call(USER_WSDL,'registerUser',$params);
    
    return $response;			
}
/**
 * LoginUser
 *
 * @param unknown_type $email as AccountName
 * @param unknown_type $password
 * @return unknown
 */
function apptivo_loginUser($email,$password)
{	
  $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>trim($email),"arg3"=>trim($password),"arg4"=>null,"arg5"=>null);
  $response = ecommerce_soap_call(USER_WSDL,'loginUser',$params);
  return $response;
}
/**
 * ResetPassword.
 *
 * @param unknown_type $email
 * @return unknown
 */
function apptivo_resetpassword($email)
{
  $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>trim($email));
  $response = ecommerce_soap_call(USER_WSDL,'resetPassword',$params);
  return $response;
}
/**
 * update user password.
 *
 * @param unknown_type $email
 * @param unknown_type $password
 * @return unknown
 */
function updateUserPassword($email,$password)
{
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>trim($email),"arg3"=>trim($password));
    $response = ecommerce_soap_call(USER_WSDL,'updateUserPassword',$params);
	return $response;
}
/**
 * apptivo User account Id.
 *
 * @return unknown
 */
function is_apptivo_user_logged_in()
{
  $user_accountid = $_SESSION['apptivo_user_account_id'];  //apptivo Account user_id
  if( isset($user_accountid) && trim($user_accountid) != '') :
  	return $user_accountid;
  else :
  	return false;
  endif;  
}
/**
 * eCommerce Order History ( PayPal Checkout and Authorize.Net )
 */
function apptivo_orderHistory()
{
	$account_id = is_apptivo_user_logged_in();
	$params = array( "arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$account_id);
	$response = ecommerce_soap_call(CART_WSDL,'getAllOrderByFirmIdUserId',$params);
	return $response;
}
/*
 * Shipping Address format
 */
function formatted_shipping_address($shippingaddress)
{
	if(trim($shippingaddress->firstName) != '') :
	$shipto = $shippingaddress->firstName.' '.$shippingaddress->lastName.'<br /> '.$shippingaddress->address1 .' '.$shippingaddress->address2 .' <br />'.$shippingaddress->city .' '.$shippingaddress->country .' ';
		return $shipto;
	else:
		return '';
	endif;
}
/**
 * apptivo account Name.
 *
 * @return unknown
 */
function is_apptivo_account_name_logged_in()
{
  $user_accountid = $_SESSION['apptivo_user_account_name'];  //apptivo Account user_id
  if( isset($user_accountid) && trim($user_accountid) != '') :
 	 return $user_accountid;
  else :
  	return false;
  endif;
}
/**
 * apptivo cart session ID.
 *
 * @return unknown
 */
function is_apptivo_cart_sessionId()
{
  $cart_sessionId = $_SESSION['apptivo_cart_sessionId'];  //apptivo Account user_id
  if( isset($cart_sessionId) && trim($cart_sessionId) != '') :
  	return $cart_sessionId;
  else :
  	return false;
  endif;  
}
/**
 * get apptivo billing ans shipping address.
 *
 * @return unknown
 */
function get_apptivo_billing_shipping_address()
{
	  $apptivo_billandship_address = $_SESSION['apptivo_biillingandshipping_address'];  //apptivo Account user_id
	  if( isset($apptivo_billandship_address) && !empty($apptivo_billandship_address)) :
	  	return $apptivo_billandship_address;
	  else :
	  	return false;
	  endif;  
}
/**
 * Apptivo Paypal token And ID
 *
 * @param unknown_type $pypalreturns
 * @return unknown
 */
function apptivo_paypal_credentials($pypalreturns)
{  
	if(!empty($pypalreturns)) :
	$_SESSION['apptivo_payeridwithtoken'] = $pypalreturns;
		return true;
	else :
		return false;
	endif;
}
/**
 * get Apptivo Payer Id and Token
 *
 * @return unknown
 */
function get_apptivo_payerwithtoken()
{  
	  $payerid_token_amt = $_SESSION['apptivo_paypal_payerid_token_amt'];  //apptivo Account user_id
	  if( isset($payerid_token_amt) && !empty($payerid_token_amt)) :
	  	return $payerid_token_amt;
	  else :
	  	return false;
	  endif;
}
/**
 * set apptivo billing and shipping address
 *
 * @param unknown_type $posted
 * @return unknown
 */
function apptivo_billing_shipping_address($posted)
{
	if($posted[shiptobilling] == 1)
	{
    $posted['shipping_first_name'] 	= $posted['billing_first_name'];
    $posted['shipping_last_name'] 	= $posted['billing_last_name'];
    $posted ['shipping_company']	= $posted['billing_company'];
    $posted['shipping_address'] 	= $posted['billing_address'];
    $posted['shipping_address-2'] 	= $posted['billing_address-2'];
    $posted['shipping_city'] 		= $posted['billing_city'];
    $posted['shipping_postcode'] 	= $posted['billing_postcode'];
    $posted['shipping_country'] 	= $posted['billing_country'];
    $posted['shipping_state'] 		= $posted['billing_state'];
    $posted['shipping_email'] 		= $posted['billing_email'];
    $posted['shipping_phone'] 		= $posted['billing_phone'];
	}
	$_SESSION['apptivo_biillingandshipping_address'] = $posted;
	return true;
}
/**
 * Setup Paypal checkout.
 * @param unknown_type $sessionId
 * @param unknown_type $userId
 * @param unknown_type $returnURL
 * @param unknown_type $Editurl
 * @param unknown_type $Shippingmethodid
 * @return unknown
 */
function setupPaypalCheckout($sessionId,$userId,$Editurl,$Shippingmethodid)
{
   global $apptivo_ecommerce;	
   $return_Url = $apptivo_ecommerce->plugin_url().'/paypalredirect.php';	

   $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$userId,"arg4"=>$return_Url,
                   "arg5"=>$Editurl,"arg6"=>$Shippingmethodid,"arg7"=>'0.0',"arg8"=>null,"arg9"=>null);
   
   $response = ecommerce_soap_call(CART_WSDL,'setupPaypalCheckout',$params);
   
   return $response;
}
/**
 * apptivo confirm create paypal Order.
 *
 * @param unknown_type $sessionId
 * @param unknown_type $userId
 * @param unknown_type $token
 * @param unknown_type $PayerID
 * @param unknown_type $amount
 * @return unknown
 */
function apptivo_paypalorder($sessionId,$userId,$payeridwithtoken)
{   
	$posted = get_apptivo_billing_shipping_address(); // get apptivo billing and shipping address using session.
		
	$token = $payeridwithtoken['token'];
	$PayerID = $payeridwithtoken['payerid'];
	$amount = $payeridwithtoken['amount'];
		
	$firstName    	  = $posted['shipping_first_name'];		  
	$lastName 		  = $posted['shipping_last_name'];
	$companyName	  = $posted['shipping_company'];
	$address1 		  = $posted['shipping_address'];
	$address2 		  = $posted['shipping_address-2'];
	$city 			  = $posted['shipping_city'];
	$provinceAndState = $posted['shipping_state'];
	$postalCode 	  = $posted['shipping_postcode'];
	$homePhoneNo 	  = $posted['shipping_phone'];
	$country 		  = $posted['shipping_country'];
	$emailId 		  = $posted['shipping_email'];
	$type             = 'SHIPPING';
	$giftNote         = $posted['gift_notes'];
    $addressId =NULL;
	$affilateobj = NULL;
    if( trim($lastName) == '' ) {
	$contactObj = NULL;
    } else {
	/*Contact details Object */								  
	$contactObj = array('address1'=>$address1,'address2'=>$address2,'addressId'=>$addressId,'city'=>$city,'firstName'=>$firstName,'lastName'=>$lastName,'type'=>$type,
		                 'emailId'=>$emailId,'postalCode'=>$postalCode,'provinceAndState'=>$provinceAndState,'companyName'=>$companyName,'country'=>$country,'homePhoneNo'=>$homePhoneNo);								  
									  
    }
    $clientIpAddress = stripslashes(get_ClientIpAddr());								  
    
    $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$contactObj,"arg4"=>$token,"arg5"=>$PayerID,
                    "arg6"=>$amount,"arg7"=>$userId,"arg8"=>$giftNote,"arg9"=>$affilateobj,"arg10"=>$clientIpAddress );
    
    $response = ecommerce_soap_call(CART_WSDL,'confirmAndCreateNewPayPalOrder',$params);
	
    if($response->retun->orderId != '' || $response == 'E_100')
	{  
		global $apptivo_ecommerce;
        $apptivo_ecommerce->cart->emty_shopping_cart(); 
	}
	$response->return->payment_method = 'PayPal';
	return $response;
}

/**
 * Secure Checkout Create Order.
 *
 * @param unknown_type $cardType
 * @param unknown_type $creditCartNumber
 * @param unknown_type $expiryMonth
 * @param unknown_type $expiryYear
 * @param unknown_type $cardVerificationValue
 * @param unknown_type $shippingMethodID
 * @param unknown_type $posted
 * @return unknown
 */

function securecheckout($cardType,$creditCartNumber,$expiryMonth,$expiryYear,$cardVerificationValue,$shippingMethodID,$posted)
{
	    $cart_sessionId = $_SESSION['apptivo_cart_sessionId'];
	    $userId = $_SESSION['apptivo_user_account_id']; 
	    $billfname = $posted['billing_first_name'];
	    $billlname = $posted['billing_last_name'];
	    $paymentMethod = "1";
	    $giftNote         = $posted['gift_notes'];
	    //Billing contact Details.
	    
	    $baddress1 			= $posted['billing_address'];
		$baddress2 			= $posted['billing_address-2'];
		$baddressId 		= NULL;
		$bcity 				= $posted['billing_city'];
		$bfirstName 		= $posted['billing_first_name'];
		$blastName 			= $posted['billing_last_name'];
		$bpostalCode 		= $posted['billing_postcode'];
		$bprovinceAndState  = $posted['billing_state'];
		$bcompanyName		= $posted['billing_company'];
		$bcountry 			= $posted['billing_country'];
		$bhomePhoneNo		= $posted['billing_phone'];
		$btype				= 'BILLING';
		$bemailId           = $posted['billing_email'];
			
		$selectedBillingAddress = array('address1'=>$baddress1,'address2'=>$baddress2,'addressId'=>$baddressId,'city'=>$bcity,'firstName'=>$bfirstName,'lastName'=>$blastName,
	                                     'emailId'=>$bemailId,'type'=> $btype,'postalCode'=>$bpostalCode,'provinceAndState'=>$bprovinceAndState,'companyName'=>$bcompanyName,'country'=>$bcountry,'homePhoneNo'=>$bhomePhoneNo);
			
		$shipping_firstName = apptivo_ecommerce_clean($posted['shipping_first_name']);
		
		if( trim($shipping_firstName) == '' ) 
		{
		    $selectedShippingAddress = null;
		}else {
			
			$saddress1 			= $posted['shipping_address'];
			$saddress2 			= $posted['shipping_address-2'];
			$saddressId 		= NULL;
			$scity 				= $posted['shipping_city'];
			$sfirstName 		= $posted['shipping_first_name'];
			$slastName 			= $posted['shipping_last_name'];
			$spostalCode 		= $posted['shipping_postcode'];
			$sprovinceAndState  = $posted['shipping_state'];
			$scompanyName		= $posted['shipping_company'];
			$scountry 			= $posted['shipping_country'];
			$shomePhoneNo		= $posted['shipping_phone'];
			$stype				= 'SHIPPING';
			$semailId           = $posted['shipping_email'];
			
			$selectedShippingAddress = array('address1'=>$saddress1,'address2'=>$saddress2,'addressId'=>$saddressId,'city'=>$scity,'firstName'=>$sfirstName,'lastName'=>$slastName,
	                                         'emailId'=>$semailId,'type'=> $stype,'postalCode'=>$spostalCode,'provinceAndState'=>$sprovinceAndState,'companyName'=>$scompanyName,'country'=>$scountry,'homePhoneNo'=>$shomePhoneNo);	
        }
        
		$clientIpAddress = stripslashes(get_ClientIpAddr());
		//Payment Details.
		$shippingAmount = '0.0';
        $payment = array("creditCardCode"=>$cardVerificationValue, "creditCardHolderName"=>NULL, "creditCardNumber"=>$creditCartNumber, "creditCardType"=>$cardType,
                         "expiryMonth"=>$expiryMonth,"expiryYear"=>$expiryYear, "creditCardId"=>NULL, "firstName" =>$billfname, "middleName"=>NULL,
                         "lastName"=>$billlname, "providerName"=>NULL, "paymentType"=>null, "paymentInstrumentNumber"=>null );
        
        //Create Order Object.									  
		$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$cart_sessionId,"arg3"=>$selectedBillingAddress,"arg4"=>$selectedShippingAddress,
                         "arg5"=>$payment,"arg6"=>$shippingMethodID,"arg7"=>$shippingAmount,"arg8"=>$giftNote,"arg9"=>$clientIpAddress,"arg10"=>$userId,"arg11"=>$paymentMethod);

        $response = ecommerce_soap_call(CART_WSDL,'createOrder',$params);
        
        if($response == 'E_100')
		{
			return 'E_100';
		}
		if($response->retun->orderId != '' || $response == 'E_100')
		{  
			global $apptivo_ecommerce;
	        $apptivo_ecommerce->cart->emty_shopping_cart();		
		}
		$response->return->payment_method = 'Secure checkout';
		$_SESSION['apptivo_cart_response'] = $response;
		return $response;
}

/**
 * addItems in Shopping Cart.
 *
 * @param unknown_type $cart_contents
 * @param unknown_type $promotionCodes
 * @param unknown_type $calculateZipcode
 * @param unknown_type $zipcode
 * @return Boolean( TRUE / FALSE )
 */

function singleitem_addtocart($item_id,$item_uomid,$item_qty,$item_price,$itemcolor='',$itemsize='',$p_id='')
{

 	$apptivo_cart_sessionid = trim($_SESSION['apptivo_cart_sessionId']);		
	$sessionId = ($apptivo_cart_sessionid != '' ) ? $apptivo_cart_sessionid : null;
	$userId = NULL;
	$clientIPAddress = stripslashes(get_ClientIpAddr());
	$promotionCodes = NULL;
	$shippingOption = NULL;
	
	$shoppingCartLines = array('itemId'=>$item_id,'unitPrice'=>$item_price,'uomId'=>$item_uomid,'quantity'=>$item_qty,'lineTypeCode'=>'CART','color'=>$itemcolor,'size'=>$itemsize );
    
	$shoppingCart = array('sessionId'=>$sessionId, 'promotionCode'=>$promotionCodes, 'shoppingCartLines'=>$shoppingCartLines, 'shippingOption'=>$shippingOption);
		                                   
    $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$shoppingCart,
                    "arg4"=>$clientIPAddress,"arg5"=>'N',"arg6"=>$userId );
    
    $add_ons = apply_filters('apptivo_ecommerce_add_ons_single_addcart',$p_id);
    
    if( $add_ons == 'Error')
    {
      global $apptivo_ecommerce;
	  $apptivo_ecommerce->add_error( __('You cannot add this product to the Shopping Cart.Please select other item.', 'apptivo-ecommerce') );
	  return false; // Failed in add items in cart.
    }
    $response = ecommerce_soap_call(CART_WSDL,'addItems',$params);
    $cart_sessionId = $response->return->sessionId;
     //Shopping cart informations are maintained in session.
   	 if($cart_sessionId != '') {
	   $_SESSION['apptivo_cart_sessionId'] = $cart_sessionId;
	   $_SESSION['apptivo_cart_baginfo']   = $response;
   	 }
	  
      if( $response->return->methodResponse->responseStatus == 1 && !empty($response->return->shoppingCartLines) ) {
	  return true; // Shopping cart item has been added successfully. [responseCode] => CS-001	  
      } else{
	  global $apptivo_ecommerce;
	  $apptivo_ecommerce->add_error( __('You cannot add that product to the Shopping Cart.Please try again later.', 'apptivo-ecommerce') );
	  return false; // Failed in add items in cart.
      }	
}

/**
 * Add items to cart
 *
 * @param unknown_type $item_ids
 * @param unknown_type $item_qtys
 * @param unknown_type $p_id
 * @return unknown
 */
function additems_cart($sessionId,$item_ids,$item_qtys,$p_id='')
{
 	$userId = NULL;
	$clientIPAddress = stripslashes(get_ClientIpAddr());
	$promotionCodes = NULL;
	$shippingOption = NULL;

	$shoppingCartLines = array ();
	for($i = 0; $i < count ( $item_ids ); $i ++) {
		
		$item_id = get_post_meta($item_ids[$i],'_apptivo_item_id',true);		
		$item_uomid = get_post_meta($item_ids[$i],'_apptivo_item_uom_id',true);
		$item_price = get_post_meta($item_ids[$i],'_apptivo_sale_price',true);
		$item_qty = $item_qtys[$i];
		$shoppingCartLines[$i] = array('itemId'=>$item_id,'unitPrice'=>$item_price,'uomId'=>$item_uomid,'quantity'=>$item_qty,'lineTypeCode'=>'CART');
	}
	
    
	$shoppingCart = array('sessionId'=>$sessionId, 'promotionCode'=>$promotionCodes, 'shoppingCartLines'=>$shoppingCartLines, 'shippingOption'=>$shippingOption);
		                                   
    $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$shoppingCart,
                    "arg4"=>$clientIPAddress,"arg5"=>'N',"arg6"=>$userId );
    
   
    $add_ons = apply_filters('apptivo_ecommerce_add_ons_single_addcart',$p_id);
    
    if( $add_ons == 'Error')
    {
      global $apptivo_ecommerce;
	  $apptivo_ecommerce->add_error( __('You cannot add this product to the Shopping Cart.Please select other item.', 'apptivo-ecommerce') );
	  return false; // Failed in add items in cart.
    }
    
    $response = ecommerce_soap_call(CART_WSDL,'addItems',$params);
    
    $cart_sessionId = $response->return->sessionId;
     //Shopping cart informations are maintained in session.
   	 if($cart_sessionId != '') {
	   $_SESSION['apptivo_cart_sessionId'] = $cart_sessionId;
	   $_SESSION['apptivo_cart_baginfo']   = $response;
   	 }
	  
      if( $response->return->methodResponse->responseStatus == 1 && !empty($response->return->shoppingCartLines) ) {
	  return true; // Shopping cart item has been added successfully. [responseCode] => CS-001	  
      } else{
	  global $apptivo_ecommerce;
	  $apptivo_ecommerce->add_error( __('You cannot add that product to the Shopping Cart.Please try again later.', 'apptivo-ecommerce') );
	  return false; // Failed in add items in cart.
      }	
}
/*
 * Remove tht items from cart
 */
function deleteItem($cartLineid)
{
	//UserId
	$userId = $_SESSION['apptivo_user_account_id'];
	$userId = (isset($userId))?$userId:0;
	$zipcode = NULL;
	//Cart sessionId.
	$sessionId = $_SESSION['apptivo_cart_sessionId'];
		
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$cartLineid,"arg4"=>$zipcode,"arg5"=>"N","arg6"=>$userId );
	
    $response = ecommerce_soap_call(CART_WSDL,'deleteItem',$params);
    if( $response == 'E_100')
    {
    	return false;
    }
    $_SESSION['apptivo_cart_baginfo']   = $response;			
	return true;		
}


/**
 * Update Shopping cart 
 */
function update_shopping_cart($CartLines='',$item_qtys='',$promotionCodes=null,$calculateZipcode = 'yes',$zipcode='',$updatestatus='yes',$shipping_method=null,$itemcolor='',$itemsize='')
{
		$shippingOption = $shipping_method;//$_POST['hidden_shippingoption']; //Hidden Shippinh Method Name.
        $shippingOption = ($shippingOption != '' ) ? $shippingOption : null;        
	    $shoppingCart = null;
		$shoppingCartLines = array ();		
		$apptivo_cart_sessionid = trim($_SESSION['apptivo_cart_sessionId']);		
		$sessionId = ($apptivo_cart_sessionid != '' ) ? $apptivo_cart_sessionid : null;
		$clientIPAddress = stripslashes(get_ClientIpAddr());
		$userId = NULL;
		
		if($updatestatus == 'yes') :		
		for($i = 0; $i < count ( $CartLines ); $i ++) {
          if( $CartLines[$i]->lineTypeCode == 'CART' ) {

          	$shoppingCartLine[$i] = array('itemId'=> $CartLines[$i]->itemId,'uomId'=>$CartLines[$i]->uomId,'quantity'=>$item_qtys[$i],'lineTypeCode'=>'CART','color'=>$itemcolor[$i],'size'=>$itemsize[$i] );							
			array_push ( $shoppingCartLines, $shoppingCartLine[$i] ); 					

          } //if( $CartLines[$i]->lineTypeCode == 'CART' )			
		}
		
		$ship_to_postal_code = null;

		$shoppingCart = array('sessionId'=>$sessionId, 'promotionCode'=>$promotionCodes,'shipToPostalCode'=>$ship_to_postal_code, 'shoppingCartLines'=>$shoppingCartLines, 'shippingOption'=>$shippingOption);
        
		$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$sessionId,"arg3"=>$shoppingCart,
                    "arg4"=>$clientIPAddress,"arg5"=>'N',"arg6"=>$userId );
		
	    $response = ecommerce_soap_call(CART_WSDL,'updateItems',$params);

		if( $response == 'E_100')
		{
		  	return 'E_100';
		}
        $_SESSION['apptivo_cart_baginfo'] = $response;
        
	    $sessionId = $response->return->sessionId;
	    
	    endif;

	    if($sessionId != '')
		   {
		   $_SESSION['apptivo_cart_sessionId'] = $sessionId;

           //Calculate shipping and tax usin Zipcode.	
              
			   	if($zipcode != '')
				   { 
				   	$response = estimateShippingAndTaxRate($sessionId,$shipping_method,$zipcode);		//Calculate ratedShipment
				   }else{ 
				   	if($shipping_method != ''):
				   	$response = update_shippingmethods($shipping_method);
				   	endif;
				   }
			  
		   }
	   
	   //Set Tax Amounts	   
	   $shoppingCartLines = app_convertObjectToArray($response->return->shoppingCartLines);
       foreach($shoppingCartLines as $TaxLines)
		{
			if($TaxLines->lineTypeCode == 'TAX')
			{
				$taxName = $TaxLines->lineTypeName;
				if($taxName == '')
				{
					$taxName = 'Tax';
				}
				$taxAmount = $TaxLines->effectiveTotalPrice;
			}
		}
		if($taxAmount == '')
		{
			$taxAmount = 0;
		}
		$response->return->taxAmount = 	$taxAmount;
		$_SESSION['apptivo_cart_baginfo']   = $response;	
		return $response;
}
/*
 * eCommerce search Item
 */
function searchitems($query,$maxItemCount,$fromIndex,$sortBy = 0)
{
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$query,"arg3"=>$fromIndex,"arg4"=>$maxItemCount,"arg5"=>$sortBy);
	$response = ecommerce_soap_call(INDEX_ITEM_WSDL,'searchItems',$params);
	if( $response == 'E_100')
	{
	 return '';
	}
	$itemDetails = $response->return->itemDetailsDocuments;
	return array(app_convertObjectToArray($itemDetails),$response->return->totalHits);
}
/*
 * eCommerce Product category lists
 */
function all_product_category()
{
       $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY);	
       $response = ecommerce_soap_call(INDEX_ITEM_WSDL,'getCategoryTree',$params);
        if( $response == 'E_100')
		{
		 return 'E_100';
		}
		return 	$response->return;	
		
}
/*
 * Get Itm ByID ( Product Description Page )
 */
function getItemById($itemId) {
	   $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2" => $itemId);		
	   $response = ecommerce_soap_call(INDEX_ITEM_WSDL,'getItemById',$params);	
	   if( $response == 'E_100')
		{
		 return 'E_100';
		}
       return 	$response->return;	
}

/* get AllItem For sync */ 
function getAllItemsForSync(){
	 $params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY);
	 $response = ecommerce_soap_call(INDEX_ITEM_WSDL,'getAllItemsForSync',$params);	
	 if( $response == 'E_100')
	 {
	 	return 'E_100';
	 }
	 $itemDetails = $response->return->itemDetailsDocuments;        
	 return array(app_convertObjectToArray($itemDetails),$response->return->totalHits);
}
/**
 * getAllItems / searchItemsByCategory
 * @param string $categoryId
 * @param string $maxcount
 * @param string $fromIndex
 * @param string $sortBy
 * @return itemDetailsDocuments and totalHits
 */
function app_getItemsByCategoryId($categoryId, $maxcount, $fromIndex, $sortBy = 0)
{   
	
		  if($sortBy == 0) :    
		    $sort_option = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_products_sorting_type'));
		  else:
		    $sort_option = $sortBy;
		  endif;
	    
	    if($categoryId == '' || strlen($categoryId) == 0 ):
	    	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>$fromIndex,"arg3" => $maxcount,"arg4"=>$sort_option);
	    	$webmethod = 'getAllItems';
	    else:
	    	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1"=>APPTIVO_ECOMMERCE_ACCESSKEY,"arg2" => $categoryId,"arg3"=>$fromIndex,"arg4" => $maxcount,"arg5"=>$sort_option);
	    	$webmethod = 'searchItemsByCategory';
	    endif;
	    		
	    $response = ecommerce_soap_call(INDEX_ITEM_WSDL,$webmethod,$params);
	    if ( $response == 'E_100') //error in response
	    {
	    	return array('',0); 
	    }
		$itemDetails = $response->return->itemDetailsDocuments;
		return array(app_convertObjectToArray($itemDetails),$response->return->totalHits);
}
/*
 * getOrderDetailsByOrderNumber(String siteAuthenticationKey, String orderNumber, boolean getNotes)
 */
function get_order_details($orderno,$getnodes=false)
{
	$params = array("arg0"=>APPTIVO_ECOMMERCE_API_KEY,"arg1" => APPTIVO_ECOMMERCE_ACCESSKEY, "arg2" => $orderno,"arg3" => $getnodes);
	$response = ecommerce_soap_call(CART_WSDL,'getOrderDetailsByOrderNumber',$params);
	return $response;
}
/*
 * Pagination
 */
function products_pagination($reload, $page, $tpages,$adjacents=2) {
	$prevlabel = "&lsaquo; Prev";
	$nextlabel = "Next &rsaquo;";
	$out = "<div class=\"pagin\">\n";
	// previous
	if($page==1) {
		$out.= "<span>" . $prevlabel . "</span>\n";
	}
	elseif($page==2) {
		$out.= "<a href=\"" . $reload . "&amp;page=" . ($page-1) . "\">" . $prevlabel . "</a>\n";
	}
	else {
		$out.= "<a href=\"" . $reload . "&amp;page=" . ($page-1) . "\">" . $prevlabel . "</a>\n";
	}
	// first
	if($page>($adjacents+1)) {
		$out.= "<a href=\"" . $reload . "\">1</a>\n";
	}
	// interval
	if($page>($adjacents+2)) {
		$out.= "...\n";
	}
	// pages
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<span class=\"current\">" . $i . "</span>\n";
		}
		elseif($i==1) {
			$out.= "<a href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a>\n";
		}
		else {
			$out.= "<a href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a>\n";
		}
	}
	// interval
	if($page<($tpages-$adjacents-1)) {
		$out.= "...\n";
	}
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<a href=\"" . $reload . "&amp;page=" . $tpages . "\">" . $tpages . "</a>\n";
	}
	// next
	if($page<$tpages) {
		$out.= "<a href=\"" . $reload . "&amp;page=" . ($page+1) . "\">" . $nextlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $nextlabel . "</span>\n";
	}
	$out.= "</div>";
	return $out;
}

/* Products post pagination */
function products_posts_pagination($post_url, $page, $tpages,$page_qry='paged',$adjacents=2) {
	$prevlabel = "&lsaquo; Prev";
	$nextlabel = "Next &rsaquo;";
	$out = "<div class=\"pagin\">\n";
	
	if( $page == 0 ) $page = 1;
	
	// previous
	if($page==1) {
		$out.= "<span>" . $prevlabel . "</span>\n";
	}
	elseif($page==2) {
		$reload = add_query_arg($page_qry, ($page-1), $post_url);
		$out.= "<a href=\"" . $reload . "\">" . $prevlabel . "</a>\n";
	}
	else {
		$reload = add_query_arg($page_qry, ($page-1), $post_url);
		$out.= "<a href=\"" .$reload. "\">" . $prevlabel . "</a>\n";
	}
	// first
	if($page>($adjacents+1)) {
		$out.= "<a href=\"" . $reload . "\">1</a>\n";
	}
	// interval
	if($page>($adjacents+2)) {
		$out.= "...\n";
	}
	// pages
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<span class=\"current\">" . $i . "</span>\n";
		}
		elseif($i==1) {
			$reload = add_query_arg($page_qry, $i, $post_url);
			$out.= "<a href=\"" . $reload . "\">" . $i . "</a>\n";
		}
		else {
			$reload = add_query_arg($page_qry, $i, $post_url);
			$out.= "<a href=\"" . $reload . "\">" . $i . "</a>\n";
		}
	}
	// interval
	if($page<($tpages-$adjacents-1)) {
		$out.= "...\n";
	}
	// last
	if($page<($tpages-$adjacents)) {
		$reload = add_query_arg($page_qry, $tpages, $post_url);
		$out.= "<a href=\"" . $reload . "\">" . $tpages . "</a>\n";
	}
	// next
	if($page<$tpages) {
		$reload = add_query_arg($page_qry, ($page+1), $post_url);
		$out.= "<a href=\"" .$reload . "\">" . $nextlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $nextlabel . "</span>\n";
	}
	$out.= "</div>";
	return $out;
}
/**
 * Convert Object to Array().
 *
 * @param unknown_type $objectValue
 * @return unknown
 */
function app_convertObjectToArray($objectValue)
{
	if(is_array($objectValue)) {
		$arrayValue = $objectValue;
	}
	else {
		$arrayValue = array();
		array_push($arrayValue,$objectValue);
	}
	return $arrayValue;
}
/**
 * get postid from postmeta key;
 *
 * @param unknown_type $meta_key
 * @param unknown_type $meta_value
 * @return unknown
 */
function getIdFromMeta( $meta_key, $meta_value ) {
    global $wpdb;
    $pid = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %d AND meta_key = '$meta_key' ORDER BY post_id DESC",$meta_value) );
    if( $pid != '' )
        return $pid;
    else 
        return false;
}
/**
 * apptivo_ecommerce Product Thumbnail
 **/
if (!function_exists('get_product_thumbnail')) {
	function get_product_thumbnail($post_id, $size = 'product_catalog', $placeholder_width = 0, $placeholder_height = 0 ) {
		global $apptivo_ecommerce;
		if (!$placeholder_width) $placeholder_width = $apptivo_ecommerce->get_image_size('product_catalog_image_width');
		if (!$placeholder_height) $placeholder_height = $apptivo_ecommerce->get_image_size('product_catalog_image_height');
		if ( has_post_thumbnail($post_id) )
		{
			$attr = array(
			'alt'	=> trim(strip_tags( get_the_title($post_id))),
			'title'	=> trim(strip_tags( get_the_title($post_id))),
		    );
		
			return get_the_post_thumbnail($post_id, $size,$attr); 
		}
		else 
		{
			$catalog_noproduct_img = apply_filters('apptivo_ecommerce_catalog_noproduct_img',$apptivo_ecommerce->plugin_url(). '/assets/images/no-product-150.jpg');			
			return '<img src="'.$catalog_noproduct_img.'" title="'.get_the_title($post_id).'" alt="Placeholder" width="'.$placeholder_width.'" height="'.$placeholder_height.'" />';
		}
	}

}
function cvv2_validate($cc_number,$cvv2,$cart_type)
{
	$cart_type = strtolower(trim($cart_type));
	if ($cart_type == 'ax') 
	{ 
		if (!preg_match("/^\d{4}$/", $cvv2)) 
		{ 
			 return false;
		 }
		 else { 
		 	return true;
		 }
	 } else {
	 	if (!preg_match("/^\d{3}$/", $cvv2)) { 
	 		return false;
	 	 } else {
	 	 	return true;
	 	 }
	 } 
}
function checkCreditCard ($cardnumber, $cardname, &$errornumber='', &$errortext='') {

  $cards = array (  array ('name' => 'AX','length' => '15','prefixes' => '34,37','checkdigit' => true),                  
                    array ('name' => 'DI','length' => '16','prefixes' => '6011,622,64,65','checkdigit' => true),
                    array ('name' => 'MC','length' => '16','prefixes' => '51,52,53,54,55','checkdigit' => true),
                    array ('name' => 'VI','length' => '16','prefixes' => '4','checkdigit' => true)                  
                  );

  $ccErrorNo = 0;
  $ccErrors [0] = "Unknown card type";
  $ccErrors [1] = "No card number provided";
  $ccErrors [2] = "Credit card number has invalid format";
  $ccErrors [3] = "Credit card number is invalid";
  $ccErrors [4] = "Credit card number is wrong length";
  // Establish card type
  $cardType = -1;
  for ($i=0; $i<sizeof($cards); $i++) {
    // See if it is this card (ignoring the case of the string)
    if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
      $cardType = $i;
      break;
    }
  }
  // If card type not found, report an error
  if ($cardType == -1) {
     $errornumber = 0;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  // Ensure that the user has provided a credit card number
  if (strlen($cardnumber) == 0)  {
     $errornumber = 1;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  // Remove any spaces from the credit card number
  $cardNo = str_replace (' ', '', $cardnumber);  
  // Check that the number is numeric and of the right sort of length.
  if (!preg_match("/^[0-9]{13,20}$/",$cardNo))  {
     $errornumber = 2;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  } 
  // The credit card is in the required format.
  return true;
}
/**
 * Credit CardValidation.
 *
 * @param unknown_type $cardnumber
 * @param unknown_type $cardname
 * @param unknown_type $errornumber
 * @param unknown_type $errortext
 * @return unknown
 */
function checkCreditCard_number($cardnumber, $cardname, &$errornumber='', &$errortext='') {

  $cards = array (  array ('name' => 'AX','length' => '15','prefixes' => '34,37','checkdigit' => true),                  
                    array ('name' => 'DI','length' => '16','prefixes' => '6011,622,64,65','checkdigit' => true),
                    array ('name' => 'MC','length' => '16','prefixes' => '51,52,53,54,55','checkdigit' => true),
                    array ('name' => 'VI','length' => '16','prefixes' => '4','checkdigit' => true)                  
                  );
  $ccErrorNo = 0;
  $ccErrors [0] = "Unknown card type";
  $ccErrors [1] = "No card number provided";
  $ccErrors [2] = "Credit card number has invalid format";
  $ccErrors [3] = "Credit card number is invalid";
  $ccErrors [4] = "Credit card number is wrong length";
  // Establish card type
  $cardType = -1;
  for ($i=0; $i<sizeof($cards); $i++) {
    // See if it is this card (ignoring the case of the string)
    if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
      $cardType = $i;
      break;
    }
  }
  // If card type not found, report an error
  if ($cardType == -1) {
     $errornumber = 0;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  // Ensure that the user has provided a credit card number
  if (strlen($cardnumber) == 0)  {
     $errornumber = 1;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  // Remove any spaces from the credit card number
  $cardNo = str_replace (' ', '', $cardnumber);  
   
  // Check that the number is numeric and of the right sort of length.
  if (!preg_match("/^[0-9]{13,19}$/",$cardNo))  {
     $errornumber = 2;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  // Now check the modulus 10 check digit - if required
  if ($cards[$cardType]['checkdigit']) {
    $checksum = 0;                                  // running checksum total
    $mychar = "";                                   // next char to process
    $j = 1;                                         // takes value of 1 or 2
  
    // Process each digit one by one starting at the right
    for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {
      // Extract the next digit and multiply by 1 or 2 on alternative digits.      
      $calc = $cardNo{$i} * $j;
      // If the result is in two digits add 1 to the checksum total
      if ($calc > 9) {
        $checksum = $checksum + 1;
        $calc = $calc - 10;
      }
      // Add the units element to the checksum total
      $checksum = $checksum + $calc;
      // Switch the value of j
      if ($j ==1) {$j = 2;} else {$j = 1;};
    } 
    // All done - if checksum is divisible by 10, it is a valid modulus 10.
    // If not, report an error.
    if ($checksum % 10 != 0) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
    }
  }  
  // The following are the card-specific checks we undertake.
  // Load an array with the valid prefixes for this card
  $prefix = explode(',',$cards[$cardType]['prefixes']);
  // Now see if any of them match what we have in the card number  
  $PrefixValid = false; 
  for ($i=0; $i<sizeof($prefix); $i++) {
    $exp = '/^' . $prefix[$i] . '/';
    if (preg_match($exp,$cardNo)) {
      $PrefixValid = true;
      break;
    }
  }
      
  // If it isn't a valid prefix there's no point at looking at the length
  if (!$PrefixValid) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
    
  // See if the length is valid for this card
  $LengthValid = false;
  $lengths = explode(',',$cards[$cardType]['length']);
  for ($j=0; $j<sizeof($lengths); $j++) {
    if (strlen($cardNo) == $lengths[$j]) {
      $LengthValid = true;
      break;
    }
  }
  
  // See if all is OK by seeing if the length was valid. 
  if (!$LengthValid) {
     $errornumber = 4;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  };   
  
  // The credit card is in the required format.
  return true;
}
/**
 * Credit card Expire date Validations.
 *
 * @param unknown_type $expire_year
 * @param unknown_type $expire_month
 * @return unknown
 */
function expire_date($expire_year,$expire_month)
{
	$current_month = date("m");
	$current_year = date("Y");
    if ($expire_year > $current_year) { 
		// Valid  date
		return true;
	}else if ($expire_year < $current_year) { 
		// Invalid date
		return false;
	} else { 
		// Check if the same year,
		 // if so, make sure month is current or later 
		 if ($expire_year == $current_year) 
		 { 
		 	if ($expire_month < $current_month)
		 	 { 
		 	 	// Invalid date
		 	 	return false;
		 	 } else {
		 	 	// Valid date 
		 	 	return true;
		 	 
		 	 } 
		 }
		
	 } 
}
/*
 * Create Item and Update Item
 */
function createorupdateitem($webMethod,$itemId,$itemManufacturerId,$itemCode,$itemName,$itemDescription,$regularPrice,$salePrice,$itemShortDescription,
    							   $skuNumber,$trackSizes,$trackColors,$enabledForSales,$SSubscription,$currencyCode,$item_categoryID,$supplierName,$isFeatured,$post_id)
{	
	$idxItemDetails = array('currencyCode' => $currencyCode, 'enabledForSales' => $enabledForSales, 'itemCode' => $itemCode, 'itemManufacturerId' => $itemManufacturerId,'itemId'=>$itemId,
                            'itemName' => $itemName, 'itemDescription'=>$itemDescription, 'itemShortDescription'=>$itemShortDescription, 'regularPrice' => $regularPrice, 'salePrice'=> $salePrice,
                            'SSubscription'=> $SSubscription,'skuNumber'=>$skuNumber,'trackSizes'=>$trackSizes, 'trackColors'=>$trackColors, 'supplierName'=>$supplierName,'featured'=>$isFeatured  );
    
    $params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=> $idxItemDetails,"arg3"=>$item_categoryID);
    $response = ecommerce_soap_call(ITEM_WSDL,$webMethod,$params);
    //Delete post ( product ) from wp if the response is exception and web method is createItem
    if( $response == 'E_100' && $webMethod  == 'createItem' ){
        $apptivo_ecommerce_errors = array();
        $apptivo_ecommerce_errors[] = 'Error in adding items';
        update_option('apptivo_ecommerce_errors', $apptivo_ecommerce_errors);
        wp_delete_post( $post_id, true );
        wp_safe_redirect(get_admin_url().'post-new.php?post_type=item');
        exit;
		}
    return $response;
}
/*
 * get eCommerce Suppliers
 */
function getSupliers()
{
	$params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY );
	$response = ecommerce_soap_call(ITEM_WSDL,'getSupliers',$params);
	return $response;
	
}
/**
 * Order Number Configuration
 *
 * @param  Prefix
 * @param  Order Number Stars with
 */
function configureOrderNumberGeneration($prefix,$startsWidth)
{
	$params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=>true,"arg3"=>$prefix,"arg4"=>$startsWidth);
    $response = ecommerce_soap_call(CART_WSDL,'configureOrderNumberGeneration',$params);
    return $response;
}
/**
 * Get Next Order Number..
 */
function getNextOrderNumber()
{
	$params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY);
    $response = ecommerce_soap_call(CART_WSDL,'getNextOrderNumber',$params);
	return $response;
}
/**
 * Credit card Cvv code validations.
*/
function cvv2_validate_number($cc_number,$cvv2)
{
	$first_number = substr($cc_number, 0, 1);
	if ($first_number == 3) 
	{ 
		if (!preg_match("/^\d{4}$/", $cvv2)) 
		{ 
			 return false;
		 }
		 else { 
		 	return true;
		 }
	 } else {
	 	if (!preg_match("/^\d{3}$/", $cvv2)) { 
	 		return false;
	 	 } else {
	 	 	return true;
	 	 }
	 } 
}
function apptivo_wp_insert_term( $term, $taxonomy, $args = array(),$apptivo_catid='' ) {
	global $wpdb;
	if ( ! taxonomy_exists($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	$args['name'] = $term;
	$args['taxonomy'] = $taxonomy;
	$args = sanitize_term($args, $taxonomy, 'db');
	extract($args, EXTR_SKIP);
	$name = stripslashes($name);
	$description = stripslashes($description);
	if ( empty($slug) )
		$slug = sanitize_title($name);
	$term_group = 0;
	if ( $alias_of ) {
		$alias = $wpdb->get_row( $wpdb->prepare( "SELECT term_id, term_group FROM $wpdb->terms WHERE slug = %s", $alias_of) );
		if ( $alias->term_group ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} else {
			// The alias isn't in a group, so let's create a new one and firstly add the alias term to it.
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms") + 1;
			$wpdb->update($wpdb->terms, compact('term_group'), array('term_id' => $alias->term_id) );
		}
	}
		// This term does not exist at all in the database, Create it.
		$slug = wp_unique_term_slug($slug, (object) $args);
		if ( false === $wpdb->insert( $wpdb->terms, compact( 'name', 'slug', 'term_group' ) ) )
			return new WP_Error('db_insert_error', __('Could not insert term into the database'), $wpdb->last_error);
		$term_id = (int) $wpdb->insert_id;
	// Seems unreachable, However, Is used in the case that a term name is provided, which sanitizes to an empty string.
	if ( empty($slug) ) {
		$slug = sanitize_title($slug, $term_id);		
		$wpdb->update( $wpdb->terms, compact( 'slug' ), compact( 'term_id' ) );		
	}
	$tt_id = $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $taxonomy, $term_id ) );
	if ( !empty($tt_id) )
		return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);

	$wpdb->insert( $wpdb->term_taxonomy, compact( 'term_id', 'taxonomy', 'description', 'parent') + array( 'count' => 0 ) );
	$tt_id = (int) $wpdb->insert_id;
	clean_term_cache($term_id, $taxonomy);
	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

function apptivo_wp_update_term( $term_id, $taxonomy, $args = array() ) {

	global $wpdb;
	if ( ! taxonomy_exists($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	$term_id = (int) $term_id;

	// First, get all of the original args
	$term = get_term ($term_id, $taxonomy, ARRAY_A);

	if ( is_wp_error( $term ) )
		return $term;

	// Escape data pulled from DB.
	$term = add_magic_quotes($term);

	// Merge old and new args with new args overwriting old ones.
	$args = array_merge($term, $args);

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	$args = sanitize_term($args, $taxonomy, 'db');
	extract($args, EXTR_SKIP);
    
	// expected_slashed ($name)
	$name = stripslashes($name);
	$description = stripslashes($description);

	if ( '' == trim($name) )
		return new WP_Error('empty_term_name', __('A name is required for this term'));

	$empty_slug = false;
	if ( empty($slug) ) {
		$empty_slug = true;
		$slug = sanitize_title($name);
	}

	if ( $alias_of ) {
		$alias = $wpdb->get_row( $wpdb->prepare( "SELECT term_id, term_group FROM $wpdb->terms WHERE slug = %s", $alias_of) );
		if ( $alias->term_group ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} else {
			// The alias isn't in a group, so let's create a new one and firstly add the alias term to it.
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms") + 1;
			do_action( 'edit_terms', $alias->term_id );
			$wpdb->update( $wpdb->terms, compact('term_group'), array( 'term_id' => $alias->term_id ) );
			do_action( 'edited_terms', $alias->term_id );
		}
	}

	// Check $parent to see if it will cause a hierarchy loop
	$parent = apply_filters( 'wp_update_term_parent', $parent, $term_id, $taxonomy, compact( array_keys( $args ) ), $args );

	// Check for duplicate slug
	$id = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM $wpdb->terms WHERE slug = %s", $slug ) );
	if ( $id && ($id != $term_id) ) {
		// If an empty slug was passed or the parent changed, reset the slug to something unique.
		// Otherwise, bail.
		if ( $empty_slug || ( $parent != $term['parent']) )
			$slug = wp_unique_term_slug($slug, (object) $args);
		else
			return new WP_Error('duplicate_term_slug', sprintf(__('The slug &#8220;%s&#8221; is already in use by another term'), $slug));
	}
	
	$wpdb->update($wpdb->terms, compact( 'name', 'slug', 'term_group' ), compact( 'term_id' ) );
	if ( empty($slug) ) {
		$slug = sanitize_title($name, $term_id);
		$wpdb->update( $wpdb->terms, compact( 'slug' ), compact( 'term_id' ) );
	}
	

	$tt_id = $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $taxonomy, $term_id) );
	
	$wpdb->update( $wpdb->term_taxonomy, compact( 'term_id', 'taxonomy', 'description', 'parent' ), array( 'term_taxonomy_id' => $tt_id ) );
	$term_id = apply_filters('term_id_filter', $term_id, $tt_id);
	clean_term_cache($term_id, $taxonomy);
	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

 function apptivo_shorten($string, $wordsreturned)
    {
        $retval = $string;
        $array = explode(" ", $string);
        if (count($array)<=$wordsreturned){
            $retval = $string;
        }
        else {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array);
        }
        return $retval;
    }
    
function amount_format($amount=0)
    {
    	return number_format($amount, 2, '.', '');
    }
/**
 * Get Client IP Addtess
 *
 * @return IP
 */
function get_ClientIpAddr()
{
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
}
/**
 * To Make eCommerce Soap Call
 *
 * @param string $wsdl
 * @param $webmethod
 * @param $params
 * @return $response/Exceptions
 */
function ecommerce_soap_call($wsdl,$webmethod,$params)
{
	$webmethod = trim($webmethod);
	$wsdl = trim($wsdl); 
	try{
    $client = new SoapClient($wsdl);
    }catch(Exception $e){
    	return 'E_100';   //Invalid Wsdl
    }
	try{
		do_action('apptivo_ecommerce_request_'.$webmethod,$params); //do_actions for before request
		$response = $client->__soapCall($webmethod,array($params)); //make soap call	
		do_action('apptivo_ecommerce_response_'.$webmethod,$response);//do_actions for after response
	}catch(Exception $e){
		do_action('apptivo_ecommerce_exceptions_'.$webmethod,$e->getMessage());//do_actions for getting Exceptions
        return 'E_100';       	
	}
	return $response;	
}