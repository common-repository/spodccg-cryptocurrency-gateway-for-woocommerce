<?php
/**
 * Plugin Name: Spodccg - Cryptocurrency gateway for Woocommerce
 * Plugin URI: https://smdev.au/projects/
 * Author: SMDEV
 * Author URI: https://smdev.au
 * Description: Cryptocurrency payment gateway for Woocommerce.
 * Version: 1.0.1
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: spod-ccg-txt
 * 
 * CryptoCurrency Gateway For Woocommerce Plugin
 * Copyright (C) 2022-2023, Steven Mihelakis
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * Class WC_Gateway_spodccg file.
 *  
 * @package Woocommerce/spodccg
 * 
*/ 

//Security - exit file if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
//Check if woocommerce is active
if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ) return;

//extend the payment gateways to include our new CC gateway
/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + cryptocurrency gateway
 */
function add_to_spodccg_payment_gateway($gateways){
    $gateways[] = 'WC_Gateway_spodccg';
    return $gateways;
}
add_filter ('woocommerce_payment_gateways', 'add_to_spodccg_payment_gateway');

//If the Woocommerce Payment Gateway function exists load our gateway class
add_action('plugins_loaded', 'spod_ccg_payment_init',11);

function spod_ccg_payment_init(){
   if (class_exists('WC_Payment_Gateway')) {
    
        require_once plugin_dir_path( __FILE__ ) . '/inc/class-wc-payment-gateway-spodccg.php';
        require_once plugin_dir_path( __FILE__ ) . '/inc/spodccg-checkout-description.php';
        require_once plugin_dir_path( __FILE__ ) . '/inc/spodccg-invoice-details.php';
    
  }
}

//load custom css
function spod_wooccg_styles() {
    wp_enqueue_style( 'spod_spodccg_style',  plugin_dir_url( __FILE__ ) . 'css/custom.css' );
}
add_action('wp_enqueue_scripts', 'spod_wooccg_styles', 9999);


