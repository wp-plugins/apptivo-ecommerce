<?php
/**
 * Paypal Redirect Page
 * @package  Apptivo eCommerce
 * @author Rajkumar <rmohanasundaram[at]apptivo[dot]com>
 */
// Apptivo Paypal Redirections.
if (!session_id()) session_start();
$token =$_GET['token'];
$PayerID =$_GET['PayerID'];
$amount =$_GET['amount'];
$payapl_req = array( 'token'   => $token,'payerid' => $PayerID,'amount'  => $amount);
$thanks_pageid = $_SESSION['apptivo_ecommerce_thanks_page_id'];  //get thanks page ID.

if((strlen(trim($token)) != 0  && strlen(trim($PayerID)) != 0 && strlen(trim($amount)) != 0 ))
{
$_SESSION['apptivo_paypal_payerid_token_amt'] = $payapl_req;
header("Location: $thanks_pageid");
exit;
}else {
unset($_SESSION['apptivo_paypal_payerid_token_amt']);	
header("Location: /");
exit;
}