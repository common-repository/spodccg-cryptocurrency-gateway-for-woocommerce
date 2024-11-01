<?php

//Security - exit file if accessed directly
if (!defined('ABSPATH')) {
    exit;
}



add_action('woocommerce_checkout_update_order_meta', 'spod_spodccg_checkout_update_order_meta', 10, 1);
add_action('woocommerce_admin_order_data_after_billing_address', 'spod_spodccg_order_data_after_billing_address', 10, 1);
add_action('woocommerce_order_details_after_order_table', 'spod_spodccg_order_items_table', 10, 1 ); 
add_action('woocommerce_email_after_order_table', 'spod_spodccg_email_after_order_table', 10, 4 );
add_action('woocommerce_order_details_before_order_table', 'spod_spodccg_order_details_before_order_table', 10, 1 );


//add ADA details to order in dashboard
function spod_spodccg_checkout_update_order_meta($order_id){

    if( isset ( $_POST['ccg_txid_field']) || ! empty($_POST['ccg_txid_field'] ) ) {

        $ccgtxtfield = sanitize_text_field( $_POST['ccg_txid_field'] );
        update_post_meta( $order_id, 'ccg_txid_field', $ccgtxtfield );

    }
    if( isset ( $_POST['spodccg-total-ccg-hidden']) || ! empty($_POST['spodccg-total-ccg-hidden'] ) ) {

        $ccgtxtfieldhidden = sanitize_text_field( $_POST['spodccg-total-ccg-hidden'] );
        update_post_meta( $order_id, 'spodccg-total-ccg-hidden', $ccgtxtfieldhidden);
     }

     if( isset ( $_POST['spodccg-rate-ccg-hidden']) || ! empty($_POST['spodccg-rate-ccg-hidden'] ) ) {

        $ccgtxtfieldratehidden = sanitize_text_field( $_POST['spodccg-rate-ccg-hidden'] );
        update_post_meta( $order_id, 'spodccg-rate-ccg-hidden', $ccgtxtfieldratehidden);
     }

}

//show CC   details in order in dashboard
function spod_spodccg_order_data_after_billing_address($order){
    $GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];
    if( 'spodccg' == $order->get_payment_method() ) {
       $ccgtxid =  get_post_meta( $order->get_id(), 'ccg_txid_field', true );
  
       if ($GLOBALS['payment_gateway_spodccg']->txidenable == 'yes') :  
        _e('<strong>Transaction ID:</strong> '. $ccgtxid .'
        <br><br><strong>Blockchain Explorer:</strong> <a href="'.$GLOBALS['payment_gateway_spodccg']->blockchain.'" target="_blank">'.$GLOBALS['payment_gateway_spodccg']->blockchain.'</a>
        <br><br>','spod-ccg-txt');
       endif;
    _e( '<p><strong>Total '.$GLOBALS['payment_gateway_spodccg']->symbol.' Sent:</strong><br>'. get_post_meta( $order->get_id(), 'spodccg-total-ccg-hidden', true ) .'</p>','spod-ccg-txt');
 }
}

//add thankyou message above order details page
function spod_spodccg_order_details_before_order_table($order){
    $GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];
    if( 'spodccg' == $order->get_payment_method() ) {
    _e( '<p><strong>Thank you very much for your order and using '.$GLOBALS['payment_gateway_spodccg']->symbol.'!
        <br>We will confirm the transaction shortly and send you a processing order email.</strong></p>','spod-ccg-txt');
    }
}

//add the details to the order details thankyou page
function spod_spodccg_order_items_table( $order ) { 
    $GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];
    if( 'spodccg' == $order->get_payment_method() ) {

        $ccgtxid =  get_post_meta( $order->get_id(), 'ccg_txid_field', true );

    _e( '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details" style="margin-top:0;">
               <tr class="woocommerce-table__line-item order_item">
                  <td class="woocommerce-table__product-total product-total">','spod-ccg-txt');
                  if ($GLOBALS['payment_gateway_spodccg']->txidenable == 'yes') :  
                    _e('<strong>Transaction ID:</strong> '. $ccgtxid .'
                        <br><br><strong>Blockchain Explorer:</strong> <a href="'.$GLOBALS['payment_gateway_spodccg']->blockchain.'" target="_blank">'.$GLOBALS['payment_gateway_spodccg']->blockchain.'</a>
                    <br><br>','spod-ccg-txt');
                endif;
                  _e('<strong>Total '.$GLOBALS['payment_gateway_spodccg']->symbol.' Invoiced:</strong> '. get_post_meta( $order->get_id(), 'spodccg-total-ccg-hidden', true ) .'
                  <br><br>
                  <strong>Exchange Rate at time of order:</strong> ' . $GLOBALS['csym_spodccg'] . get_post_meta ($order->get_id(), 'spodccg-rate-ccg-hidden', true). ' ' . $GLOBALS['ccode_spodccg'] .'
                  </td>
                  </tr></table>','spod-ccg-txt');
    }
}

//add the CC details to the email invoices
function spod_spodccg_email_after_order_table( $order, $sent_to_admin, $plain_text, $email ) { 
    $GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];
    if( 'spodccg' == $order->get_payment_method() ) {

         $ccgtxid =  get_post_meta( $order->get_id(), 'ccg_txid_field', true );

    _e( '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details" style="margin-top:0;">
               <tr class="woocommerce-table__line-item order_item">
                  <td class="woocommerce-table__product-total product-total">','spod-ccg-txt');
                  if ($GLOBALS['payment_gateway_spodccg']->txidenable == 'yes') :  
                      _e('<strong>Transaction ID:</strong> '. $ccgtxid .'
                      <br><br><strong>Blockchain Explorer:</strong> <a href="'.$GLOBALS['payment_gateway_spodccg']->blockchain.'" target="_blank">'.$GLOBALS['payment_gateway_spodccg']->blockchain.'</a>
                      <br><br>','spod-ccg-txt');
                  endif;    
                  _e('<strong>Total '.$GLOBALS['payment_gateway_spodccg']->symbol.' Invoiced:</strong> '. get_post_meta( $order->get_id(), 'spodccg-total-ccg-hidden', true ) .'
                  <br><br>
                  <strong>Exchange Rate at time of order:</strong> ' . $GLOBALS['csym_spodccg'] . get_post_meta ($order->get_id(), 'spodccg-rate-ccg-hidden', true). $GLOBALS['ccode_spodccg'] .'
                  <br><br>
                  </td>
                  </tr></table>','spod-ccg-txt');
    }
}