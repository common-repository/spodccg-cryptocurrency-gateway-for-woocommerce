<?php

//Security - exit file if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


add_filter('woocommerce_gateway_description', 'spod_spodccg_description_fields', 20, 2);
add_action('woocommerce_checkout_process', 'spod_spodccg_description_fields_validation');

//get currency code set in woocommerce
$currency = get_woocommerce_currency();
$currencySymbol = get_woocommerce_currency_symbol();

$GLOBALS['ccode_spodccg'] = $currency;
$GLOBALS['csym_spodccg'] = $currencySymbol;

//API Call Function
function spod_spodccg_description_fields($description, $payment_id){
//get the admin settings
$GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];

        //apikey from admin settings 
        $cmcAPICCG = $GLOBALS['payment_gateway_spodccg']->apikey;
        //Ticker from admin settings
        $spodccgSymbol = $GLOBALS['payment_gateway_spodccg']->symbol;
        //set headers for api key
        $args = array(
            'headers' => array(
              'Content-Type'      => 'application/json',
              'X-CMC_PRO_API_KEY' => $cmcAPICCG, 
            )
        );
        // Send the request to coinmarketcap, save the response
        $response = wp_remote_get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol='.$spodccgSymbol.'&convert='.$GLOBALS['ccode_spodccg'], $args);
        $body     = wp_remote_retrieve_body( $response );
        // Decode the Data
        $apiDataCCG = json_decode($body,true);
        //set variable for CC => Store Currency exchange
        $rateCCG = $apiDataCCG['data'][$spodccgSymbol]['quote'][$GLOBALS['ccode_spodccg']]['price'];


    if ('spodccg' !== $payment_id){
        return $description;
    }

    ob_start();

    //get the final total price in the cart 
    $orderTotalCCG = WC()->cart->get_total('spodccgarg');

    // divide the cart total with the CC to Store Currency exchange rate
    $totalCCG = $orderTotalCCG / $rateCCG; 

     //echo $orderTotalCCG;
     //echo '<br>'.$rateCCG;
 
    //Begin Front-end Checkout Panel
    _e('<div id="spodccg-field-box">
          <p style="margin:1em 0;"><strong>Send the Total Amount in '.$spodccgSymbol.' required below, from your wallet to the address below:</strong>
          <p class="spodccg-total-ccg" id="spodccg_total_ccg">Total Amount in '.$spodccgSymbol.' to send: <strong>'. $totalCCG .'</strong></p>
          <p><small>Todays Exchange Rate: 1 '.$spodccgSymbol.' = '. $rateCCG . ' ' . $GLOBALS['ccode_spodccg'] .'</small> <small style="color:#898989;">Provided by coinmarketcap.com</small></p>

          <div class="spodccg-ccg-txt-tooltip">
               <a href="javascript:spod_copy_ccg_amount();"><span id="spodccgtooltip-textamount">Copy Amount</span> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 19V1H17V5H21V23H7V19H3ZM15 17V3H5V17H15ZM17 7V19H9V21H19V7H17Z" fill="#fff"></path></svg></a> 
           </div>
           <input id="spodccgamount-hidden" name="spodccgamount-hidden" type="text" value="'. $totalCCG .'" readonly="true" /> 

          <div class="spodccg-payment-panel">', 'spod-ccg-txt');
    if ($GLOBALS['payment_gateway_spodccg']->qrcode) :
            _e('<div id="spodccg-qrcode-panel"><img src="'. $GLOBALS['payment_gateway_spodccg']->qrcode .'" width="152" alt="receiver address qrcode" class="spodccg-qrcode-img"></div>', 'spod-ccg-txt');
    endif;
          _e( '<div class="spodccg-ccg-txt-address"> 
             <p id="spodccgaddress">'. $GLOBALS['payment_gateway_spodccg']->address .'</p>
             <input id="spodccgaddress-hidden" name="spodccgaddress-hidden" type="text" value="'. $GLOBALS['payment_gateway_spodccg']->address .'" readonly="true" />   
             </div>
             <div class="spodccg-ccg-txt-tooltip">
               <a href="javascript:spod_copy_ccg_add();"><span id="spodccgtooltip-text">Copy Address</span> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 19V1H17V5H21V23H7V19H3ZM15 17V3H5V17H15ZM17 7V19H9V21H19V7H17Z" fill="#fff"></path></svg></a> 
           </div>
         </div>', 'spod-ccg-txt'); 
    if ($GLOBALS['payment_gateway_spodccg']->txidenable == 'yes') :   
        _e('<p style="margin:3em 0 1em;"><strong>Once the transaction is complete, please enter the transaction ID into the below field.</strong></p>', 'spod-ccg-txt');
          woocommerce_form_field(
              'ccg_txid_field',
                    array(
                        'type' => 'textarea',
                        'label' => __('Transaction ID', 'spod-ccg-txt'),
                        'class' => array('form-row', 'form-row-wide'),
                        'required' => true,
                       )
                    );  
     endif;             
             _e(  '<p style="margin-bottom:0;"><input id="spodccg-total-ccg-hidden" name="spodccg-total-ccg-hidden" type="hidden" value="'. $totalCCG .'" readonly="true" />  <input id="spodccg-rate-ccg-hidden" name="spodccg-rate-ccg-hidden" type="hidden" value="'. $rateCCG .'" readonly="true" /></p>');        
             _e('<p style="margin:3em 0 1em;"><strong>Click the Place Order button below to submit the order!</strong></p>', 'spod-ccg-txt');
                    
    if ($GLOBALS['payment_gateway_spodccg']->disclaimer) :
        _e('<p class="spodccg-cc-disclaimer" style="margin:3em 0 1em;"><small><strong>Disclaimer:</strong><br>' . $GLOBALS['payment_gateway_spodccg']->disclaimer . '</small></p>', 'spod-ccg-txt');
     endif;
        _e( '</div>', 'spod-ccg-txt');

    $description .= ob_get_clean();

    return $description;
}

//make sure transaction-id field is filled out and validated
function spod_spodccg_description_fields_validation(){
    $GLOBALS['payment_gateway_spodccg'] = WC()->payment_gateways->payment_gateways()['spodccg'];
    if ($GLOBALS['payment_gateway_spodccg']->txidenable == 'yes') :   
        //if txid is not set
        if( 'spodccg' === $_POST['payment_method'] && ! isset ($_POST['ccg_txid_field']) ) {
            wc_add_notice( 'Please enter the Transaction ID', 'error' );
        
        //if txid is empty
        }elseif ( 'spodccg' === $_POST['payment_method'] && empty($_POST['ccg_txid_field'] ) ) {
            wc_add_notice( 'Please enter the Transaction ID', 'error' ); 
         }
    endif;    

}

//copy icon address function
function spod_ccg_btn() {
    _e( '<script type="text/javascript">
            function spod_copy_ccg_add(e) {
            var copyText = document.getElementById("spodccgaddress-hidden");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            var spodtooltipadd = document.getElementById("spodccgtooltip-text");
            spodtooltipadd.innerHTML = "Address Copied!";
            }
        </script>','spod-ccg-txt'); 
    _e( '<script type="text/javascript">
            function spod_copy_ccg_amount(e) {
            var copyText = document.getElementById("spodccgamount-hidden");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            var spodtooltipamount = document.getElementById("spodccgtooltip-textamount");
            spodtooltipamount.innerHTML = "Amount Copied!";
            }
        </script>','spod-ccg-txt'); 
        

}
add_action( 'wp_footer', 'spod_ccg_btn', 99 );