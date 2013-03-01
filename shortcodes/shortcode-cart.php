<?php
/**
 * Cart Shortcode
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
*/
function get_apptivo_ecommerce_cart() {	
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_cart'))
	{
	return custom_apptivo_ecommerce_cart();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_cart');
	}
}
function apptivo_ecommerce_cart() {
	global $apptivo_ecommerce;
	$validation = &new apptivo_ecommerce_validation();
	$getcartDetails = getCartDetails(); //To get Shopping cart details.
	
	//shoppingCartLines
	$shooping_cart = get_shoppingcrt_baginfo();
	$shoppingCart_Lines =$shooping_cart->return;	
	$shoppingCartLines = app_convertObjectToArray($shoppingCart_Lines->shoppingCartLines);	
	 // Process Discount Codes
	 $apply_coupan_code = false;
	if (isset($_POST['apply_coupon']) ||  ( trim($_POST['shop_cart_actions']) == 'APPLY_COUPON' )) :
		$coupon_code = stripslashes(trim($_POST['coupon_code']));
		$apply_coupan_code = true;	
	else: 
	    $coupon_code = $shooping_cart->return->promotionCode;
	endif;
	
	
	 if(!empty($_POST)):
	  if(isset($_POST['apply_zipcode']) || ( trim($_POST['shop_cart_actions']) == 'APPLY_ZIPCODE' ))
	  {
	  	 if(trim(strlen($_POST['zip_code'])) == 0 ):
	  	    $error_message_ae100 = apptivo_ecommerce_error_message('AE-100');
	  	 	$apptivo_ecommerce->add_error(  __($error_message_ae100, 'apptivo_ecommerce') );
	  	 elseif (!$validation->zipcode_isvalid( trim($_POST['zip_code']), 'US' )) :
	  	     $error_message_ae101 = apptivo_ecommerce_error_message('AE-101');
			 $apptivo_ecommerce->add_error($error_message_ae101);
	     endif; 
	  }
	

		   
	  $item_qtys = $_POST['cart_item_qty'];
	  $action = true; // To chk Item Quantity is invalid or not.
	  foreach ($item_qtys as $qty)
	  {
	  	if(!is_numeric($qty) || $qty < 1 ) :
		  	 $action = false;
		  	 $error_message_ae103 = apptivo_ecommerce_error_message('AE-103');
		  	 $apptivo_ecommerce->add_error(  __($error_message_ae103, 'apptivo_ecommerce') );
	  	 break;
	  	endif;
	  }	 
	 endif;
	 
	 
	 if ( $apptivo_ecommerce->error_count() == 0 ) :
	
	 
	 if( !isset($_SESSION['apptivo_checkout_shipping_type'])) :
	 	$_SESSION['apptivo_checkout_shipping_type'] = 'YES';
	 endif;
	 
	if(!empty($_POST) && $action ):
	
	
	   if(trim($_POST['shippingoptions']) == 'shipping'):
		 $shipping_method = trim($_POST['shipping_method']);
		 $_SESSION['apptivo_checkout_shipping_type'] = 'YES';
		else:
		 $shipping_method = trim($_POST['wiicallpickup_enabled']);
		 $_SESSION['apptivo_checkout_shipping_type'] = 'NO';
		endif;
		
		
	 if(isset($_POST['apply_zipcode']) || ( trim($_POST['shop_cart_actions']) == 'APPLY_ZIPCODE' )) {
	 
			$_SESSION['chosen_shippingandtax_zipcode'] = trim($_POST['zip_code']);
			$item_qtys = $_POST['cart_item_qty'];
			$item_color = $_POST['cart_track_color'];
		    $item_size = $_POST['cart_track_size'];		
		    $shooping_cart = update_shopping_cart($shoppingCartLines,$item_qtys,$coupon_code,'yes',trim($_POST['zip_code']),'yes',$shipping_method,$item_color,$item_size);
			$shoppingCartLines = app_convertObjectToArray($shooping_cart->return->shoppingCartLines);	
							
	 }else {
				    
		    $item_qtys = $_POST['cart_item_qty'];
		    $item_color = $_POST['cart_track_color'];
		    $item_size = $_POST['cart_track_size'];
			$shooping_cart = update_shopping_cart($shoppingCartLines,$item_qtys,$coupon_code,'yes',NULL,'yes',$shipping_method,$item_color,$item_size);
			$shoppingCartLines = app_convertObjectToArray($shooping_cart->return->shoppingCartLines);

            if($_POST['proceed_to_checkout'] == 'paypalcheckout'){
                $_SESSION['apptivo_ecommerce_PG'] = 'paypal';
				wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_checkout_page_id')) );
				exit;
            }elseif($_POST['proceed_to_checkout']  == 'securecheckout'){
		        $_SESSION['apptivo_ecommerce_PG'] = 'secure';
		  	    wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_secure_checkout_page_id')) );
				exit;
            }elseif($_POST['proceed_to_checkout']  == 'googlecheckout'){
			    $_SESSION['apptivo_ecommerce_PG'] = 'google';
			    $page = get_permalink(get_option('apptivo_ecommerce_checkout_page_id'));
				wp_safe_redirect($page );
				exit;
            }
		    
		    
	 }
	 
	endif; //if(!empty($_POST) && $action )
	
	//RatedShipment
	$available_methods = app_convertObjectToArray($shoppingCart_Lines->ratedShipment);
	
	if($available_methods == '' || empty($available_methods[0])) {
		
		if($zipcode_disp_status = 'yes' && $auto_zipcode != '') {
			$shooping_cart = update_shopping_cart($shoppingCartLines,$item_qtys,$coupon_code,$zipcode_disp_status,$auto_zipcode,'no');
		}
		
		$available_methods = $shooping_cart->return->ratedShipment;
	}
	
	endif; // if ( $apptivo_ecommerce->error_count() == 0 )
	
        //if wiill call pickup value is > 0 or Initial Update zipcode
			if(!isset($shooping_cart->return->shippingAmount))
			{
				if(isset($shooping_cart->return->ratedShipment))
				{
					$ratedshipment = app_convertObjectToArray($shooping_cart->return->ratedShipment);
					$shooping_cart->return->shippingAmount = $ratedshipment[0]->totalCharges;
					$shooping_cart->return->totalPrice     = ($shooping_cart->return->totalPrice)+($shooping_cart->return->shippingAmount);
				}
			}

	$promotionCode = $shooping_cart->return->promotionCode;
	if($apply_coupan_code){
    if(strlen(trim($promotionCode)) != 0 )
	{
		
		$error_message_ae104 = $shooping_cart->return->errorMessage;
		if(trim($_POST['coupon_code']) != trim($promotionCode) )
		{
			 $apptivo_ecommerce->add_error( 'The Coupon code  "'.$promotionCode.'" is already set in your cart.' );  
		}else if( strlen(trim($error_message_ae104)) != 0  ) {
			$apptivo_ecommerce->add_error(  __($error_message_ae104, 'apptivo_ecommerce') );
		}else{
			$error_message_ae128 = apptivo_ecommerce_error_message('AE-128');
			$apptivo_ecommerce->add_message($error_message_ae128);//Coupon code updated successfully
		}
	}else{
		if(strlen(trim($promotionCode)) != 0  ) {
			 $apptivo_ecommerce->add_error( 'The Coupon code  "'.$promotionCode.'" is already set in your cart.' );  	
		}else if(trim(strlen($_POST['coupon_code'])) == 0 )
			{  
				$error_message_ae102 = apptivo_ecommerce_error_message('AE-102');
				$apptivo_ecommerce->add_error(  __($error_message_ae102, 'apptivo_ecommerce') );
			}else{
				$error_message_ae104 = apptivo_ecommerce_error_message('AE-104');
		        $apptivo_ecommerce->add_error(  __($error_message_ae104, 'apptivo_ecommerce') );
			}
		}
	}
	
	$apptivo_ecommerce->show_messages();
	
	if (empty($shoppingCartLines[0])) :
   	    $apptivo_ecommerce->cart->emty_shopping_cart();//clear the cart session.
		do_action('apptivo_ecommerce_cart_is_empty');
		echo '<p class="empty_cart_msg">'.apply_filters('apptivo_ecommerce_cart_empty_message','Your cart is Empty    ').'<a class="empty_cart_btn" href="'.get_permalink(get_option('apptivo_ecommerce_products_page_id')).'">'.apply_filters('apptivo_ecommerce_return_to_products_label','&larr; Continue Shopping').'</a></p>';
		return;
	endif;
	?>
<!--  Shopping Cart Form Details  -->	
<form action="<?php echo esc_url( $apptivo_ecommerce->cart->shopping_cart_url() ); ?>" method="post">
<input type="hidden" id="shop_cart_actions" name="shop_cart_actions" value="" />
<?php echo '<div style="float:right;margin-bottom:10px;" class="">
           <a class="btn" href="'.get_permalink(get_option('apptivo_ecommerce_products_page_id')).'"><span>'.apply_filters('apptivo_ecommerce_continue_shopping_label','&larr; Continue shopping').'</span></a></div>
           <p style="margin:0px;padding:0px;clear:both;">            
           </p>'; ?>	
	<table  class="shop_table cart shopingcart_page" cellspacing="0">
		<thead>
			<tr>
				<th class="product-thumbnail"></th>
				<th class="product-name"><span class="nobr"><?php _e('Product Name', 'apptivo_ecommerce'); ?></span></th>
				<th class="product-price"><span class="nobr"><?php _e('Unit Price', 'apptivo_ecommerce'); ?></span></th>
				<th class="product-quantity"><?php _e('Quantity', 'apptivo_ecommerce'); ?></th>
				<th class="product-subtotal"><?php _e('Price', 'apptivo_ecommerce'); ?></th>
				<th class="product-remove"></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$itm_qty = 0;
			foreach ($shoppingCartLines as $cartLines) :
				    $product_postid = getIdFromMeta('_apptivo_item_id', $cartLines->itemId);
				    $cart_lineid = $cartLines->shoppingCartLineId;
					$item_quantity = $cartLines->quantity;
					if ($item_quantity>0) :
					?>
						<tr>
							<td class="product-thumbnail">
								<a href="<?php echo esc_url( get_permalink($product_postid) ); ?>">
								<?php 
									if (has_post_thumbnail($product_postid)) :
									    $attr = array( 'alt'	=> trim(strip_tags( get_the_title($product_postid))),
			                                           'title'	=> trim(strip_tags( get_the_title($product_postid))) 
									                  );
										echo get_the_post_thumbnail($product_postid, 'product_thumbnail',$attr); 
									else :
									     $thumbnail_noproduct_image = apply_filters('apptivo_ecommerce_thumbnail_noproduct_img',$apptivo_ecommerce->plugin_url().'/assets/images/no-product-90.jpg');
										echo '<img src="'.$thumbnail_noproduct_image. '"  title="'.get_the_title($product_postid).'"  alt="Placeholder" width="82" height="'.$apptivo_ecommerce->get_image_size('product_thumbnail_image_height').'" />'; 
									endif;
								?>
								</a>
							</td>
							<td class="product-name">
								<a href="<?php echo esc_url( get_permalink($product_postid) ); ?>"><?php echo get_post($product_postid)->post_title; ?></a>
								<input name="cart_track_color[]" value="<?php echo $cartLines->color; ?>" type="hidden" />
								<input name="cart_track_size[]" value="<?php echo $cartLines->size; ?>" type="hidden" />
								<?php if($cartLines->color != '') :?>
								<br /><span class="cart_color">Color:  <?php echo $cartLines->color; ?></span>
								<?php endif; ?>
								<?php if($cartLines->size != '') :?>
								<br /><span class="cart_size">Size:  <?php echo $cartLines->size;?></span>
								<?php endif; ?>
							</td>
							<td class="product-price"><?php echo apptivo_ecommerce_price($cartLines->effectiveUnitPrice); ?></td>
							<td class="product-quantity">
							
							<input rel="<?php echo $itm_qty; ?>" name="cart_item_qty[]" value="<?php echo esc_attr( $item_quantity ); ?>" size="2" title="Qty" class="input-text itemqty text" maxlength="2" />
							 <?php if( $cartLines->errorCode != '') { ?><span class="item_error <?php echo $cartLines->errorCode; ?>" > <?php echo $cartLines->errorMessage; ?> </span> <?php } ?> 
							</td>
							<td class="product-subtotal"><?php echo apptivo_ecommerce_price($cartLines->effectiveTotalPrice); ?></td>
							<td class="product-remove"><a href="javascript:void(0);" rel="<?php echo esc_url( $apptivo_ecommerce->cart->delete_item_url($cart_lineid) ); ?>" class="remove_item remove" title="<?php _e('Remove this item', 'apptivo_ecommerce'); ?>">Remove</a></td>
						</tr>
						<?php
						$itm_qty ++;
					endif;
				endforeach; 
			do_action( 'apptivo_ecommerce_cart_contents' );
			?>
					</tbody>
					
	</table>

				
	<div class="update_cart" style="float:right;">
	<input type="submit" class="btn" name="update_cart" value="Update Shopping Cart" />
	</div>
		    
	<div class="cart-collaterals">
		<?php do_action('apptivo_ecommerce_cart_collaterals'); ?>
		<?php apptivo_ecommerce_cart_totals(); ?>
	</div>
	<table class="shop_table cart" cellspacing="0" >
	<?php 
		$defaultFirmShippingMethodId = $shooping_cart->return->defaultFirmShippingMethodId;//Default Willcall Pickup ID.
	?>
   <input type="hidden" id="wiicallpickup_enabled" name="wiicallpickup_enabled" value="<?php echo $defaultFirmShippingMethodId; ?>" />
	   
			<tr id="shipping_zipcode" calls="tr_shipping_options">
				<td colspan="6" class="">
				 <input <?php if( $_SESSION['apptivo_checkout_shipping_type'] == 'NO' ) { ?> checked="checked" <?php } ?> type="radio" id="willcallpickup" name="shippingoptions"   value="willcall" rel="<?php echo $defaultFirmShippingMethodId; ?>" /> 
				 <label for="willcallpickup"><?php echo apply_filters('apptivo_ecommerce_willcall_pickup_label','Will-Call Pick-UP'); ?></label> <br />
				 <input type="radio" <?php if( $_SESSION['apptivo_checkout_shipping_type'] != 'NO' ) { ?> checked="checked" <?php } ?> id="shipping" name="shippingoptions" value="shipping"  />					
						 <label for="shipping"><?php echo apply_filters('apptivo_ecommerce_apply_zipcode_label','Enter Zip Code to Calculate Tax and Shipping'); ?></label>
						 <input name="zip_code" class="input-text" id="zip_code" value="" /> 
						 <input id="apply_zipcode" type="submit" class="btn" name="apply_zipcode" value="<?php echo apply_filters('apptivo_ecommerce_apply_zipcode_submit_button','Apply Zipcode'); ?>" />						 
	
				</td>
			</tr>
	 <!-- Will call pickup & Shipping End.. -->
	
					<?php if(get_option('apptivo_ecommerce_apply_coupan') == 'yes') : ?>
					<tr><td colspan="6" class="actions">
					<div class="coupons">
						<label for="coupon_code"><?php echo apply_filters('apptivo_ecommerce_coupan_label','Enter your coupon code here'); ?></label>						
						<input type="text" autocomplete="off"  name="coupon_code" class="input-text" id="coupon_code" value="" />
						 <input type="hidden" autocomplete="off"  name="applied_code" class="input-text" id="applied_code" value="<?php echo trim($promotionCode); ?>" />
						<input type="hidden"  name="hidden_shippingoption" id="hidden_shippingoption" value="" /> 
						<input type="submit" class="btn" name="apply_coupon" value="<?php echo apply_filters('apptivo_ecommerce_coupan_submit_button','Apply Coupon'); ?>" />
					</div>
					</td></tr>
					<?php endif; ?> <!--  Apply coupan End.. -->
								
			<tr>
			

		    
				<td colspan="6" class="actions">
					<?php $apptivo_ecommerce->nonce_field('cart') ?>
					<?php 
					global $apptivo_ecommerce;
					$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
					$available_gateways = apply_filters('apptivo_ecommerce_add_ons_available_gateways',$available_gateways);
				    if($available_gateways){
				    
				    $paypal_checkout_method = false;
				    $secure_checkout_method = false;
				    $google_checkout_method = false;
				    
				    foreach ($available_gateways as $gateway ) :
				     if( $gateway->id == 'paypal'):
				      $paypal_checkout_method = true;				     
				     endif; 
				     if($gateway->id == 'GoogleCheckout'):
				     $google_checkout_method = true;				     
				     endif;
				     if( $gateway->id == 'SecureCheckout' ):
				      $secure_checkout_method = true;				     
				     endif; 
				      endforeach;
				      
				    }
					?>
					<input type="hidden" name="proceed_to_checkout" id="proceed_to_checkout" value="" />
					
					<!-- Google Checkout -->
					<?php if($google_checkout_method) :
					$google_checkout_image = apply_filters('apptivo_ecommerce_cart_google_checkout_image','http://sandbox.google.com/checkout/buttons/checkout.gif?merchant_id=1234567890&amp;w=180&amp;h=46&amp;style=white&amp;variant=text&amp;loc=en_US');  ?>
					<input type="image" src="<?php echo $google_checkout_image; ?>" 
					title="Fast checkout through Google" alt="Fast checkout through Google" name="Google Checkout" class="google_checkout" >
					<?php if($secure_checkout_method ) : ?>
							<font style="vertical-align: middle;" class="cart_or">-OR-</font>
						<?php endif; ?>
					 <?php endif; ?> 
					 
					 
                    <!-- Paypal Checkout -->
					<?php if($paypal_checkout_method) : 
					$paypal_checkout_image = apply_filters('apptivo_ecommerce_cart_paypal_checkout_image','https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif');
					?>
					<input type="image"  class="paypal_checkout" src="<?php echo $paypal_checkout_image; ?>" >
					<?php endif; ?>
					
					 <!-- Secure Checkout -->
					<?php if($secure_checkout_method) : ?>
					
					<?php if($paypal_checkout_method ) : ?>
							<font style="vertical-align: middle;" class="cart_or">-OR-</font>
						<?php endif; ?>
						
					<?php 
					$a_net_img = file_exists(get_stylesheet_directory() . '/apptivo-ecommerce/images/secureCheckout.png') ? get_stylesheet_directory_uri() . '/apptivo-ecommerce/images/secureCheckout.png' : APPTIVO_ECOMMERCE_PLUGIN_BASEURL.'/assets/images/secureCheckout.png';
					$authorize_checkout_image = apply_filters('apptivo_ecommerce_cart_authorize_checkout_image',$a_net_img); 
					?>
					<input type="image"  class="secure_checkout" src="<?php echo $authorize_checkout_image; ?>" >
					<?php endif; ?>
					
				</td>
			</tr>			
			</table>
			</form>	
	<?php		
}