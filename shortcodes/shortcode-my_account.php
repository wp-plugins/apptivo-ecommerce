<?php
/**
 * My Account Shortcode
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
function get_apptivo_ecommerce_my_account () {
	global $apptivo_ecommerce;
    if(function_exists('custom_apptivo_ecommerce_my_account'))
	{
	return custom_apptivo_ecommerce_my_account();	
	}else {
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_my_account');
	} 
}	
function apptivo_ecommerce_my_account( ) {
	global $apptivo_ecommerce;
	$apptivo_ecommerce->show_messages();
	if (is_apptivo_user_logged_in()) :
		?>
		<p><?php echo sprintf( __('Hello, <strong>%s</strong>. From your account you can view your recent orders and <a class="change_password" href="%s">change your password</a>.', 'apptivo-ecommerce'), $_SESSION['apptivo_user_account_name'], 'javascript:void(0);'); ?></p>
		
		<form style="display:none;"id="change_pwd" class="change_pwd" action="<?php echo esc_url( get_permalink(get_option('apptivo_ecommerce_change_password_page_id')) ); ?>" method="post">
		   <p class="chg_pwd_heading"><?php echo apply_filters('apptivo_ecommerce_change_password_heading','Change Password');?> <a class="cancel_chg_pwd" href="javascript:void(0);">Cancel</a></p>
			<p class="form-row form-row-first">
				<label for="password-1"><?php _e('New password', 'apptivo-ecommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-1" id="password-1" />
			</p>
			<p class="form-row form-row-last">
				<label for="password-2"><?php _e('Re-enter new password', 'apptivo-ecommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-2" id="password-2" />
			</p>
			<div class="clear"></div>
			<?php $apptivo_ecommerce->nonce_field('change_password')?>
			<p class="change_pwd_submit"><input type="submit" class="btn" name="save_password" value="<?php _e('Change your Password', 'apptivo-ecommerce'); ?>" /></p>
		</form>
		<?php do_action('apptivo_ecommerce_before_my_account'); ?>
		<h2><?php  echo apply_filters('apptivo_ecommerce_recent_orders_label','Recent Orders');  ?></h2>		
		<table class="shop_table my_account_orders">
			<thead>
				<tr>
					<th><span class="nobr"><?php _e('#', 'apptivo-ecommerce'); ?></span></th>
					<th><span class="nobr"><?php _e('Date', 'apptivo-ecommerce'); ?></span></th>
					<th><span class="nobr"><?php _e('Ship to', 'apptivo-ecommerce'); ?></span></th>
					<th><span class="nobr"><?php _e('Total', 'apptivo-ecommerce'); ?></span></th>
				</tr>
			</thead>
			<tbody><?php
			    $order_history = apptivo_orderHistory();
			    $apptivo_orders = app_convertObjectToArray($order_history->return);
			    if($order_history != 'E_100' && !empty($apptivo_orders[0])) :			    
			    foreach ($apptivo_orders as $order) :
					?><tr class="order">
						<td><?php echo $order->orderNumber; ?></td>
						<td><time title="<?php echo esc_attr( strtotime($order->orderedDate) ); ?>"><?php echo date(get_option('date_format'), strtotime($order->orderedDate)); ?></time></td>
						<td><address><?php if ($order->shippingContactDetails) echo formatted_shipping_address($order->shippingContactDetails); else echo '&ndash;'; ?></address></td>
						<td><?php echo apptivo_ecommerce_price($order->totalAmount); ?></td>
						</tr><?php
				endforeach;
				else :
				?><tr class="order">
				<td colspan="4" style="text-align:center"><?php echo 'No orders currently exist.'; ?></td>
				</tr>
				<?php 
				endif;
			?></tbody>
		</table>
		<div class="col2-set addresses">
		</div><!-- /.col2-set -->		
		<?php
		do_action('apptivo_ecommerce_after_my_account');
	else :
		wp_safe_redirect(get_permalink(get_option('apptivo_ecommerce_login_page_id'))); //Redirected to Login Page
	    exit();
	endif;
		
}

function get_apptivo_ecommerce_change_password () {
	global $apptivo_ecommerce;
	return $apptivo_ecommerce->shortcode_wrapper('apptivo_ecommerce_change_password'); 
}
	
function apptivo_ecommerce_change_password() {
	global $apptivo_ecommerce;
	$user_id = is_apptivo_user_logged_in();
	$user_account_name = is_apptivo_account_name_logged_in();
	if (is_apptivo_user_logged_in()) :
		
		if ($_POST) :
			
			if ($user_id>0 ) :
				
				if ( $_POST['password-1'] && $_POST['password-2']  ) :
					
					if ( $_POST['password-1'] == $_POST['password-2'] ) :
	
						$update_password = updateUserPassword($user_account_name,$_POST['password-1']);
						if($update_password->return =='AS-015')
						{  
							$error_message_as015 = apptivo_ecommerce_error_message('AS-015');
							$apptivo_ecommerce->add_message( __($error_message_as015, 'apptivo-ecommerce') );
						}else if( $update_password == 'E_100')
						{  
							$error_message_ae105 = apptivo_ecommerce_error_message('AE-105');
							$apptivo_ecommerce->add_error( __($error_message_ae105, 'apptivo-ecommerce') );
						}else {
							$error_message_ae106 = apptivo_ecommerce_error_message('AE-106');
							$apptivo_ecommerce->add_error( __($error_message_ae106, 'apptivo-ecommerce') );
						}
						wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')) );
						exit;
					else :
					    $error_message_ae107 = apptivo_ecommerce_error_message('AE-107');
						$apptivo_ecommerce->add_error( __($error_message_ae107, 'apptivo-ecommerce') );
					
					endif;
				
				else :
				    $error_message_ae108 = apptivo_ecommerce_error_message('AE-108');
					$apptivo_ecommerce->add_error( __($error_message_ae108, 'apptivo-ecommerce') );
				endif;			
			endif;
		endif;
		$apptivo_ecommerce->show_messages();
		?>
		<form id="change_pwd" class="change_pwd" action="<?php echo esc_url( get_permalink(get_option('apptivo_ecommerce_change_password_page_id')) ); ?>" method="post">
		   <p class="chg_pwd_heading"><?php echo apply_filters('apptivo_ecommerce_change_password_heading','Change Password');?></p>
			<p class="form-row form-row-first">
				<label for="password-1"><?php _e('New password', 'apptivo-ecommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-1" id="password-1" />
			</p>
			<p class="form-row form-row-last">
				<label for="password-2"><?php _e('Re-enter new password', 'apptivo-ecommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-2" id="password-2" />
			</p>
			<div class="clear"></div>
			<?php $apptivo_ecommerce->nonce_field('change_password')?>
			<p class="change_pwd_submit"><input type="submit" class="btn" name="save_password" value="<?php _e('Change your Password', 'apptivo-ecommerce'); ?>" /></p>
		</form>
		<?php
	else :
		wp_safe_redirect( get_permalink(get_option('apptivo_ecommerce_myaccount_page_id')) );
		exit;
	endif;
}