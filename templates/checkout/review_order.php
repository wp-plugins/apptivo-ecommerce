<?php
/*
 * Order Review Page Secure Checkout(Authorize.Net).
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
<h3 id="app_order_h3"><?php echo apply_filters('apptivo_ecommerce_your_order','Your order'); ?></h3>
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
			//if( $baginfo->defaultFirmShippingMethodId != $baginfo->shippingOption ) :
			if (sizeof($available_methods)>0 && $available_methods[0] != '' && ( $_SESSION['apptivo_checkout_shipping_type'] != 'NO' ) ) : ?>
			<tr id="shipping_method_tr">	
				<td colspan="3"><?php _e('Shipping Method', 'apptivo-ecommerce'); ?></td>
				<td>
				<?php
				//Deafult Will call Pickup id.
					echo '<select name="shipping_method" id="shipping_method">';
						foreach ($available_methods as $method ) :
						  echo '<option value="'.esc_attr($method->firmShippingMethodId).'" ';
							if ($method->firmShippingMethodId== $baginfo->shippingOption ) echo 'selected="selected"';
							echo '>'.esc_html($method->serviceName).' &ndash;'.$method->totalCharges;
							echo '</option>';
						endforeach;
					echo '</select>';
						
				?></td>
				</tr>
			<?php endif; 
			//endif; ?>
			
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
				if ($available_gateways) :
				$secure_checkout_gateway = false;
				
					foreach ($available_gateways as $gateway ) :
					   if($gateway->id == 'SecureCheckout') :
					   $secure_checkout_gateway = true;
						?>
						<li>
						<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->title; ?>
							<?php  global $apptivo_ecommerce;
						           if ($gateway->icon) :
						             $a_net_icon = apply_filters('apptivo_ecommerce_authorize_icon',$gateway->icon);
							          echo  '<img src="'. $apptivo_ecommerce->force_ssl($a_net_icon).'" alt="'.$gateway->title.'"  title="'.$gateway->title.'" />';
						           endif;
						       ?>
										
						</label> 
							<?php
								if ($gateway->has_fields || $gateway->description) : 
	                               
	                                ?>
	                                <div class="pg_box payment_method_SecureCheckout">
	                                <?php $gateway->payment_fields(); ?>
							<p>							
							<label for="cart_type">Cart Type <span class="required">*</span></label>
							<select id="cart_type" name="cart_type">
							<option value="">-- Select Cart Type --</option>
							<option value="VI">Visa</option>
                            <option value="MC">Mastercard</option>
                            <option value="AX">American Express</option>
                            <option value="DI">Discover</option>
							</select>
							<div style="left: 90px; top: -150px;display:none;" id="payment-tool-tip" class="tool-tip" >
    	                                    <img style="max-width:100%;" width="493" height="245" alt="" src="<?php echo APPTIVO_ECOMMERCE_PLUGIN_BASEURL.'/assets/images/what-is-this.gif'; ?>">
                                            <a class="tool-tip-closeme" href="javascript:void(0);"><img style="position: absolute;margin:3px;margin-right:-3px;" class="tool-tip-close" src="<?php echo APPTIVO_ECOMMERCE_PLUGIN_BASEURL.'/assets/images/closeme.png'; ?>" alt="" /> </a>
                            </div>
							<table cellspacing="0" cellpadding="0" border="0" style="display: inline;border:none;" id="tblPMCreditCard" class="font_size2">
                                 <tbody>
                                       <tr>
                                           <td valign="top" height="15" class="form-row" >Credit Card Number <span class="required">*</span></td>
                                           <td valign="top" height="15" name="CCExpires" id="CCExpires" class="form-row" >Expiration Date <span class="required">*</span></td>
                                           <td class="form-row" style="margin: 0px; padding-left: 10px;" >CVV <span class="required">*</span> <a class="cvv-what-is-this" href="javascript:void(0);">What's this?</a></td>
                                           
    
                                       </tr>
                                       <tr>
                                           <td valign="top">
                                             <input class="ccNum" type="text" value="" autocomplete="off" maxlength="20" name="ccNum" size="22" > <br>
                                           </td>
                                           <td valign="top" style="margin: 0px; padding: 0px;">
                                              
                                              <?php $month = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',
                                                                    '07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'); 
                                                        echo '<select name="expire_month">';
                                                        foreach ($month as $key => $value) :
                                                          echo '<option '.selected(date('m'),$key).' value="'.$key.'">'.$value.'</option> ';
                                                        endforeach;
                                                        echo '</select>';
                                                         ?>
                                                        <select name="expire_year"> 
                                                             <?php // Set the year to be the current year up to ten years from now 
                                                                    for ($i = date("Y"); $i < date("Y") + 10; $i++) 
                                                                        { 
                                                                            	echo "<option value=\"" . $i . "\">" . $i . "</option>";
                                                                         }
                                                             ?> 
                                                        </select>  
                                             </td>
                                                   <td valign="top" style="margin: 0px; padding-left: 10px;" >
                                                      <input type="password" value="" autocomplete="off" name="ccId" id="ccId" maxlength="4" size="8" >
                                                   </td>
                                              </tr>
                                       </tbody>
                                       </table> 
 							</p>
							</div>
	                                <?php 
	                                
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
		
			<?php $apptivo_ecommerce->nonce_field('process_checkout')?>
			
			<?php do_action( 'apptivo_ecommerce_review_order_before_submit' ); ?>
			
			<?php  if( $secure_checkout_gateway) :  ?>
			<input type="submit" class="btn alt" name="place_order" id="place_order" value="<?php echo apply_filters('apptivo_ecommerce_place_order_submit_button','Place Your Order'); ?>" />
			<span id="payment_load" style="float:right;margin-right:50px;"></span>
			<?php  $confirm_page = get_option('apptivo_ecommerce_enable_a_net_confirm'); ?>
			<?php if (get_option('apptivo_ecommerce_terms_page_id')>0 && $confirm_page != 'yes') : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php echo 'I agree with '.$_SERVER['HTTP_HOST'].','; ?> <a href="<?php echo esc_url( get_permalink(get_option('apptivo_ecommerce_terms_page_id')) ); ?>" target="_blank"><?php _e('Terms and Conditions.', 'apptivo-ecommerce'); ?></a></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php if (isset($_POST['terms'])) echo 'checked="checked"'; ?> id="terms" />
			</p>
			<?php endif; ?>			
			<?php endif; ?>			
			<?php do_action( 'apptivo_ecommerce_review_order_after_submit' );?>			
		</div>		
	</div>	
</div>