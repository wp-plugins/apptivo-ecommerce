<?php

/**
 * Thankyou Shortcode
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
 
function get_apptivo_ecommerce_thankyou( ) {
	global $apptivo_ecommerce;
	if(function_exists('custom_apptivo_ecommerce_thankyou'))
	{
	return custom_apptivo_ecommerce_thankyou();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_thankyou');
	} 
}


function apptivo_ecommerce_thankyou( ) {
     global $apptivo_ecommerce;
     $payerid_token_amt = get_apptivo_payerwithtoken();   //Get payerId, Token and Amount.
     if(isset($payerid_token_amt) && !empty($payerid_token_amt['token']))
	 {
	 unset($_SESSION['apptivo_paypal_payerid_token_amt']); //Unset payerId, Token and Amount.
	 $cartSessionId = is_apptivo_cart_sessionId(); //get cartsession Id.
	 $userId = is_apptivo_user_logged_in();	  //get account Id.
	 $order_response =  apptivo_paypalorder($cartSessionId,$userId,$payerid_token_amt); //Apptivo confirm Paypal Order
	 }else {
	 	if(isset($_SESSION['apptivo_cart_response']) && !empty($_SESSION['apptivo_cart_response']))
	 	{  
	 		$order_response = $_SESSION['apptivo_cart_response'];
	 		unset($_SESSION['apptivo_cart_response']);	 
	 	}
	 }
	 $order_id = trim($order_response->return->orderId);
	 if(isset($order_id)) :	  
	    $apptivo_ecommerce->cart->emty_shopping_cart(); //Clear the cart session	   
	 endif;
	 $order = $order_response->return;
		 
	if ($order_id != '' ) :	
		     $_SESSION['apptivo_ecommerce_orderid'] = $order_id;  
			?>
				<p>Thank you. Your order has been received.&nbsp;&nbsp; &nbsp; <a href="javascript:void(0);" class="win_print" rel="<?php echo $order->orderNumber; ?>" >Click Here to print a receipt</a></p>
				<p class="order"><?php _e('Order:', 'apptivo-ecommerce'); ?>
						<strong># <?php echo $order->orderNumber; ?></strong></p>
				<p class="date"><?php _e('Date:', 'apptivo-ecommerce'); ?>
						<strong><?php echo date(get_option('date_format'), strtotime($order->orderedDate)); ?></strong></p>
				<p class="total"><?php _e('Total:', 'apptivo-ecommerce'); ?>
						<strong><?php echo apptivo_ecommerce_price($order->totalAmount); ?></strong></p>
				<p class="payment_method"><?php _e('Payment method:', 'apptivo-ecommerce'); ?>
						<strong><?php 
							$gateways = $apptivo_ecommerce->payment_gateways->payment_gateways();
							if (isset($gateways[$order->payment_method])) echo $gateways[$order->payment_method]->title;
							else echo $order->payment_method; 
						?></strong></p>
						
						<table class="shop_table cart" cellspacing="0">
		<thead>
			<tr>
				<th class="product-name">
				<span class="nobr"><?php _e('Product Name', 'apptivo-ecommerce'); ?></span></th>
				<th class="product-price"><span class="nobr"><?php _e('Unit Price', 'apptivo-ecommerce'); ?></span></th>
				<th class="product-quantity"><?php _e('Quantity', 'apptivo-ecommerce'); ?></th>
				<th class="product-subtotal"><?php _e('Price', 'apptivo-ecommerce'); ?></th>
			</tr>
		</thead>
	
		<tbody>
			<?php
			$orderLineDetails = app_convertObjectToArray($order->orderLineDetails);
			if (sizeof($orderLineDetails)>0) : 
				foreach ($orderLineDetails as $orderedDetails) :
						if($orderedDetails->lineType == 'ITEM') :
						?>
						<tr>
							<td class="product-name">
								<?php echo $orderedDetails->itemName; ?>
								<?php if($orderedDetails->trackColor != '') :?>
									<br /><span class="order_color">Color:  <?php echo $orderedDetails->trackColor; ?></span>
								<?php endif; ?>
								<?php if($orderedDetails->trackSize != '') :?>
									<br /><span class="order_color">Size:  <?php echo $orderedDetails->trackSize;?></span>
								<?php endif; ?>				
							</td>
							<td class="product-price">
							<?php echo apptivo_ecommerce_price($orderedDetails->unitPrice); ?> 
							</td>
							
							<td class="product-quantity">
							<?php echo $orderedDetails->quantity; ?> 
							</td>
							<td class="product-subtotal">
							<?php echo apptivo_ecommerce_price($orderedDetails->totalPrice); ?> 
							</td>
						</tr>
						<?php
					endif;
				endforeach; 
			endif;
			?>
		</tbody>
			<tfoot>
			       <!-- sub total Amount -->
					<tr>
						<td colspan="3"><?php _e('Subtotal', 'apptivo-ecommerce'); ?></td>
						<td><?php echo apptivo_ecommerce_price($order->subTotalAmount); ?></td>
					</tr>
					
					<!-- Shipping Method and Shipping Amount -->
					<?php foreach ($orderLineDetails as $orderedDetails) :
					
					if($orderedDetails->lineType == 'SHIPPING') { ?>
					<?php if(strlen(trim($orderedDetails->itemName)) != 0 ) : ?>
					<tr>
						<td colspan="3"><?php _e('Shipping Method', 'apptivo-ecommerce'); ?></td>
						<td><?php echo $orderedDetails->itemName; ?></small></td>
					</tr>
					       <?php endif; ?>
					<tr>
						<td colspan="3"><?php _e('Shipping Amount', 'apptivo-ecommerce'); ?></td>
						<td><?php echo apptivo_ecommerce_price($orderedDetails->totalPrice); ?></small></td>
					</tr>
					    
					<?php
					} else if($orderedDetails->lineType == 'DISCOUNT') {
						 if($orderedDetails->totalPrice > 0) {
						 ?>
						 <tr class="discount">
							<td colspan="3"><strong><?php echo $orderedDetails->itemName; ?></strong></td>
							<td><strong>-<?php echo apptivo_ecommerce_price($orderedDetails->totalPrice); ?></strong></td>
						 </tr>					
						 <?php 
						 }

					} else if($orderedDetails->lineType == 'TAX'){
						 if($orderedDetails->totalPrice > 0) {
						 ?>
						 <tr class="discount">
							<td colspan="3"><strong><?php echo $orderedDetails->itemName; ?></strong></td>
							<td><strong><?php echo apptivo_ecommerce_price($orderedDetails->totalPrice); ?></strong></td>
						 </tr>					
						 <?php 
						 }
						 
					}
					endforeach; ?>
				
				   <!-- Slaes Tax Amount -->
					<?php if ($taxAmount > 0) : ?>
					<tr>
						<td colspan="3"><strong><?php _e($taxName, 'apptivo-ecommerce'); ?></strong></td>
						<td><strong><?php echo apptivo_ecommerce_price($taxAmount); ?></strong></td>
					</tr>
					<?php endif; ?>
					
					
					
				    <!-- Grand Total Amount -->
					<tr>
						<td colspan="3"><strong><?php _e('Grand Total', 'apptivo-ecommerce'); ?></strong></td>
						<td><strong><?php echo apptivo_ecommerce_price($order->totalAmount); ?></strong></td>
					</tr>
		</tfoot>
	</table>
				<div class="clear"></div>
				<?php
	else :
	    wp_safe_redirect( get_option('home') );
	    exit;
	endif;	
}