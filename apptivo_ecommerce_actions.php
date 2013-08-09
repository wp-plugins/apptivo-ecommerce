<?php
/**
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */

//Products Sorting
add_action('wp_ajax_apptivo_ecommerce_products_sorting', 'apptivo_ecommerce_products_sorting');
add_action('wp_ajax_nopriv_apptivo_ecommerce_products_sorting', 'apptivo_ecommerce_products_sorting');
function apptivo_ecommerce_products_sorting(){
	$categoryID = $_POST['category_id'];
    $pageNo = $_POST['page_no'];
    $sort_option = $_POST['sort_option'];
    $_SESSION['apptivo_ecommerce_sort_type'] = $sort_option;
    $count_posts = get_option('apptivo_ecommerce_products_per_page');
    if($count_posts == '' ) : $count_posts = 8; endif;
	$from_index = ($pageNo*$count_posts) - ($count_posts-1);
	list($item_lists,$total_items_in_apptivo) = app_getItemsByCategoryId($categoryID,$count_posts,$pageNo, $sort_option);
	$apptivo_ecommerce_loop['loop'] = 0;
      ?>
      <ul class="items" id="item_lists">
	<?php
	   $reagular_price_option = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_enable_regular_price')); 
       foreach($item_lists as $items_details) :      
       
       if( $items_details->trackColors == '' && $items_details->trackSizes == '' )
       {
         $track_size_color = false;
       }else {
        $track_size_color = true;
       }
      
        if ($items_details->qatt <= 0 && $items_details->itemSaleECommInventoryConstrained == 'true') {
        	$qatt = true; 
        }else { $qatt = false; }
       
       $product_postid = getIdFromMeta( '_apptivo_item_id', $items_details->itemId );
       if( $product_postid != '') {
       	
       if ($items_details->qatt <= 0 && $items_details->itemSaleECommInventoryConstrained == 'true') {
        	$qatt = true;        	
       }else { $qatt = false; }
        $_product = &new apptivo_ecommerce_product( $product_postid );
        $apptivo_ecommerce_loop['loop']++;	
		?>
		<li class="product <?php if ($apptivo_ecommerce_loop['loop']%4==0) echo 'last'; if (($apptivo_ecommerce_loop['loop']-1)%4==0) echo 'first'; ?>">
			<?php do_action('apptivo_ecommerce_before_products_loop_item'); ?>
				
			<div class="apptivo_product_image">
			<a href="<?php echo get_permalink($product_postid); ?>">
			<?php echo get_product_thumbnail($product_postid); ?>
			</a>
			</div>
						
			   <!-- Price  -->
			   <span class="price">			       
				   <?php if($items_details->itemEffectivePrice == '') { $items_details->itemEffectivePrice = '0.00'; }?>
				   <?php echo apptivo_ecommerce_price($items_details->itemEffectivePrice);?>				   
			   </span>
			  <!-- Item Name -->
			  <div class="item_name"><a title="<?php echo $items_details->itemName; ?>" href="<?php echo get_permalink($product_postid); ?>"><?php echo apptivo_ecommerce_itemname(trim($items_details->itemName));?></a></div>
			<?php
			//add to cart button.
			$item_lists = $product_postid."+".$items_details->itemId."+".$items_details->itemPrimaryUOMId."+1+".$items_details->itemEffectivePrice; // For posting Purpose... Format : itemID+itemUomID+itemqty+itemPrice
			apptivo_ecommerce_add_to_cart($product_postid, $_product,$item_lists,$track_size_color,$qatt);
			?>			
		</li>
		<?php 
       }
		endforeach;
		?></ul>
      <?php 
	  exit;
}

/*Recaptcha */
add_action('wp_ajax_apptivo_ecommerce_captcha_refresh', 'apptivo_ecommerce_captcha_refresh');
add_action('wp_ajax_nopriv_apptivo_ecommerce_captcha_refresh', 'apptivo_ecommerce_captcha_refresh');
function apptivo_ecommerce_captcha_refresh()
{
global $apptivo_ecommerce;
?>
<?php 
$characters_on_image = 6;
$possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
$cphcacode = '';
$i = 0;
while ($i < $characters_on_image) { 
$cphcacode .= substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
$i++;
}
$_SESSION['apptvo_ecommerce_captacha_code'] = $cphcacode;
?>
<img src="<?php echo $apptivo_ecommerce->plugin_url();?>/captcha_code_file.php?cphcacode=<?php echo $cphcacode; ?>" id='captchaimg' style="margin-left:10%;border:1px solid #000;">
<?php 
die();
	
}

/* apptivo eCommerce update shipping method */
add_action('wp_ajax_apptivo_ecommerce_update_shipping_method', 'apptivo_ecommerce_ajax_update_shipping_method');
add_action('wp_ajax_nopriv_apptivo_ecommerce_update_shipping_method', 'apptivo_ecommerce_ajax_update_shipping_method');

function apptivo_ecommerce_ajax_update_shipping_method() {
	check_ajax_referer( 'update-shipping-method', 'security' );
	
	if (isset($_POST['shipping_method'])) $_SESSION['_chosen_shipping_method'] = $_POST['shipping_method'];
	$shipMethod =  $_SESSION['_chosen_shipping_method'];
	if( $_POST['type'] == 'shipping' || $_POST['shipping_type'] == 'shipping' ):
		$_SESSION['apptivo_checkout_shipping_type'] = 'YES';
	else:
	    $_SESSION['apptivo_checkout_shipping_type'] = 'NO';
	endif;
	update_shippingmethods($shipMethod); // Update apptivo Shipping Methods in cart Page
	
	apptivo_ecommerce_cart_totals();
	die();
}

/**
 * Apptivo eCommerce update order review.
 */
add_action('wp_ajax_apptivo_ecommerce_update_order_review', 'apptivo_ecommerce_ajax_update_order_review');
add_action('wp_ajax_nopriv_apptivo_ecommerce_update_order_review', 'apptivo_ecommerce_ajax_update_order_review');

function apptivo_ecommerce_ajax_update_order_review() {		
	$shoppingCart_Lines = get_baginfo()->shoppingCartLines;
	$shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines);
	 
	if (empty($shoppingCartLines[0])) :
		echo '<p class="error">'.__('Sorry, your session has expired.', 'apptivo-ecommerce').' <a href="'.home_url().'">'.__('Return to homepage &rarr;', 'apptivo-ecommerce').'</a></p>';
		die();
	endif;
	do_action('apptivo_ecommerce_checkout_update_order_review', $_POST['post_data']);
	update_shippingmethods($_POST['shipping_method']); // Update apptivo Shipping Methods in checkout Page.
	
	if($_POST['pg_type'] == 'paypalcheckout'):
	do_action('apptivo_ecommerce_checkout_order_review'); // Display review order table
	else:
	do_action('apptivo_ecommerce_order_review'); // Display review order table
	endif;
	
	die();
}




/**
 * Apptivo cCommerce update shipping tax.
 */
add_action('wp_ajax_apptivo_ecommerce_update_shipping_tax', 'apptivo_ecommerce_ajax_update_shipping_tax');
add_action('wp_ajax_nopriv_apptivo_ecommerce_update_shipping_tax', 'apptivo_ecommerce_ajax_update_shipping_tax');

function apptivo_ecommerce_ajax_update_shipping_tax() {
	if (isset($_POST['shipping_method'])) $_SESSION['_chosen_shipping_method'] = $_POST['shipping_method'];
	if($_POST['s_postcode'] != 'Postcode') :
	$estimate_shipp_tax = estimateShippingAndTaxRate($_SESSION['apptivo_cart_sessionId'],$_POST['shipping_method'],$_POST['s_postcode']);
	$_SESSION['apptivo_cart_baginfo'] = $estimate_shipp_tax;
	endif;
	
	if($_POST['pg_type'] == 'paypalcheckout'):
	do_action('apptivo_ecommerce_checkout_order_review'); // Display review order table
	else:
	do_action('apptivo_ecommerce_order_review'); // Display review order table
	endif;
	die();
}


/**
 * Single item ti add in shopping cart.
 */
add_action('wp_ajax_apptivo_ecommerce_add_to_cart', 'apptivo_ecommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_apptivo_ecommerce_add_to_cart', 'apptivo_ecommerce_ajax_add_to_cart');

function apptivo_ecommerce_ajax_add_to_cart() {
	
	global $apptivo_ecommerce;
	
	check_ajax_referer( 'add-to-cart', 'security' );
	
	$product_itemlists =  $_POST['product_id']; // get Product itm lists Format: itemId+itemUOMId+itemQty+itemPrice
    $product_itemlists = explode('+',$product_itemlists);
    
	$product_id             = $product_itemlists[0]; //wordpress Product id.
	$apptivo_itemid         = $product_itemlists[1]; //apptivo item ID
	$apptivo_item_uomid     = $product_itemlists[2]; //apptivo itemPrimaryUOMId
	$apptivo_item_qty       = $product_itemlists[3]; //apptivo item Quantity
	$apptivo_item_price     = $product_itemlists[4]; //apptivo item Effective Price.
	
	if( empty($apptivo_item_qty))
	{
		$apptivo_item_qty = 1;
	}

	apply_filters('apptivo_ecommerce_add_ons_addcart', $product_id);
	
	//single Item add to Cart in apptivo
	$apptivo_addtocart = singleitem_addtocart($apptivo_itemid,$apptivo_item_uomid,$apptivo_item_qty,$apptivo_item_price,'','',$product_id);
	if ($apptivo_addtocart) :		
		$data = apply_filters('add_to_cart_fragments', array());				
	else :
		// Return error
		$data = array(
			'error' => $apptivo_ecommerce->errors[0]
		);
		$apptivo_ecommerce->clear_messages();
	endif;
	   
	echo json_encode( $data );
	
	die();
}

/**
 * Remove item from cart / Update shopping cart.
 **/
add_action( 'init', 'apptivo_ecommerce_update_cart_action' );

function apptivo_ecommerce_update_cart_action() {
	
	global $apptivo_ecommerce;
	
	// Remove from cart
	if ( isset($_GET['remove_item']) && is_numeric($_GET['remove_item']) ) :	
	    $shoppingCartLineId = $_GET['remove_item'];
	   $delete_items =  deleteItem($shoppingCartLineId);//Delete item from shopping cart
	   
	   
	    if( $delete_items )
	    {
	    $delete_message = apptivo_ecommerce_error_message('DE-100');
		$apptivo_ecommerce->add_message( __($delete_message, 'apptivo-ecommerce') );
	    }else{
	    $delete_message = apptivo_ecommerce_error_message('DE-101');
	    $apptivo_ecommerce->add_error( __($delete_message, 'apptivo-ecommerce') );
	    }
		
		if ( isset($_SERVER['HTTP_REFERER'])) :
			wp_safe_redirect($_SERVER['HTTP_REFERER']);
			exit;
		endif;
	
	// Update Cart
	elseif (isset($_POST['update_cart']) && $_POST['update_cart'] && (trim($_POST['shop_cart_actions']) == '')) :
		$update_message = apptivo_ecommerce_error_message('UE-100');
		$apptivo_ecommerce->add_message( __($update_message, 'apptivo-ecommerce') );		
	endif;

}

/**
 * Add to cart in Single Product.
 **/
add_action( 'init', 'apptivo_ecommerce_add_items_in_cart' );

function apptivo_ecommerce_add_items_in_cart() {
	
	global $apptivo_ecommerce;
 	if (empty($_GET['add-items-to-cart'])) return;
 	$item_ids = explode(' ',$_GET['add-items-to-cart']);
 	$apptivo_update_items = singleitem_addtocart($item_ids[1],$item_ids[2],1,$item_ids[4],'','',$item_ids[0]);
	if($apptivo_update_items) {
		
		$update_message = apptivo_ecommerce_error_message('UE-101');
		$apptivo_ecommerce->add_message( __($update_message, 'apptivo-ecommerce') );
		
	}
	
   if ( $apptivo_ecommerce->error_count() == 0) {
		wp_safe_redirect( $apptivo_ecommerce->cart->shopping_cart_url() );
		exit;
	}
	// Otherwise redirect to where they came
	elseif ( isset($_SERVER['HTTP_REFERER'])) {
		wp_safe_redirect($_SERVER['HTTP_REFERER']);
		exit;
	}
	// If all else fails redirect to root
	else {
		wp_safe_redirect(home_url());
		exit;
	}	
}

add_action( 'init', 'apptivo_ecommerce_add_to_cart_action' );

function apptivo_ecommerce_add_to_cart_action() {
	
	global $apptivo_ecommerce;
 	if (empty($_GET['add-to-cart']) || !$apptivo_ecommerce->verify_nonce('add_to_cart', '_GET')) return;
    $item_ids = explode(' ',$_GET['add-to-cart']);
 	if (is_numeric($item_ids[0])) :
	  if( !is_numeric($_POST['quantity']) || $_POST['quantity'] <= 0) :
	        $error_message = apptivo_ecommerce_error_message('AE-103');
	   		$apptivo_ecommerce->add_error( __($error_message, 'apptivo-ecommerce') );
	  else : 
			//single product
			$quantity = (isset($_POST['quantity'])) ? (int) $_POST['quantity'] : 1;			
			$item_color = $_POST['colors'];
			$item_size = $_POST['sizes'];			
			// Add to the cart
			$apptivo_update_items = singleitem_addtocart($item_ids[1],$item_ids[2],$quantity,$item_price,$item_color,$item_size,'','',$item_ids[0]);
			if($apptivo_update_items) :
			    $update_message = apptivo_ecommerce_error_message('UE-101');
				$apptivo_ecommerce->add_message( __($update_message, 'apptivo-ecommerce') );
			endif;
			
	   endif;	
	endif;
	
   if ( $apptivo_ecommerce->error_count() == 0) {
		wp_safe_redirect( $apptivo_ecommerce->cart->shopping_cart_url() );
		exit;
	}
	// Otherwise redirect to where they came
	elseif ( isset($_SERVER['HTTP_REFERER'])) {
		wp_safe_redirect($_SERVER['HTTP_REFERER']);
		exit;
	}
	// If all else fails redirect to root
	else {
		wp_safe_redirect(home_url());
		exit;
	}	
}
/**
 * Process the login form
 **/
add_action('init', 'apptivo_ecommerce_process_login');
 
function apptivo_ecommerce_process_login() {
	
	global $apptivo_ecommerce;
	
	/** Login User **/
	if (isset($_POST['login']) && $_POST['login']) :
		
		if ( !isset($_POST['username']) || empty($_POST['username']) )
		{
			$apptivo_ecommerce->add_error( __('Email Address is required.', 'apptivo-ecommerce'),'username' );
		}else{
			if ( !is_email(trim($_POST['username'])) ) $apptivo_ecommerce->add_error( __('Invalid Email Address', 'apptivo-ecommerce'),'username' );
		}
		if ( !isset($_POST['password']) || empty($_POST['password']) ) $apptivo_ecommerce->add_error( __('Password is required.', 'apptivo-ecommerce'),'password' );
		
		if ($apptivo_ecommerce->error_count()==0) :
			
			$creds = array();
			$creds['user_login'] = $_POST['username'];
			$creds['user_password'] = $_POST['password'];
			$user_login = apptivo_loginUser($creds['user_login'],$creds['user_password']);
			
			if( $user_login->return->methodResponse->responseStatus != 1) :
				$apptivo_ecommerce->add_error( $user_login->return->methodResponse->responseMessage );
			else :
			    $_SESSION['apptivo_user_account_id'] = $user_login->return->accountId;
			    $_SESSION['apptivo_user_account_name'] = $user_login->return->accountName;
			    
				if ( isset($_SERVER['HTTP_REFERER'])) :
					wp_safe_redirect($_SERVER['HTTP_REFERER']);
					exit;
				endif;
				
				wp_redirect(get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')));
				exit;
			endif;
			
		endif;
	
	endif;	
	
	/** Reset Password **/
	if (isset($_POST['last_pwd']) && $_POST['last_pwd']) :	
		$apptivo_ecommerce->verify_nonce('last_pwd');
		$account_username= apply_filters('apptivo_ecommerce_account_username_label','Email address');
		if (empty($_POST['account_username']) )
		{
			$apptivo_ecommerce->add_error( __($account_username. ' is required.', 'apptivo-ecommerce'),'account_username' );
		}else {
		if (!is_email(trim($_POST['account_username']))) $apptivo_ecommerce->add_error( __('Invalid '.$account_username, 'apptivo-ecommerce') ,'account_username');
		}
		if ($apptivo_ecommerce->error_count()==0) :
			$account_username = $_POST['account_username'];	
			$reset_pwd = apptivo_resetpassword($account_username);
			if ($reset_pwd->return == "AS-021"){
				$apptivo_ecommerce->add_error ( "Could not locate any user registered with the specified email address. Check your user name." );
			} else if ($reset_pwd->return == "AS-019"){
				$apptivo_ecommerce->add_message( "Password has been reset successfully and sent in mail." );
			} else if ($reset_pwd->return == "AS-020"){
				$apptivo_ecommerce->add_error ( "Action was failed.Please try again after 10 mins." );
			} else if($reset_pwd == 'E_100'){
				$apptivo_ecommerce->add_error ( "Action was failed.Please try again after 10 mins." );
			}     
		endif;
	
	endif;	
	
}

/**
 * Process ajax checkout form
 */
add_action('wp_ajax_apptivo_ecommerce-checkout', 'apptivo_ecommerce_process_checkout');
add_action('wp_ajax_nopriv_apptivo_ecommerce-checkout', 'apptivo_ecommerce_process_checkout');

function apptivo_ecommerce_process_checkout () {
	global $apptivo_ecommerce, $apptivo_ecommerce_checkout;
    if(get_option('apptivo_ecommerce_demo_store') == 'yes')
	{
		$apptivo_ecommerce->add_error( __("This is a demo site for testing purposes. Sorry! you can't place order.", 'apptivo_ecommerce') );
		$apptivo_ecommerce->show_messages();//display error/message
	    die(0);	
     }
	include_once($apptivo_ecommerce->plugin_path() . '/classes/checkout.class.php');
	$apptivo_ecommerce_checkout = &new apptivo_ecommerce_checkout();
	$apptivo_ecommerce_checkout->process_checkout();
	die(0);
}

/*Confirm checkout */
add_action('wp_ajax_apptivo_ecommerce-confirm-checkout', 'apptivo_ecommerce_confirm_checkout');
add_action('wp_ajax_nopriv_apptivo_ecommerce-confirm-checkout', 'apptivo_ecommerce_confirm_checkout');

function apptivo_ecommerce_confirm_checkout() {
	global $apptivo_ecommerce, $apptivo_ecommerce_checkout;
    if(get_option('apptivo_ecommerce_demo_store') == 'yes')
	{
		$apptivo_ecommerce->add_error( __("This is a demo site for testing purposes. Sorry! you can't place order.", 'apptivo_ecommerce') );
		$apptivo_ecommerce->show_messages();//display error/message
	    die(0);	
     }
	include_once($apptivo_ecommerce->plugin_path() . '/classes/checkout.class.php');
	$apptivo_ecommerce_checkout = &new apptivo_ecommerce_checkout();
	$apptivo_ecommerce_checkout->process_confirm_checkout();
	die(0);
}

/*PayPal Checkout */
add_action('wp_ajax_apptivo_ecommerce-paypal-checkout', 'apptivo_ecommerce_process_paypal_checkout');
add_action('wp_ajax_nopriv_apptivo_ecommerce-paypal-checkout', 'apptivo_ecommerce_process_paypal_checkout');

function apptivo_ecommerce_process_paypal_checkout () {
	global $apptivo_ecommerce, $apptivo_ecommerce_checkout;
	if(get_option('apptivo_ecommerce_demo_store') == 'yes')
	{
		$apptivo_ecommerce->add_error( __("This is a demo site for testing purposes. Sorry! you can't place order.", 'apptivo_ecommerce') );
		$apptivo_ecommerce->show_messages();//display error/message
	    die(0);	
     }
				
	include_once($apptivo_ecommerce->plugin_path() . '/classes/checkout.class.php');
	$apptivo_ecommerce_checkout = &new apptivo_ecommerce_checkout();
	$apptivo_ecommerce_checkout->process_paypal_checkout();
	die(0);
}

//eCommerce Reset Password Action
add_action('wp_ajax_apptivo_ecommerce_pwd_reset_action', 'apptivo_ecommerce_pwd_reset_action');
add_action('wp_ajax_nopriv_apptivo_ecommerce_pwd_reset_action', 'apptivo_ecommerce_pwd_reset_action');
function apptivo_ecommerce_pwd_reset_action()
{   
	global $apptivo_ecommerce;
	$email = trim($_POST['email_address']);
	//Email Address validation
       if (empty($email) )
		{
			$apptivo_ecommerce->add_error( __(' Email Address is required.', 'apptivo-ecommerce'),'account_username' );
		}else {
		if (!is_email(trim($email))) $apptivo_ecommerce->add_error( __('Invalid Email Address', 'apptivo-ecommerce') ,'account_username');
		}
	//reset password processing.
	if ($apptivo_ecommerce->error_count()==0) {	
	$reset_pwd = apptivo_resetpassword($email);
	if ($reset_pwd->return == "AS-021"){
				$apptivo_ecommerce->add_error ( "Could not locate any user registered with the specified email address. Check your user name." );
			} else if ($reset_pwd->return == "AS-019"){
				echo 'AS-019';die(0);
				$apptivo_ecommerce->add_message( "Password has been reset successfully and sent in mail." );
			} else if ($reset_pwd->return == "AS-020"){
				$apptivo_ecommerce->add_error ( "Action was failed.Please try again after 10 mins." );
			} else if($reset_pwd == 'E_100'){
				$apptivo_ecommerce->add_error ( "Action was failed.Please try again after 10 mins." );
			} 
	}
	$apptivo_ecommerce->show_messages();
	die(0);	
}

//eCommerce change password
add_action('wp_ajax_apptivo_ecommerce_pwd_change_action', 'apptivo_ecommerce_pwd_change_action');
add_action('wp_ajax_nopriv_apptivo_ecommerce_pwd_change_action', 'apptivo_ecommerce_pwd_change_action');
function apptivo_ecommerce_pwd_change_action()
{
	global $apptivo_ecommerce;
	$password = trim($_POST['password']);
	$re_password = trim($_POST['re_password']);
	$user_account_name = is_apptivo_account_name_logged_in();
	
	if ( strlen($password) >= 8 ) { //Password atleast minimum 8 charactes
		if ( $password == $re_password ) { //To check passwrd is mismatch
		$update_password = updateUserPassword($user_account_name,$password);
		if($update_password->return =='AS-015') //Password updated successfully
		 {  
		 	$error_message_as015 = apptivo_ecommerce_error_message('AS-015');
			$apptivo_ecommerce->add_message( __($error_message_as015, 'apptivo-ecommerce') );
		 }else if( $update_password == 'E_100'){  
			$error_message_ae105 = apptivo_ecommerce_error_message('AE-105');
			$apptivo_ecommerce->add_error( __($error_message_ae105, 'apptivo-ecommerce') );
		}else {
			$error_message_ae106 = apptivo_ecommerce_error_message('AE-106');
			$apptivo_ecommerce->add_error( __($error_message_ae106, 'apptivo-ecommerce') );
		}	
		} else{ //password is mismatch
			$error_message_ae107 = apptivo_ecommerce_error_message('AE-107');
			$apptivo_ecommerce->add_error( __($error_message_ae107, 'apptivo-ecommerce') );
		}
				
	} else{///Password atleast minimum 8 charactes
		if(strlen($password) != 0 )
		{ 
		$error_message_ae124 = apptivo_ecommerce_error_message('AE-124');
		$apptivo_ecommerce->add_error( __($error_message_ae124, 'apptivo-ecommerce') );
		}else { 
		$error_message_ae108 = apptivo_ecommerce_error_message('AE-108');
		$apptivo_ecommerce->add_error( __($error_message_ae108, 'apptivo-ecommerce') );
		}
	}
	$apptivo_ecommerce->show_messages();//display error/message
	die(0);				 
}

/*Products page custom permalink */
if (get_option( 'permalink_structure' )=="") add_action( 'init', 'apptivo_ecommerce_item_page_archive_redirect' );
function apptivo_ecommerce_item_page_archive_redirect() {
	if ( isset($_GET['page_id']) && $_GET['page_id'] == get_option('apptivo_ecommerce_products_page_id') ) :
		wp_safe_redirect( get_post_type_archive_link('item') );
		exit;
	endif;
}