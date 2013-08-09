jQuery(document).ready(function($) {
	jQuery('form').preventDoubleSubmit();
	$("input.itemqty, input.ccNum").keypress(function (e){
		  var charCode = (e.which) ? e.which : e.keyCode;
		  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		    return false;
		  }
		});
	
	
	$('#zip_code').bind('keypress', function(e) {
        if(e.keyCode==13){
          $('#shop_cart_actions').val('APPLY_ZIPCODE');
          return true;
        }
    });
	
	$('#coupon_code').bind('keypress', function(e) {
        if(e.keyCode==13){
          $('#shop_cart_actions').val('APPLY_COUPON');
          return true;
        }
    });
	
	$('.cvv-what-is-this').live('click',function(){
		$('#payment-tool-tip').toggle();		
	});
        $('.tool-tip-closeme').live('click',function(){
		$('#payment-tool-tip').hide();		
	});
         
	$('.paypal_checkout').live('click',function(){
		$('#proceed_to_checkout').val('paypalcheckout');
		return true;
	});
	
	$('.secure_checkout').live('click',function(){
		$('#proceed_to_checkout').val('securecheckout');
		return true;
	});
	
	$('.google_checkout').live('click',function(){
		$('#proceed_to_checkout').val('googlecheckout');
		return true;
	});
	$('#6_letters_code').live("cut copy paste",function(e) {
	      e.preventDefault();
	});
	
	//Reset password
	$('a.lost_password').click(function(){
		$('.apptivo_ecommerce_error').remove();
		$('.apptivo_ecommerce_message').remove();
		$('form.login').hide();
		$('form.reset_pwd').show();
		$('#account_username').val('');
		return false;
	});
    //Cancel reset password
	$('a.cancel_forgot_password').click(function(){
		$('.apptivo_ecommerce_error').remove();
		$('.apptivo_ecommerce_message').remove();
		$('form.reset_pwd').hide();
		$('form.login').show();
		$('#username').val('');
		$('#password').val('');
		return false;
	});
    //Update password
	$('a.change_password').click(function(){
		$('.apptivo_ecommerce_error').remove();
		$('.apptivo_ecommerce_message').remove();
		$('#change_pwd').toggle();
		return false;
	});
	$('a.cancel_chg_pwd').click(function(){
		$('#change_pwd').hide();
		return false;
	});
	//Reset password form submission
	$('form#reset_pwd').submit(function(){
		$('.apptivo_ecommerce_error').remove();
		$('.apptivo_ecommerce_message').remove();
		var data = {
				action: 'apptivo_ecommerce_pwd_reset_action',
				email_address : $('#account_username').val()
			};
	
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				if( response == 'AS-019')
				{
					$('#username').val($('#account_username').val());
					$('form.reset_pwd').hide();
					$('form.login').show();					
					$('p.login_heading').after('<div class="apptivo_ecommerce_message">Password has been reset successfully and sent in mail.</div>');
				}else{
				$('p.reset_pwd_heading').after(response);
				$('#account_username').focus();
				}
			})
			return false;
	});
	//update password form submission
	$('form#change_pwd').submit(function(){
		$('.apptivo_ecommerce_error').remove();
		$('.apptivo_ecommerce_message').remove();
		var data = {
				action: 'apptivo_ecommerce_pwd_change_action',
				password : $('#password-1').val(),
				re_password : $('#password-2').val()
			};
	
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				$('p.chg_pwd_heading').after(response);
				$('#password-1').val('');
				$('#password-2').val('');
				$('#password-1').focus();
			})
			return false;
	});		
	
	//Set Maximum height for single product image
	if( $("img.attachment-product_single").size() > 0 ) {
		$("img.attachment-product_single").css('max-height', apptivo_ecommerce_params.single_image_height+'px')
		}
	//prettyPhoto Light Box Disable
	var light_box_options = $('#product_lightbox').val();
	if(light_box_options == 'no')
	{	
	$("a.product_thumbnial").live("hover", function(){ 
    var href_url = $(this).attr("target_url");
     $('.attachment-product_single').attr('src',href_url) ;
    $("a.single_image").attr('href',href_url);
	});
	}else{
		if( $("a.pretty_gallery").size() > 0 )
        {
		  $("a[rel^='prettyPhoto']").prettyPhoto();
        }
	}
	//Captrch refresh
	$("#captach_refresh").click(function() {
		var data = {
				action: 			'apptivo_ecommerce_captcha_refresh'
			};
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				$('#captchaimg').replaceWith( response );
			});				
	});
	//Highlight Products menu
	if($("#page-item-products").size() > 0 )
	{
	var  products_pageid = $("#page-item-products").val();
	$(".page-item-"+products_pageid).addClass('current_page_item');
	}
	
	if ($("#willcallpickup").attr("checked"))
	{
	$("#zip_code").attr("disabled", "disabled");
	$("#apply_zipcode").attr("disabled", "disabled");
	$("#cart_shipping_options").hide();
	}
	
	$( "input[name='shippingoptions']" ).bind( "click", radioClicks )
    function radioClicks()
	 {
		 var methodtype =  $( this ).val();
         if(methodtype == 'willcall')
         {
        	 var method = $( this ).attr('rel');
        	 $("#zip_code").attr("disabled", "disabled");
        	 $("#apply_zipcode").attr("disabled", "disabled");
        	 $('#wiicallpickup_enabled').val(method);
             var data = {
					action: 			'apptivo_ecommerce_update_shipping_method',
					security: 			apptivo_ecommerce_params.update_shipping_method_nonce,
					shipping_method: 	method
				};
				$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
					$('div.totals_cart').replaceWith( response );
					 $("#cart_shipping_options").hide();
				});
				
        	 
         }else{
        	 var method = $('#shipping_method').val();
        	 $('#wiicallpickup_enabled').val('NO');
        	 $("#zip_code").attr("disabled", false);
        	 $("#apply_zipcode").attr("disabled", false);
        	 $("#shipping_method").attr("disabled",false);
        	 var data = {
 					action: 			'apptivo_ecommerce_update_shipping_method',
 					security: 			apptivo_ecommerce_params.update_shipping_method_nonce,
 					shipping_method: 	method,
 					type:'shipping',
 					shipping_type:'shipping'
 				};
 				$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
 					$('div.totals_cart').replaceWith( response );
 								
 				});
        	 $("#cart_shipping_options").show();
         }
	 }

	$( "input[name='shippingcosts']" ).bind( "click", radioClicksCustom );
	
	$( "#apptivo_item_qty" ).bind( "change", seelctChangeCustom );

	
	function seelctChangeCustom()
	 {
		 var shipping_productID =  $('#ship_prod_id').val();
		 var qty =  $('#apptivo_item_qty').val();
		 var productID =  $('#apptivo_item_qty').attr("rel");
		
		
		 var data = {
					action: 			'apptivo_ecommerce_update_price',
					shipping_productID: 	shipping_productID,
					qty:qty,
					productID:productID
				};
				$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
					$('#up_total_proce').html(response);
					
				});
				
	 }
	
    function radioClicksCustom()
	 {
		 var shipping_productID =  $( this ).val();
		 $('#ship_prod_id').val(shipping_productID);
		 var qty =  $('#apptivo_item_qty').val();
		 var productID =  $('#apptivo_item_qty').attr("rel");
		
		
		 var data = {
					action: 			'apptivo_ecommerce_update_price',
					shipping_productID: 	shipping_productID,
					qty:qty,
					productID:productID
				};
				$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
					$('#up_total_proce').html(response);
					
				});
				
	 }
	
	
    
	if($('#register_login').length)
	{ 
		if ($('#ecommerce_error_field').length)
		{
			var field_val = $('#ecommerce_error_field').val();
			$('html, body').animate({
				scrollTop: ($('.apptivo_ecommerce_error').offset().top - 35)
			}, 01);
			
			if( field_val == '')
			{
				field_val = 'username';
			}
			if('account_username' == field_val )
			{
				$('#last_pwd').show();
			}
			$('#'+field_val).focus();
		}
	}
	
	if($('#register_focus').length)
	{ 		
		if ($('#ecommerce_error_field').length)
		{
			$('html, body').animate({
			    scrollTop: ($('.apptivo_ecommerce_error').offset().top - 35)
			}, 01);
			
			var field_val = $('#ecommerce_error_field').val();
			$('#'+field_val).focus();
		}
	}
	//Items sorting...
	$('select.sortby').change(function(){		
		var sort_option = $(this).val();
		var category_id = $('#item_category').val();
		var page_no = $('#item_pageno').val();
		var post_type = 'item';
		var data = {
				action:'apptivo_ecommerce_products_sorting',
				sort_option: sort_option,
				category_id:category_id,
				page_no:page_no,
				post_type:post_type
			};
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
			 	$('ul#item_lists').replaceWith( response );
			});
			
	});
	
	//Add  items to cart in Form Submit.
	$('.add_item_to_cart_button').live('click',function(){
		$('form.additems_tocart').submit();
	});
	
	//For Print Receipt.
	$('a.win_print').live('click',function(){
		var orderno = $(this).attr('rel');
		var page_id = apptivo_ecommerce_params.print_receipt_page;
		newWindow=window.open("?page_id="+page_id+"&orderno="+orderno,"OrderedInformation","menubar=1, resizable=1, width=920, height=900, scrollbars=yes");
		newWindow.moveTo(0,0);//newWindow.focus();
	});

	if( $('.hidden_cart0').size() > 0)
	{
		$('.widget_apptivo_shopping_cart').hide();
		$('.app_widget_shopping_cart').hide();
	}
	// Ajax add to cart
	$('.addtocart_button').live('click', function() {
		
	    if($(this).hasClass('loading')) return false;

	    var thisbutton = $(this);
		if (thisbutton.is('.product_type_item')) {
	
			$(thisbutton).addClass('loading');
			var data = {
				action: 		'apptivo_ecommerce_add_to_cart',
				product_id: 	$(thisbutton).attr('rel'),
				security: 		apptivo_ecommerce_params.add_to_cart_nonce
			};
			
			// Trigger event
			$('body').trigger('adding_to_cart');
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				// Get response
				data = $.parseJSON( response );
				if (data.error) {
					$(thisbutton).removeClass('loading');
					alert(data.error);					
					return;
				}
				fragments = data;
				
				// Block fragments class
				if (fragments) {
					$.each(fragments, function(key, value) {
						$(key).addClass('updating');
					});
				}
				// Block widgets and fragments
				$('.widget_apptivo_shopping_cart, .app_widget_shopping_cart, .shop_table.cart, .updating, .totals_cart').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
				
				// Changes button classes
				$(thisbutton).addClass('added');
				
				$(thisbutton).removeClass('loading');
				$('.app_widget_shopping_cart').removeClass('hidden_cart0');
				// Cart widget load
				if ($('.app_widget_shopping_cart').size()>0) {
					
					$('.app_widget_shopping_cart:eq(0)').load( window.location + ' .app_widget_shopping_cart:eq(0) > *', function() {
						$('#widget_cart_title').show();
						// Replace fragments
						if (fragments) {
							$.each(fragments, function(key, value) {								
								$(key).replaceWith(value);
							});
						}						
						// Unblock
						$('.widget_apptivo_shopping_cart, .app_widget_shopping_cart, .updating').css('opacity', '1').unblock();
					} );
				} else {
					// Replace fragments
					if (fragments) {
						$.each(fragments, function(key, value) {
							$(key).replaceWith(value);
						});
					}
					// Unblock
					$('.widget_apptivo_shopping_cart, .app_widget_shopping_cart, .updating').css('opacity', '1').unblock();
				}
				
				
				
				$('.totals_cart').load( window.location + ' .totals_cart:eq(0) > *', function() {
					$('.totals_cart').css('opacity', '1').unblock();
				});
				
				
				// Trigger event so themes can refresh other areas
				$('body').trigger('added_to_cart');
		
			});
			
			return false;
		
		} else {
			return true;
		}
		
	});
	
	

	
	/* states */
	var states_json = apptivo_ecommerce_params.countries.replace(/&quot;/g, '"');
	var states = $.parseJSON( states_json );			
	
	//On Change
	$('select.country_to_state').live('change', function() {		
		var country = $(this).val();
		var state_box = $('#' + $(this).attr('rel'));		
		var input_name = $(state_box).attr('name');
		var input_id = $(state_box).attr('id');
        
		if (states[country]) {
			var options = '';
			var state = states[country];
			for(var index in state) {
				options = options + '<option value="' + index + '">' + state[index] + '</option>';
			}
			if ($(state_box).is('input')) {
				$(state_box).replaceWith('<select name="' + input_name + '" id="' + input_id + '"><option value="">' + apptivo_ecommerce_params.select_state_text + '</option></select>');
				state_box = $('#' + $(this).attr('rel'));
			}
			$(state_box).append(options);
		} else {
			if ($(state_box).is('select')) {
				$(state_box).replaceWith('<input type="text" class="input-text" placeholder="' + apptivo_ecommerce_params.state_text + '" name="' + input_name + '" id="' + input_id + '" />');
				state_box = $('#' + $(this).attr('rel'));
			}
		}
		
	}).change();
	
	
	//On keydown
	$('select.country_to_state').live('keydown', function() {
		var country = $(this).val();
		var state_box = $('#' + $(this).attr('rel'));
		var input_name = $(state_box).attr('name');
		var input_id = $(state_box).attr('id');
    	if (states[country]) {
			var options = '';
			var state = states[country];
			for(var index in state) {
				options = options + '<option value="' + index + '">' + state[index] + '</option>';
			}
			if ($(state_box).is('input')) {
				$(state_box).replaceWith('<select name="' + input_name + '" id="' + input_id + '"><option value="">' + apptivo_ecommerce_params.select_state_text + '</option></select>');
				state_box = $('#' + $(this).attr('rel'));
			}
			$(state_box).append(options);
		} else {
			if ($(state_box).is('select')) {
				$(state_box).replaceWith('<input type="text" class="input-text" placeholder="' + apptivo_ecommerce_params.state_text + '" name="' + input_name + '" id="' + input_id + '" />');
				state_box = $('#' + $(this).attr('rel'));
			}
		}
		
	}).keydown();
	
	
	/*Cart Page*/
	if (apptivo_ecommerce_params.is_cart==1) { 
		
	   $('a.remove_item').live('click',function(){
		   var remove_url  = $( this ).attr('rel');
		   var answer = confirm('The chosen item will be removed from your cart.Please confirm removal of the chosen item.');
   		   if (answer) {
		   window.location = decodeURI(remove_url);
		   return true;
   		   }else {
		   return false;
   		   }
	   });
		
		if ($('.shopingcart_page').size()>0){
			 setTimeout(function () {
			        $('.apptivo_ecommerce_message').hide();
			    }, 5000);
		    }
		
		$('select#shipping_method').live('change', function() {
			var method = $('#shipping_method').val();
			$('#hidden_shippingoption').val(method);
			$('div.totals_cart').block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			var data = {
				action: 			'apptivo_ecommerce_update_shipping_method',
				security: 			apptivo_ecommerce_params.update_shipping_method_nonce,
				shipping_method: 	method
			};
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				$('div.totals_cart').replaceWith( response );
			});
		});
	}
	
	/* Checkout Page*/
	if (apptivo_ecommerce_params.is_checkout==1) {
		
		if( $('#shipping_state').length == 0 && $('form.confirm').length == 0 ) {
			$('#shipping_method_tr').remove();
		}
		var updateTimer;
		
		function update_checkout() {
		
			var method = $('#shipping_method').val();
			
			var country 	= $('#billing_country').val();
			var state 		= $('#billing_state').val();
			var postcode 	= $('input#billing_postcode').val();
				
			if ($('#shiptobilling input').is(':checked') || $('#shiptobilling input').size()==0) {
				var s_country 	= $('#billing_country').val();
				var s_state 	= $('#billing_state').val();
				var s_postcode 	= $('input#billing_postcode').val();
				
			} else {
				var s_country 	= $('#shipping_country').val();
				var s_state 	= $('#shipping_state').val();
				var s_postcode 	= $('input#shipping_postcode').val();
			}
			
			if( $('#shipping_state').length > 0) {
			$('#app_order').block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			}
			
			if( $('#shiptobilling input').length > 0)
			{
				//To proceed The address have billing and shipping Address.
				var data = {
						action: 			'apptivo_ecommerce_update_shipping_tax',
						security: 			apptivo_ecommerce_params.update_order_review_nonce,
						shipping_method: 	method, 
						country: 			country, 
						state: 				state, 
						postcode: 			postcode, 
						s_country: 			s_country, 
						s_state: 			s_state, 
						s_postcode: 		s_postcode,
						post_data:			$('form.checkout').serialize()
					};
			}else {
               //To proceed the address have shipping address only				
				var pg_method    = $('#pg_method').val();
				if( pg_method != 'secure' ) {
				var pg_type     = 'paypalcheckout';
				}else {
				var pg_type     = 'securecheckout';	
				}
				var s_country 	= $('#shipping_country').val();
				var s_state 	= $('#shipping_state').val();
				var s_postcode 	= $('input#shipping_postcode').val();
			
				var data = {
						action: 			'apptivo_ecommerce_update_shipping_tax',
						security: 			apptivo_ecommerce_params.update_order_review_nonce,
						shipping_method: 	method, 
						country: 			country, 
						state: 				state, 
						postcode: 			postcode, 
						s_country: 			s_country, 
						s_state: 			s_state, 
						s_postcode: 		s_postcode,
						pg_type: pg_type,
						pg_method:pg_method,
						post_data:			$('form.paypalcheckout').serialize()
					};
			}
			//To Proceed secure checkout page have steps only one and apyap/Google Checkout
			if( $('#shipping_state').length > 0 ) {
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				if ( apptivo_ecommerce_params.confirm_page == 'no' || pg_type == 'paypalcheckout' ) { 
				$('#app_order').remove();
				$('#app_order_heading').after(response);
				$('#app_order input[name=payment_method]:checked').click();
				}				
			});
			}
		
		}
			
		function update_shipping_method() {
			
			var method = $('#shipping_method').val();
			
			var country 	= $('#billing_country').val();
			var state 		= $('#billing_state').val();
			var postcode 	= $('input#billing_postcode').val();
				
			if ($('#shiptobilling input').is(':checked') || $('#shiptobilling input').size()==0) {
				var s_country 	= $('#billing_country').val();
				var s_state 	= $('#billing_state').val();
				var s_postcode 	= $('input#billing_postcode').val();
				
			} else {
				var s_country 	= $('#shipping_country').val();
				var s_state 	= $('#shipping_state').val();
				var s_postcode 	= $('input#shipping_postcode').val();
			}
			
			
			$('#app_order').block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			
			if($('form.confirm').length > 0 )
			{
				var pg_type     = 'securecheckout';			
				var data = {
						action: 			'apptivo_ecommerce_update_order_review',						
						shipping_method: 	method, 						
						pg_type:            pg_type						
					};
			}else if( $('#shiptobilling input').length > 0) {
			var data = {
				action: 			'apptivo_ecommerce_update_order_review',
				security: 			apptivo_ecommerce_params.update_order_review_nonce,
				shipping_method: 	method, 
				country: 			country, 
				state: 				state, 
				postcode: 			postcode, 
				s_country: 			s_country, 
				s_state: 			s_state, 
				s_postcode: 		s_postcode,
				post_data:			$('form.checkout').serialize()
			};
				
			}else {
				var pg_type     = 'paypalcheckout';
				var s_country 	= $('#shipping_country').val();
				var s_state 	= $('#shipping_state').val();
				var s_postcode 	= $('input#shipping_postcode').val();
			
				var data = {
						action: 			'apptivo_ecommerce_update_order_review',
						security: 			apptivo_ecommerce_params.update_order_review_nonce,
						shipping_method: 	method, 
						country: 			country, 
						state: 				state, 
						postcode: 			postcode, 
						s_country: 			s_country, 
						s_state: 			s_state, 
						s_postcode: 		s_postcode,
						pg_type:            pg_type,
						post_data:			$('form.paypalcheckout').serialize()
					};
			}
			
			$.post( apptivo_ecommerce_params.ajax_url, data, function(response) {
				
				$('#app_order').remove();
				$('#app_order_heading').after(response);
				$('#app_order input[name=payment_method]:checked').click();
				
			
			});
		
		}
		
		$(function(){
			
			$('p.password').hide();
			
			$('input.show_password').change(function(){
				$('p.password').slideToggle();
			});
			

			$('#shiptobilling input').change(function(){
			    $('div.shipping_address').hide();
			    if (!$(this).is(':checked')) {
					$('div.shipping_address').slideDown();
				}
			}).change();
			
			

			
			$('.payment_methods input.input-radio').live('click', function(){
				$('div.pg_box').hide();
				if ($(this).is(':checked')) {
					$('div.pg_box.' + $(this).attr('ID')).slideDown();
				}
			});
			
			$('#app_order input[name=payment_method]:checked').click();
			
			 if( $('form.checkout').is(':visible') )
			 {
				 $('form.login').hide();
			 }
			
			$('a.login_view').click(function(){
				$('form.login').slideToggle();
				return false;
			});
		
			
			/* Update totals */
			$('#shipping_method').live('change', function(){
				if( $('#wiicallpickup_enabled').length == 0)
				{
				clearTimeout(updateTimer);
				update_shipping_method();
				}
			});
			
			
			$('#billing_postcode, #shipping_postcode').live('blur', function(){
				clearTimeout(updateTimer);
				update_checkout();
				
			});
			
			$('select#billing_country, select#billing_state, select#shipping_country, select#shipping_state, #shiptobilling input').live('change', function(){
				clearTimeout(updateTimer);
				update_checkout();
			});
			
				
			/* AJAX Form Submission */
			$('form.paypalcheckout').submit(function(){				
				var form = this;
				$(form).block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
				var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
				jQuery('#payment_load').html('<img src="'+load_image+'"/>');
				
				$.ajax({
					type: 		'POST',
					url: 		apptivo_ecommerce_params.paypal_checkout_url,
					data: 		$(form).serialize(),
					success: 	function( code ) {	
					var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
					if( code == '')
					{
						$(form).prepend( code );
						$(form).unblock(); 
						jQuery('#payment_load').html('');
						alert(apptivo_ecommerce_params.payment_error);
						window.location.href = apptivo_ecommerce_params.cart_page;
						$('html, body').animate({
						    scrollTop: ($('form.checkout').offset().top - 100)
						}, 500);
						
					}else{					
						$('.apptivo_ecommerce_error, .apptivo_ecommerce_message').remove();
							try {
								success = $.parseJSON( code );
								$('html, body').animate({
								    scrollTop: ($('form.paypalcheckout').offset().top - 100)
								}, 500);
								jQuery("body").block(
										{ 
											message: "<img src=\"http://acwpcdnbucket1.s3.amazonaws.com/awp-content_1/11501wp10034/files/loading.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" /> We are now redirecting to make payment..", 
											overlayCSS: 
											{ 
												background: "#fff", 
												opacity: 0.6 
											},
											css: { 
												padding:        20, 
										        textAlign:      "center", 
										        color:          "#555", 
										        border:         "3px solid #aaa", 
										        backgroundColor:"#fff", 
										        cursor:         "wait",
										        lineHeight:		"32px"
										    } 
										});
								window.location = decodeURI(success.redirect);
							}
							catch(err) {
								$(form).prepend( code );
								$(form).unblock(); 
								jQuery('#payment_load').html('');
								$('html, body').animate({
								    scrollTop: ($('form.paypalcheckout').offset().top - 100)
								}, 500);
								var error_fld = $('#ecommerce_error_field').val();								
								$('#'+error_fld).focus();
							}
					}
						},
					dataType: 	"html"
				});
				return false;
			});
			
			/* AJAX Form Submission */
			$('form.checkout').submit(function(){
				
				var form = this;
				$(form).block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
				var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
				jQuery('#payment_load').html('<img src="'+load_image+'"/>');
				
				$.ajax({
					type: 		'POST',
					url: 		apptivo_ecommerce_params.checkout_url,
					data: 		$(form).serialize(),
					success: 	function( code ) {
					
					var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
					if( code == 1000 )
					{	
						window.location = decodeURI(apptivo_ecommerce_params.confirm_url);
						return true;
					}else if( code == '')
					{
						$(form).prepend( code );
						$(form).unblock(); 
						jQuery('#payment_load').html('');
						alert(apptivo_ecommerce_params.payment_error);
						window.location.href = apptivo_ecommerce_params.cart_page;
						$('html, body').animate({
						    scrollTop: ($('form.checkout').offset().top - 100)
						}, 500);
						
					}else{					
						$('.apptivo_ecommerce_error, .apptivo_ecommerce_message').remove();
							try {
								success = $.parseJSON( code );
								$('html, body').animate({
								    scrollTop: ($('form.checkout').offset().top - 100)
								}, 500);
								jQuery("body").block(
										{ 
											message: "<img src=\"http://acwpcdnbucket1.s3.amazonaws.com/awp-content_1/11501wp10034/files/loading.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" /> We are now redirecting to make payment..", 
											overlayCSS: 
											{ 
												background: "#fff", 
												opacity: 0.6 
											},
											css: { 
												padding:        20, 
										        textAlign:      "center", 
										        color:          "#555", 
										        border:         "3px solid #aaa", 
										        backgroundColor:"#fff", 
										        cursor:         "wait",
										        lineHeight:		"32px"
										    } 
										});
								window.location = decodeURI(success.redirect);
							}
							catch(err) {
								$(form).prepend( code );
								$(form).unblock(); 
								jQuery('#payment_load').html('');
								$('html, body').animate({
								    scrollTop: ($('form.checkout').offset().top - 100)
								}, 500);
								var error_fld = $('#ecommerce_error_field').val();								
								$('#'+error_fld).focus();
							}
					}
						},
					dataType: 	"html"
				});
				return false;
			});
			
$('form.confirm').submit(function(){
				
				var form = this;
				$(form).block({message: null, overlayCSS: {background: '#fff url(' + apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
				var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
				jQuery('#payment_load').html('<img src="'+load_image+'"/>');
				
				$.ajax({
					type: 		'POST',
					url: 		apptivo_ecommerce_params.confirm_checkout_url,
					data: 		$(form).serialize(),
					success: 	function( code ) {
					
					var load_image = apptivo_ecommerce_params.plugin_url + '/assets/images/ajax-loader.gif';
					if( code == '')
					{
						$(form).prepend( code );
						$(form).unblock(); 
						jQuery('#payment_load').html('');
						alert(apptivo_ecommerce_params.payment_error);
						window.location.href = apptivo_ecommerce_params.cart_page;
						$('html, body').animate({
						    scrollTop: ($('form.confirm').offset().top - 100)
						}, 500);
						
					}else{					
						$('.apptivo_ecommerce_error, .apptivo_ecommerce_message').remove();
							try {
								success = $.parseJSON( code );
								$('html, body').animate({
								    scrollTop: ($('form.confirm').offset().top - 100)
								}, 500);
								jQuery("body").block(
										{ 
											message: "<img src=\"http://acwpcdnbucket1.s3.amazonaws.com/awp-content_1/11501wp10034/files/loading.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" /> We are now redirecting to make payment..", 
											overlayCSS: 
											{ 
												background: "#fff", 
												opacity: 0.6 
											},
											css: { 
												padding:        20, 
										        textAlign:      "center", 
										        color:          "#555", 
										        border:         "3px solid #aaa", 
										        backgroundColor:"#fff", 
										        cursor:         "wait",
										        lineHeight:		"32px"
										    } 
										});
								window.location = decodeURI(success.redirect);
							}
							catch(err) {
								$(form).prepend( code );
								$(form).unblock(); 
								jQuery('#payment_load').html('');
								$('html, body').animate({
								    scrollTop: ($('form.confirm').offset().top - 100)
								}, 500);
								var error_fld = $('#ecommerce_error_field').val();								
								$('#'+error_fld).focus();
							}
					}
						},
					dataType: 	"html"
				});
				return false;
			});

		
		});
	} /*Only For Checkout and Secure checkout Page*/
	

});


/*!
 * jQuery blockUI plugin
 * Copyright (c) 2007-2010 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 */
;(function($) {
/*
if (/1\.(0|1|2)\.(0|1|2)/.test($.fn.jquery) || /^1.1/.test($.fn.jquery)) {
	alert('blockUI requires jQuery v1.2.3 or later!  You are using v' + $.fn.jquery);
	return;
}
*/
$.fn._fadeIn = $.fn.fadeIn;

var noOp = function() {};

// this bit is to ensure we don't call setExpression when we shouldn't (with extra muscle to handle
// retarded userAgent strings on Vista)
var mode = document.documentMode || 0;
var setExpr = $.browser.msie && (($.browser.version < 8 && !mode) || mode < 8);
var ie6 = $.browser.msie && /MSIE 6.0/.test(navigator.userAgent) && !mode;

// global $ methods for blocking/unblocking the entire page
$.blockUI   = function(opts) { install(window, opts); };
$.unblockUI = function(opts) { remove(window, opts); };

// convenience method for quick growl-like notifications  (http://www.google.com/search?q=growl)
$.growlUI = function(title, message, timeout, onClose) {
	var $m = $('<div class="growlUI"></div>');
	if (title) $m.append('<h1>'+title+'</h1>');
	if (message) $m.append('<h2>'+message+'</h2>');
	if (timeout == undefined) timeout = 3000;
	$.blockUI({
		message: $m, fadeIn: 700, fadeOut: 1000, centerY: false,
		timeout: timeout, showOverlay: false,
		onUnblock: onClose, 
		css: $.blockUI.defaults.growlCSS
	});
};

// plugin method for blocking element content
$.fn.block = function(opts) {
	return this.unblock({ fadeOut: 0 }).each(function() {
		if ($.css(this,'position') == 'static')
			this.style.position = 'relative';
		if ($.browser.msie)
			this.style.zoom = 1; // force 'hasLayout'
		install(this, opts);
	});
};

// plugin method for unblocking element content
$.fn.unblock = function(opts) {
	return this.each(function() {
		remove(this, opts);
	});
};

$.blockUI.version = 2.39; // 2nd generation blocking at no extra cost!

// override these in your code to change the default behavior and style
$.blockUI.defaults = {
	// message displayed when blocking (use null for no message)
	message:  '<h1>Please wait...</h1>',

	title: null,	  // title string; only used when theme == true
	draggable: true,  // only used when theme == true (requires jquery-ui.js to be loaded)
	
	theme: false, // set to true to use with jQuery UI themes
	
	// styles for the message when blocking; if you wish to disable
	// these and use an external stylesheet then do this in your code:
	// $.blockUI.defaults.css = {};
	css: {
		padding:	0,
		margin:		0,
		width:		'30%',
		top:		'40%',
		left:		'35%',
		textAlign:	'center',
		color:		'#000',
		border:		'3px solid #aaa',
		backgroundColor:'#fff',
		cursor:		'wait'
	},
	
	// minimal style set used when themes are used
	themedCSS: {
		width:	'30%',
		top:	'40%',
		left:	'35%'
	},

	// styles for the overlay
	overlayCSS:  {
		backgroundColor: '#000',
		opacity:	  	 0.6,
		cursor:		  	 'wait'
	},

	// styles applied when using $.growlUI
	growlCSS: {
		width:  	'350px',
		top:		'10px',
		left:   	'',
		right:  	'10px',
		border: 	'none',
		padding:	'5px',
		opacity:	0.6,
		cursor: 	'default',
		color:		'#fff',
		backgroundColor: '#000',
		'-webkit-border-radius': '10px',
		'-moz-border-radius':	 '10px',
		'border-radius': 		 '10px'
	},
	
	// IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w
	// (hat tip to Jorge H. N. de Vasconcelos)
	iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',

	// force usage of iframe in non-IE browsers (handy for blocking applets)
	forceIframe: false,

	// z-index for the blocking overlay
	baseZ: 1000,

	// set these to true to have the message automatically centered
	centerX: true, // <-- only effects element blocking (page block controlled via css above)
	centerY: true,

	// allow body element to be stetched in ie6; this makes blocking look better
	// on "short" pages.  disable if you wish to prevent changes to the body height
	allowBodyStretch: true,

	// enable if you want key and mouse events to be disabled for content that is blocked
	bindEvents: true,

	// be default blockUI will supress tab navigation from leaving blocking content
	// (if bindEvents is true)
	constrainTabKey: true,

	// fadeIn time in millis; set to 0 to disable fadeIn on block
	fadeIn:  200,

	// fadeOut time in millis; set to 0 to disable fadeOut on unblock
	fadeOut:  400,

	// time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock
	timeout: 0,

	// disable if you don't want to show the overlay
	showOverlay: true,

	// if true, focus will be placed in the first available input field when
	// page blocking
	focusInput: true,

	// suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity)
	applyPlatformOpacityRules: true,
	
	// callback method invoked when fadeIn has completed and blocking message is visible
	onBlock: null,

	// callback method invoked when unblocking has completed; the callback is
	// passed the element that has been unblocked (which is the window object for page
	// blocks) and the options that were passed to the unblock call:
	//	 onUnblock(element, options)
	onUnblock: null,

	// don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493
	quirksmodeOffsetHack: 4,

	// class name of the message block
	blockMsgClass: 'blockMsg'
};

// private data and functions follow...

var pageBlock = null;
var pageBlockEls = [];

function install(el, opts) {
	var full = (el == window);
	var msg = opts && opts.message !== undefined ? opts.message : undefined;
	opts = $.extend({}, $.blockUI.defaults, opts || {});
	opts.overlayCSS = $.extend({}, $.blockUI.defaults.overlayCSS, opts.overlayCSS || {});
	var css = $.extend({}, $.blockUI.defaults.css, opts.css || {});
	var themedCSS = $.extend({}, $.blockUI.defaults.themedCSS, opts.themedCSS || {});
	msg = msg === undefined ? opts.message : msg;

	// remove the current block (if there is one)
	if (full && pageBlock)
		remove(window, {fadeOut:0});

	// if an existing element is being used as the blocking content then we capture
	// its current place in the DOM (and current display style) so we can restore
	// it when we unblock
	if (msg && typeof msg != 'string' && (msg.parentNode || msg.jquery)) {
		var node = msg.jquery ? msg[0] : msg;
		var data = {};
		$(el).data('blockUI.history', data);
		data.el = node;
		data.parent = node.parentNode;
		data.display = node.style.display;
		data.position = node.style.position;
		if (data.parent)
			data.parent.removeChild(node);
	}

	$(el).data('blockUI.onUnblock', opts.onUnblock);
	var z = opts.baseZ;

	// blockUI uses 3 layers for blocking, for simplicity they are all used on every platform;
	// layer1 is the iframe layer which is used to supress bleed through of underlying content
	// layer2 is the overlay layer which has opacity and a wait cursor (by default)
	// layer3 is the message content that is displayed while blocking

	var lyr1 = ($.browser.msie || opts.forceIframe) 
		? $('<iframe class="blockUI" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+opts.iframeSrc+'"></iframe>')
		: $('<div class="blockUI" style="display:none"></div>');
	
	var lyr2 = opts.theme 
	 	? $('<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+ (z++) +';display:none"></div>')
	 	: $('<div class="blockUI blockOverlay" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>');

	var lyr3, s;
	if (opts.theme && full) {
		s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:fixed">' +
				'<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>' +
				'<div class="ui-widget-content ui-dialog-content"></div>' +
			'</div>';
	}
	else if (opts.theme) {
		s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:absolute">' +
				'<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>' +
				'<div class="ui-widget-content ui-dialog-content"></div>' +
			'</div>';
	}
	else if (full) {
		s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage" style="z-index:'+(z+10)+';display:none;position:fixed"></div>';
	}			 
	else {
		s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement" style="z-index:'+(z+10)+';display:none;position:absolute"></div>';
	}
	lyr3 = $(s);

	// if we have a message, style it
	if (msg) {
		if (opts.theme) {
			lyr3.css(themedCSS);
			lyr3.addClass('ui-widget-content');
		}
		else 
			lyr3.css(css);
	}

	// style the overlay
	if (!opts.theme && (!opts.applyPlatformOpacityRules || !($.browser.mozilla && /Linux/.test(navigator.platform))))
		lyr2.css(opts.overlayCSS);
	lyr2.css('position', full ? 'fixed' : 'absolute');

	// make iframe layer transparent in IE
	if ($.browser.msie || opts.forceIframe)
		lyr1.css('opacity',0.0);

	//$([lyr1[0],lyr2[0],lyr3[0]]).appendTo(full ? 'body' : el);
	var layers = [lyr1,lyr2,lyr3], $par = full ? $('body') : $(el);
	$.each(layers, function() {
		this.appendTo($par);
	});
	
	if (opts.theme && opts.draggable && $.fn.draggable) {
		lyr3.draggable({
			handle: '.ui-dialog-titlebar',
			cancel: 'li'
		});
	}

	// ie7 must use absolute positioning in quirks mode and to account for activex issues (when scrolling)
	var expr = setExpr && (!$.boxModel || $('object,embed', full ? null : el).length > 0);
	if (ie6 || expr) {
		// give body 100% height
		if (full && opts.allowBodyStretch && $.boxModel)
			$('html,body').css('height','100%');

		// fix ie6 issue when blocked element has a border width
		if ((ie6 || !$.boxModel) && !full) {
			var t = sz(el,'borderTopWidth'), l = sz(el,'borderLeftWidth');
			var fixT = t ? '(0 - '+t+')' : 0;
			var fixL = l ? '(0 - '+l+')' : 0;
		}

		// simulate fixed position
		$.each([lyr1,lyr2,lyr3], function(i,o) {
			var s = o[0].style;
			s.position = 'absolute';
			if (i < 2) {
				full ? s.setExpression('height','Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.boxModel?0:'+opts.quirksmodeOffsetHack+') + "px"')
					 : s.setExpression('height','this.parentNode.offsetHeight + "px"');
				full ? s.setExpression('width','jQuery.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"')
					 : s.setExpression('width','this.parentNode.offsetWidth + "px"');
				if (fixL) s.setExpression('left', fixL);
				if (fixT) s.setExpression('top', fixT);
			}
			else if (opts.centerY) {
				if (full) s.setExpression('top','(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"');
				s.marginTop = 0;
			}
			else if (!opts.centerY && full) {
				var top = (opts.css && opts.css.top) ? parseInt(opts.css.top) : 0;
				var expression = '((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + '+top+') + "px"';
				s.setExpression('top',expression);
			}
		});
	}

	// show the message
	if (msg) {
		if (opts.theme)
			lyr3.find('.ui-widget-content').append(msg);
		else
			lyr3.append(msg);
		if (msg.jquery || msg.nodeType)
			$(msg).show();
	}

	if (($.browser.msie || opts.forceIframe) && opts.showOverlay)
		lyr1.show(); // opacity is zero
	if (opts.fadeIn) {
		var cb = opts.onBlock ? opts.onBlock : noOp;
		var cb1 = (opts.showOverlay && !msg) ? cb : noOp;
		var cb2 = msg ? cb : noOp;
		if (opts.showOverlay)
			lyr2._fadeIn(opts.fadeIn, cb1);
		if (msg)
			lyr3._fadeIn(opts.fadeIn, cb2);
	}
	else {
		if (opts.showOverlay)
			lyr2.show();
		if (msg)
			lyr3.show();
		if (opts.onBlock)
			opts.onBlock();
	}

	// bind key and mouse events
	bind(1, el, opts);

	if (full) {
		pageBlock = lyr3[0];
		pageBlockEls = $(':input:enabled:visible',pageBlock);
		if (opts.focusInput)
			setTimeout(focus, 20);
	}
	else
		center(lyr3[0], opts.centerX, opts.centerY);

	if (opts.timeout) {
		// auto-unblock
		var to = setTimeout(function() {
			full ? $.unblockUI(opts) : $(el).unblock(opts);
		}, opts.timeout);
		$(el).data('blockUI.timeout', to);
	}
};

// remove the block
function remove(el, opts) {
	var full = (el == window);
	var $el = $(el);
	var data = $el.data('blockUI.history');
	var to = $el.data('blockUI.timeout');
	if (to) {
		clearTimeout(to);
		$el.removeData('blockUI.timeout');
	}
	opts = $.extend({}, $.blockUI.defaults, opts || {});
	bind(0, el, opts); // unbind events

	if (opts.onUnblock === null) {
		opts.onUnblock = $el.data('blockUI.onUnblock');
		$el.removeData('blockUI.onUnblock');
	}

	var els;
	if (full) // crazy selector to handle odd field errors in ie6/7
		els = $('body').children().filter('.blockUI').add('body > .blockUI');
	else
		els = $('.blockUI', el);

	if (full)
		pageBlock = pageBlockEls = null;

	if (opts.fadeOut) {
		els.fadeOut(opts.fadeOut);
		setTimeout(function() { reset(els,data,opts,el); }, opts.fadeOut);
	}
	else
		reset(els, data, opts, el);
};

// move blocking element back into the DOM where it started
function reset(els,data,opts,el) {
	els.each(function(i,o) {
		// remove via DOM calls so we don't lose event handlers
		if (this.parentNode)
			this.parentNode.removeChild(this);
	});

	if (data && data.el) {
		data.el.style.display = data.display;
		data.el.style.position = data.position;
		if (data.parent)
			data.parent.appendChild(data.el);
		$(el).removeData('blockUI.history');
	}

	if (typeof opts.onUnblock == 'function')
		opts.onUnblock(el,opts);
};

// bind/unbind the handler
function bind(b, el, opts) {
	var full = el == window, $el = $(el);

	// don't bother unbinding if there is nothing to unbind
	if (!b && (full && !pageBlock || !full && !$el.data('blockUI.isBlocked')))
		return;
	if (!full)
		$el.data('blockUI.isBlocked', b);

	// don't bind events when overlay is not in use or if bindEvents is false
	if (!opts.bindEvents || (b && !opts.showOverlay)) 
		return;

	// bind anchors and inputs for mouse and key events
	var events = 'mousedown mouseup keydown keypress';
	b ? $(document).bind(events, opts, handler) : $(document).unbind(events, handler);

// former impl...
//	   var $e = $('a,:input');
//	   b ? $e.bind(events, opts, handler) : $e.unbind(events, handler);
};

// event handler to suppress keyboard/mouse events when blocking
function handler(e) {
	// allow tab navigation (conditionally)
	if (e.keyCode && e.keyCode == 9) {
		if (pageBlock && e.data.constrainTabKey) {
			var els = pageBlockEls;
			var fwd = !e.shiftKey && e.target === els[els.length-1];
			var back = e.shiftKey && e.target === els[0];
			if (fwd || back) {
				setTimeout(function(){focus(back)},10);
				return false;
			}
		}
	}
	var opts = e.data;
	// allow events within the message content
	if ($(e.target).parents('div.' + opts.blockMsgClass).length > 0)
		return true;

	// allow events for content that is not being blocked
	return $(e.target).parents().children().filter('div.blockUI').length == 0;
};

function focus(back) {
	if (!pageBlockEls)
		return;
	var e = pageBlockEls[back===true ? pageBlockEls.length-1 : 0];
	if (e)
		e.focus();
};

function center(el, x, y) {
	var p = el.parentNode, s = el.style;
	var l = ((p.offsetWidth - el.offsetWidth)/2) - sz(p,'borderLeftWidth');
	var t = ((p.offsetHeight - el.offsetHeight)/2) - sz(p,'borderTopWidth');
	if (x) s.left = l > 0 ? (l+'px') : '0';
	if (y) s.top  = t > 0 ? (t+'px') : '0';
};

function sz(el, p) {
	return parseInt($.css(el,p))||0;
};

})(jQuery);

/**
 *  Prevent Double form submission
*/

jQuery.fn.preventDoubleSubmit = function() {
	  jQuery(this).submit(function() {
	    if (this.beenSubmitted)
	      return false;
	    else
	      this.beenSubmitted = true;
	  });
	};

/**
 *  placeholders
*/

(function($){if("placeholder"in document.createElement("input"))return;$(document).ready(function(){$(':input[placeholder]').each(function(){setupPlaceholder($(this));});$('form').submit(function(e){clearPlaceholdersBeforeSubmit($(this));});});function setupPlaceholder(input){var placeholderText=input.attr('placeholder');if(input.val()==='')input.val(placeholderText);input.bind({focus:function(e){if(input.val()===placeholderText)input.val('');},blur:function(e){if(input.val()==='')input.val(placeholderText);}});}
function clearPlaceholdersBeforeSubmit(form){form.find(':input[placeholder]').each(function(){var el=$(this);if(el.val()===el.attr('placeholder'))el.val('');});}})(jQuery);