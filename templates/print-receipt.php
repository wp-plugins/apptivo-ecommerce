<?php
/**
 * Print Receipt Template.
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
$orderno = $_GET['orderno'];
$order = get_order_details($orderno)->return;
if(empty($order) || $order->orderId != $_SESSION['apptivo_ecommerce_orderid'])
{
	wp_safe_redirect('/');
	exit;
}
$billingDetails = $order->billingContactDetails;
$shippingDetails = $order->shippingContactDetails;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<title>Apptivo Ecommerce Print Receipt</title>
<style type='text/css'>
* { margin: 0; padding: 0; }
body { font: 14px/1.4 Georgia, serif; }
#page-wrap { width: 800px; margin: 0 auto; }
table { border-collapse: collapse; }
table td, table th { border: 1px solid black; padding: 5px; }
#header { height: 15px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }
#address { width: 250px; height: 150px; float: left; }
#address span,#address p{display:block;}
#customer { overflow: hidden; }
.due,#subtotal,.price,.cost,.itemqty{float:right;}
#logo { text-align: right; float: right; position: relative; margin-top: 20px;margin-bottom: 20px; border: 1px solid #fff; max-width: 540px; max-height: 200px; overflow: hidden; }
#customer-title { font-size: 20px; font-weight: bold; float: left; }
#meta { margin-top: 1px; width: 300px; float: right; }
#meta td { text-align: right;  }
#meta td.meta-head { text-align: left; background: #eee; }
#meta td textarea { width: 100%; height: 20px; text-align: right; }
#items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
#items th { background: #eee; }
#items textarea { width: 80px; height: 50px; }
#items tr.item-row td { border: 1px solid #000; vertical-align: top; }
#items td.description { width: 300px; }
#items td.item-name { width: 175px; }
#items td.description textarea, #items td.item-name textarea { width: 100%; }
#items td.total-line { border-right: 0; text-align: right; }
#items td.total-value { border-left: 0; padding: 10px; }
#items td.total-value textarea { height: 20px; background: none; }
#items td.balance { background: #eee; }
#items td.blank { border: 0; }
#terms { text-align: center; margin: 20px 0 0 0; }
#terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
#bilto{width:40%;float:left;}#shipto{width:40%;float:right;}
</style>
	<link rel='stylesheet' type='text/css' href='css/print.css' media="print" />
</head>

<body>

	<div id="page-wrap">

<div id="identity">
            <div id="logo">
            <?php $receipt_logo = get_option('apptivo_ecommerce_print_receipt_logo');
            if(strlen(trim($receipt_logo)) == 0):
            ?>
                <h1 id="sitename"><?php bloginfo('name'); ?></h1>
            <?php 
            else:
            ?>
            <img src="<?php echo $receipt_logo; ?>" alt="<?php bloginfo('name'); ?>" />
            <?php 
            endif;
            ?>
                
            </div>
		</div>
		<div style="clear:both"></div>
		<div id="customer">

<div id="address">
<?php echo $content = apply_filters('the_content', get_option('apptivo_ecommerce_print_receipt_address'));  ?>
</div>

            <table id="meta">
                <tr>
                    <td class="meta-head">Order Number</td>
                    <td><?php echo $order->orderNumber; ?></td>
                </tr>
                <tr>

                    <td class="meta-head">Date</td>
                    <td><?php echo date(get_option('date_format'), strtotime($order->orderedDate)); ?></td>
                </tr>
                <tr>
                    <td class="meta-head">Total</td>
                    <td><div class="due"><?php echo apptivo_ecommerce_price($order->totalAmount)?></div></td>
                </tr>

            </table>
 

		
		</div>
		
		 <div style="width:100%" >
 <?php if($billingDetails->addressId) :?>
<div id="bilto" >
<strong> Bill To<br /></strong>
<?php echo $billingDetails->firstName.' '.$billingDetails->lastName.'<br />';
echo $billingDetails->address1.' '.$billingDetails->address2.'<br />';
echo $billingDetails->city.', '.$billingDetails->provinceAndState.', '.$billingDetails->country.'<br />';  
echo 'Zip code: '.$billingDetails->postalCode;
?>
</div>
<?php endif; ?>


 <?php if(trim($shippingDetails->firstName) != '') :?>
<div id="shipto" >
<strong> Ship To<br /></strong>
<?php echo $shippingDetails->firstName.' '.$shippingDetails->lastName.'<br />';
echo $shippingDetails->address1.' '.$shippingDetails->address2.'<br />';
echo $shippingDetails->city.', '.$shippingDetails->provinceAndState.', '.$shippingDetails->country.'<br />';  
echo 'Zip code: '.$shippingDetails->postalCode;
?>
</div>
<?php endif; ?>

		<table id="items">
		  <tr>
		      <th class="product-name">Product</th>
			  <th class="product-price">Unit Price</th>		
			  <th class="product-quantity">Quantity</th>
		      <th class="product-subtotal">Total</th>		      
		  </tr>		  
		  <?php
		     $orderLineDetails = app_convertObjectToArray($order->orderLineDetails); 
		     $sub_toal = 0;
		     foreach($orderLineDetails as $orderdetails) :
		      if( $orderdetails->lineType == 'ITEM') : ?>
		   <tr class="item-row">
		      <td class="item-name"><?php echo $orderdetails->itemName; ?>
             <?php if($orderdetails->trackColor != '') :?>
				<br /><span class="order_color">Color:  <?php echo $orderdetails->trackColor; ?></span>
		     <?php endif; ?>
			 <?php if($orderdetails->trackSize != '') :?>
				<br /><span class="order_color">Size:  <?php echo $orderdetails->trackSize;?></span>
			<?php endif; ?>	
		      <td><span class="cost"><?php echo apptivo_ecommerce_price($orderdetails->unitPrice)?></span></td>
		      <td><span class="itemqty"><?php echo $orderdetails->quantity; ?></span></td>
		      <td><span class="price"><?php echo apptivo_ecommerce_price($orderdetails->totalPrice)?></span></td>
		  </tr>
		  <?php $sub_toal += $orderdetails->totalPrice;
		   endif;
		   endforeach; ?>
		   <tr>
		      <td class="blank"> </td><td class="blank"> </td>
		      <td class="total-line">Subtotal</td>
		      <td class="total-value"><div id="subtotal"><?php echo apptivo_ecommerce_price($sub_toal)?></div></td>
		  </tr>	  
		  
		  <?php  foreach($orderLineDetails as $orderdetails) : 
		    if( $orderdetails->lineType != 'ITEM') :
		     
		    if(strtolower(trim($orderdetails->lineType)) == 'shipping'){		    	
		    			    	
		    	if( strlen($orderdetails->itemName) != 0 )
		    	{
		    		$orderdetails->itemName = "( ".$orderdetails->itemName." ) Shipping Amount";
		    	}
		    }
		    if($orderdetails->totalPrice > 0 ):
		    ?>
		    <tr>
		      <td class="blank"> </td><td class="blank"> </td>
		      <td class="total-line"><?php echo (strlen($orderdetails->itemName)==0)?ucfirst(strtolower($orderdetails->lineType)):$orderdetails->itemName; ?></td>
		      <td class="total-value"><div id="subtotal"><?php echo apptivo_ecommerce_price($orderdetails->totalPrice)?></div></td>
		  </tr>
		  
		     <?php endif; 
		     endif;
		   endforeach; ?>
		  <tr>
		      <td  class="blank"> </td><td  class="blank"> </td>
		      <td class="total-line balance">Grand Total</td>
		      <td class="total-value balance"><div class="due"><?php echo apptivo_ecommerce_price($order->totalAmount)?></div></td>
		  </tr>
		
		</table>
		
		<div id="terms">
		 
		</div>
	
	</div>
	
</body>
<script>
function loadprint(){
	 window.print();
}
window.onload = loadprint();
</script>
</html>