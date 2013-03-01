(
	function(){
		tinymce.create(
			"tinymce.plugins.ApptivoecommerceShortcodes",
			{
				init: function(d,e) {},
				createControl:function(d,e)
				{
				
					if(d=="apptivo-ecommerce_shortcodes_button"){
					
						d=e.createMenuButton( "apptivo-ecommerce_shortcodes_button",{
							title:"Insert Apptivo eCommerce Shortcode",
							image:false
							});
							
							var a=this;d.onRenderMenu.add(function(c,b){
								
										a.addImmediate(b,"Cart","[apptivo_ecommerce_cart]" );
										a.addImmediate(b,"Authorize.Net Checkout",'[apptivo_ecommerce_secure_checkout]');
										a.addImmediate(b,"Paypal/Google Checkout","[apptivo_ecommerce_checkout]" );
										a.addImmediate(b,"Register",'[apptivo_ecommerce_register]');
										a.addImmediate(b,"Login",'[apptivo_ecommerce_login]');
										a.addImmediate(b,"Logout",'[apptivo_ecommerce_logout]');
										a.addImmediate(b,"My Account",'[apptivo_ecommerce_my_account]');
										a.addImmediate(b,"Thanks",'[apptivo_ecommerce_thankyou]');
										b.addSeparator();
										a.addImmediate(b,"Recent Products",'[apptivo_ecommerce_recent_products featured="" orderby="" order="" per_page="8" columns="4" pagination_type="bottom"]');
										a.addImmediate(b,"Feautured Products",'[apptivo_ecommerce_featured_products orderby="" order=""  per_page="8" columns="4" pagination_type="bottom"]');
										a.addImmediate(b,"Products by category ID",'[apptivo_ecommerce_products_by_category category_id="" featured="" orderby="" order="" per_page="8" columns="4" pagination_type="bottom"]');
										a.addImmediate(b,"Products by price",'[apptivo_ecommerce_products_by_price min="" max="" featured="" orderby="" order="" per_page="8" columns="4" pagination_type="bottom"]');
										

							});
						return d
					
					} // End IF Statement
					
					return null
				},
		
				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}
				
			}
		);
		
		tinymce.PluginManager.add( "ApptivoecommerceShortcodes", tinymce.plugins.ApptivoecommerceShortcodes);
	}
	
)();

(
		function(){
			tinymce.create(
				"tinymce.plugins.ApptivoecombusinessShortcodes",
				{
					init: function(d,e) {},
					createControl:function(d,e)
					{
					
						if(d=="apptivo-ecommerce_shortcodes_button"){
						
							d=e.createMenuButton( "apptivo-ecommerce_shortcodes_button",{
								title:"Insert Apptivo Shortcode",
								image:false
								});
								
								var a=this;d.onRenderMenu.add(function(c,b){
									
									c=b.addMenu({title:"eCommerce"});
									a.addImmediate(c,"Cart","[apptivo_ecommerce_cart]" );
									a.addImmediate(c,"Authorize.Net Checkout",'[apptivo_ecommerce_secure_checkout]');
									a.addImmediate(c,"Paypal/Google Checkout","[apptivo_ecommerce_checkout]" );
									a.addImmediate(c,"Register",'[apptivo_ecommerce_register]');
									a.addImmediate(c,"Login",'[apptivo_ecommerce_login]');
									a.addImmediate(c,"Logout",'[apptivo_ecommerce_logout]');
									a.addImmediate(c,"My Account",'[apptivo_ecommerce_my_account]');
									a.addImmediate(c,"Thanks",'[apptivo_ecommerce_thankyou]');
									c.addSeparator();
									a.addImmediate(c,"Recent Products",'[apptivo_ecommerce_recent_products featured="" orderby="" order="" per_page="8" columns="4" pagination_type="bottom"]');
									a.addImmediate(c,"Feautured Products",'[apptivo_ecommerce_featured_products orderby="" order=""  per_page="8" columns="4" pagination_type="bottom"]');
									a.addImmediate(c,"Products by category ID",'[apptivo_ecommerce_products_by_category category_id="" featured="" orderby="" order=""  per_page="8" columns="4" pagination_type="bottom"]');
									a.addImmediate(c,"Products by price",'[apptivo_ecommerce_products_by_price min="" max="" featured="" orderby="" order="" per_page="8" columns="4" pagination_type="bottom"]');
									
									b.addSeparator();
									
									a.addImmediate(b,"Contact Form", '[apptivocontactform name="&lt;&lt;contactform name&gt;&gt;"]');
									a.addImmediate(b,"Cases Form", '[apptivo_cases]');
																	
									c=b.addMenu({title:"Testimonials"});
											a.addImmediate(c,"Full View","[apptivo_testimonials_fullview]" );
											a.addImmediate(c,"Inline View","[apptivo_testimonials_inline]" );
	                               
									c=b.addMenu({title:"News"});
											a.addImmediate(c,"Full View","[apptivo_news_fullview]" );
											a.addImmediate(c,"Inline View","[apptivo_news_inline]" );
											
									c=b.addMenu({title:"Events"});
											a.addImmediate(c,"Full View","[apptivo_events_fullview]" );
											a.addImmediate(c,"Inline View","[apptivo_events_inline]" );
									
									b.addSeparator();
									
									a.addImmediate(b,"Newsletter", '[apptivonewsletterform name="&lt;&lt;newsletterform name&gt;&gt;"]');
									
									c=b.addMenu({title:"Jobs"});
											a.addImmediate(c,"Job Lists","[apptivo_jobs]" );
											a.addImmediate(c,"Job Search Form",'[apptivo_job_searchform name="jsform"]');
											a.addImmediate(c,"Job Description","[apptivo_job_description]" );
											a.addImmediate(c,"Job Applicant Form",'[apptivo_job_applicantform name="jaform"]');
									

								});
							return d
						
						} // End IF Statement
						
						return null
					},
			
					addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}
					
				}
			);
			
			tinymce.PluginManager.add( "ApptivoecombusinessShortcodes", tinymce.plugins.ApptivoecombusinessShortcodes);
		}
)();