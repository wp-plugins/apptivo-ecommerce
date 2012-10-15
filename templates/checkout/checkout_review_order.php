<?php
/*
 * Order Review Page PayPal and GoogleCheckout
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */  
global $apptivo_ecommerce;
$baginfo = get_baginfo();
$CartLines = app_convertObjectToArray($baginfo->shoppingCartLines);
$available_methods = app_convertObjectToArray($baginfo->ratedShipment);
//Sales Tax Name and Tax Amount.
foreach($CartLines as $TaxLines)
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
?>
<div id="app_order">
	<table class="shop_table">
		<thead>
			<tr>
			  <th class="product-name">Product</th>	
			  <th class="product-quantity">Quantity</th>
		      <th class="product-price"><span class="nobr">Unit Price</span></th>
		      <th class="product-subtotal">Total</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"><?php _e('Subtotal', 'apptivo-ecommerce'); ?></td>
				<td><?php echo apptivo_ecommerce_price($baginfo->subTotalAmount); ?></td>
			</tr>
		   <!--  Shipping Amount  -->
					<?php if ($baginfo->shippingAmount != '' && $baginfo->shippingAmount > 0) : ?>
					<tr>
						<td colspan="3"><?php _e('Shipping Amount', 'apptivo-ecommerce'); ?></td>
						<td><?php echo apptivo_ecommerce_price($baginfo->shippingAmount); ?></td>
					</tr>
					<?php endif;?>
			
			<?php  	
			 echo '<input type="hidden" id="default_willcall_pickup_id"  name="default_willcall_pickup_id" value="'.$baginfo->defaultFirmShippingMethodId.'" />';			
			 if (sizeof($available_methods)>0 && $available_methods[0] != '' && ( $_SESSION['apptivo_checkout_shipping_type'] != 'NO' )) : ?>
				<td colspan="3"><?php _e('Shipping Method', 'apptivo-ecommerce'); ?></td>
				<td>
				<?php
					echo '<select name="shipping_method" id="shipping_method">';
						foreach ($available_methods as $method ) :
						  echo '<option value="'.esc_attr($method->firmShippingMethodId).'" ';
							if ($method->firmShippingMethodId== $baginfo->shippingOption ) echo 'selected="selected"';
							echo '>'.esc_html($method->serviceName).' &ndash;'.$method->totalCharges;
							echo '</option>';
						endforeach;
					echo '</select>';
						
				?></td>
			<?php endif;  ?>
			
			<?php if ($taxAmount > 0) : ?><tr>
				<td colspan="3"><?php _e($taxName, 'apptivo-ecommerce'); ?></td>
				<td><?php echo apptivo_ecommerce_price($taxAmount); ?></td>
			</tr><?php endif; ?>
			<?php if ($baginfo->totalDiscountAmount > 0) : ?><tr class="discount">
				<td colspan="3"><?php _e('Discount', 'apptivo-ecommerce'); ?></td>
				<td>-<?php echo apptivo_ecommerce_price($baginfo->totalDiscountAmount); ?></td>
			</tr><?php endif; ?>
			<tr>
				<td colspan="3"><strong><?php _e('Grand Total', 'apptivo-ecommerce'); ?></strong></td>
				<td><strong><?php echo apptivo_ecommerce_price($baginfo->totalPrice); ?></strong></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$CartLines = app_convertObjectToArray($baginfo->shoppingCartLines);
			if (sizeof($CartLines)>0 && !empty($CartLines[0])) : 
				foreach ($CartLines as $cart_items) :
				$product_postid = getIdFromMeta('_apptivo_item_id', $cart_items->itemId);
					if ($cart_items->quantity > 0) :
						echo '
							<tr>
								<td class="product-name">'.get_post($product_postid)->post_title.'';
						
						if($cart_items->color != '') : ?>
									<br /><span class="review_order_color">Color:  <?php echo $cart_items->color; ?></span>
								<?php endif; ?>
								<?php if($cart_items->size != '') :?>
									<br /><span class="review_order_color">Size:  <?php echo $cart_items->size;?></span>
								<?php endif; 
								
						echo '</td>
								<td class="product-qty">'.$cart_items->quantity.'</td>
								<td class="product-unitprice">'.apptivo_ecommerce_price($cart_items->effectiveUnitPrice).'</td>
								<td class="product-totalprice">'.apptivo_ecommerce_price($cart_items->effectiveTotalPrice).'</td>
							</tr>';
					endif;
				endforeach; 
			endif;
			?>
		</tbody>
	</table>
	<div id="payment">
		<ul class="payment_methods methods">
			<?php 
				$available_gateways = $apptivo_ecommerce->payment_gateways->available_payment_gateway_lists();
				
				$available_gateways = apply_filters('apptivo_ecommerce_add_ons_available_gateways',$available_gateways); 
				
				if ($available_gateways) : 
				    $paypal_google_checkout_method = false;
					foreach ($available_gateways as $gateway ) :
					
					  if($gateway->id != 'SecureCheckout') :
					  $paypal_google_checkout_method = true;					  
					  $pg_method = $_SESSION['apptivo_ecommerce_PG'];
					  
					  if( $pg_method == 'google')
					  {
					   $type = 'GoogleCheckout';
					  }else {
					   $type = 'paypal';
					  }
					  
					  if($gateway->id == 'GoogleCheckout') :
					  $gateway_icon = apply_filters('apptivo_ecommerce_google_checkout_icon',$gateway->icon);
					  endif;
					  
					  if($gateway->id == 'paypal') :
					  $gateway_icon = apply_filters('apptivo_ecommerce_paypal_checkout_icon',$gateway->icon);
					  endif;
					  
						?>
						<li>
						<input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php if ($type == $gateway->id ) echo 'checked="checked"'; ?> />
						<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->title; ?> 
						 <?php  global $apptivo_ecommerce;
						           if ($gateway->icon) :						             
							          echo  '<img src="'. $apptivo_ecommerce->force_ssl($gateway_icon).'" alt="'.$gateway->title.'"  title="'.$gateway->title.'" />';
						           endif;
						       ?>
						       
						</label> 
							<?php
								if ($gateway->has_fields || $gateway->description) : 
	                                  echo '<div class="pg_box payment_method_'.$gateway->id.'" style="display:none;">';
									  $gateway->payment_fields();
									  echo '</div>';						
								endif;
							?>
						</li>
						<?php
						endif;
						
						
					endforeach;
				endif;
			?>
		</ul>
		
		<div class="form-row">
		
			<noscript><?php _e('Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'apptivo-ecommerce'); ?><br/><input type="submit" class="button-alt" name="update_totals" value="<?php _e('Update totals', 'apptivo-ecommerce'); ?>" /></noscript>
		
			<?php $apptivo_ecommerce->nonce_field('process_checkout')?>
			
			<?php do_action( 'apptivo_ecommerce_review_order_before_submit' ); ?>
			
			<?php if($paypal_google_checkout_method) : ?>
			<input type="submit" class="btn alt" name="place_order" id="place_order" value="<?php echo apply_filters('apptivo_ecommerce_place_order_submit_button','Place Your Order'); ?>" />
			<span id="payment_load" style="float:right;margin-right:50px;"></span>
			<?php if (get_option('apptivo_ecommerce_terms_page_id')>0) : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php echo 'I agree with '.$_SERVER['HTTP_HOST'].','; ?> <a href="<?php echo esc_url( get_permalink(get_option('apptivo_ecommerce_terms_page_id')) ); ?>" target="_blank"><?php _e('Terms and Conditions.', 'apptivo-ecommerce'); ?></a></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php if (isset($_POST['terms'])) echo 'checked="checked"'; ?> id="terms" />
			</p>
			<?php endif; ?>			
			<?php endif; ?>			
			<?php do_action( 'apptivo_ecommerce_review_order_after_submit' ); ?>			
		</div>		
	</div>
		
</div>