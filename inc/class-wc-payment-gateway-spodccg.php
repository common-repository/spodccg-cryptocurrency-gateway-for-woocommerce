<?php
/**
 * Class WC_Gateway_spodccg file.
 *
 * @package WooCommerce\Gateways
 */
//Security - exit file if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * CryptoCurrency Gateway For Woocommerce.
 *
 * Provides a Payment Gateway using the Cryptocurrency.
 *
 * @class       WC_Gateway_spodccg
 * @extends     WC_Payment_Gateway
 * @version     1.0.0
 * @package     WooCommerce\Classes\Payment
 */



class WC_Gateway_spodccg extends WC_Payment_Gateway {
/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		// Setup general properties.
		$this->setup_properties();

		// Get settings.
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->disclaimer         = $this->get_option( 'disclaimer' );
		$this->address            = $this->get_option( 'address' );
		$this->qrcode             = $this->get_option( 'qrcode' );
		$this->apikey             = $this->get_option( 'apikey' );
		$this->txidenable         = $this->get_option( 'txidenable' );
		$this->symbol             = $this->get_option( 'symbol' );
		$this->blockchain         = $this->get_option( 'blockchain' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 3 );

		// Customer Emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );

	}

	/**
	 * Setup general properties for the gateway.
	 */
	protected function setup_properties() {
		$this->id                 = 'spodccg';
		$this->icon               = apply_filters( 'woocommerce_spodccg_icon', plugins_url('../assets/icon.png', __FILE__) );
		$this->method_title       = __( 'CryptoCurrency Gateway', 'woocommerce' );
		$this->method_description = __( 'Have your customers pay with Cryptocurrency.', 'woocommerce' );
		$this->has_fields         = false;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'            => array(
				'title'       => __( 'Enable/Disable', 'spod-ccg-txt' ),
				'label'       => __( 'Enable CryptoCurrency Gateway', 'spod-ccg-txt' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),

			'firstitle'       => array(
				'title'       => __( 'CryptoCurrency Payment Settings', 'spod-ccg-txt' ),
				'css'		    => 'display:none;',
			),

			'apikey' => array(
				'title' 	  => __('API Key', 'spod-ccg-txt'), 
				'type' 	      => 'text',
				'css'		  => 'width:100%;',
				'description' => __('This is your api key from coinmarketcap.com.<br>Sign up for the free basic one here: <a href="https://pro.coinmarketcap.com/signup/" target="_blank">https://pro.coinmarketcap.com/signup/</a>', 'spod-ccg-txt'),
				'default'     => __('','spod-ccg-txt'),
				//'desc_tip'    => true,
			),
			'symbol' => array(
				'title' 	  => __('Ticker', 'spod-ccg-txt'), 
				'type' 	      => 'text',
				'css'		  => 'width:100px;',
				'description' => __('This is the symbol of Cryptocurrency you want to use as your checkout method. <br> Only go to this list to find the symbol and enter it into the field. <a href="https://coinmarketcap.com/all/views/all/" target="_blank">https://coinmarketcap.com/all/views/all/</a><br>It will be in the Symbol column and is usually a 3 to 6 letter representation of the cryptocurrency.', 'spod-ccg-txt'),
				'default'     => __('BTC','spod-ccg-txt'),
				//'desc_tip'    => true,
			),
			'address' => array(
				'title' 	  => __('Receiving Address', 'spod-ccg-txt'), 
				'type' 	      => 'text',
				'css'		  => 'width:100%;',
				'description' => __('This is your address the CryptoCurrency will be paid into. TRIPLE CHECK THIS IS CORRECT.', 'spod-ccg-txt'),
				'default'     => __('Enter Address','spod-ccg-txt'),
				//'desc_tip'    => true,
			),
			'qrcode' => array(
				'title' 		=> __('Upload receiving address QR Code Image', 'spod-ccg-txt'),
				'type'  		=> 'text',
				'css'		    => 'width:100%;',
				'description'   => __('This is the QR code image associated with your receiving address. This will make processing transactions easier for customers.'),
				//'default'       => __(''),
				'desc_tip'      => true,
			),
			'txidenable'            => array(
				'title'       => __( 'Enable/Disable Transaction ID Field', 'spod-ccg-txt' ),
				'label'       => __( 'Enable Transaction ID Field', 'spod-ccg-txt' ),
				'type'        => 'checkbox',
				'description' => __('Disable the need for your customer to enter in a transaction ID', 'spod-ccg-txt'),
				'default'     => 'no',
				//'desc_tip'      => true,
			),
			'blockchain' => array(
				'title' 	  => __('Blockchain Link', 'spod-ccg-txt'), 
				'type' 	      => 'text',
				'description' => __('This is the link to the Cryptocurrencies blockchain explorer to make it easier to find the transaction. <br>You can find this by doing a google search of your chosen crypto and "Blockchain Exlporer" eg: "Bitcoin Blockhain explorer". <br><strong>This is only needed if the Transaction ID field is enabled.</strong>', 'spod-ccg-txt'),
				//'default'       => __(''),
				//'desc_tip'    => true,
			),	
			'secondtitle'     => array(
				'title'       => __( 'Checkout Page Details', 'spod-ccg-txt' ),
				'css'		    => 'display:none;',
			),
			'title'              => array(
				'title'       => __( 'Title', 'spod-ccg-txt' ),
				'type'        => 'text',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'spod-ccg-txt' ),
				'default'     => __( 'Pay with Bitcoin (BTC)', 'spod-ccg-txt' ),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __( 'Description', 'spod-ccg-txt' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your website.', 'spod-ccg-txt' ),
				'default'     => __( 'example: Pay with Bitcoin (BTC).', 'spod-ccg-txt' ),
				'desc_tip'    => true,
			),
			'disclaimer'       => array(
				'title'       => __( 'Disclaimer', 'spod-ccg-txt' ),
				'type'        => 'textarea',
				'description' => __( 'A disclaimer that will be added to checkout panel', 'spod-ccg-txt' ),
				'default'     => __( 'Please only send the required cryptocurrency to this address. Sending any other digital asset will result in permanent loss. Please make sure you have correctly scanned the QR Code above and/or verified the address is correct in your wallet. We can not be held responsible for incorrectly processed transactions.', 'spod-ccg-txt' ),
				'desc_tip'    => true,
			),
		);
	}


	/**
	 * Checks to see whether or not the admin settings are being accessed by the current request.
	 *
	 * @return bool
	 */
	private function is_accessing_settings() {
		if ( is_admin() ) {
			// phpcs:disable WordPress.Security.NonceVerification
			if ( ! isset( $_REQUEST['page'] ) || 'wc-settings' !== $_REQUEST['page'] ) {
				return false;
			}
			if ( ! isset( $_REQUEST['tab'] ) || 'checkout' !== $_REQUEST['tab'] ) {
				return false;
			}
			if ( ! isset( $_REQUEST['section'] ) || 'spodccg' !== $_REQUEST['section'] ) {
				return false;
			}
			// phpcs:enable WordPress.Security.NonceVerification

			return true;
		}

		return false;
	}



	/**
	 * Indicates whether a rate exists in an array of canonically-formatted rate IDs that activates this gateway.
	 *
	 * @since  3.4.0
	 *
	 * @param array $rate_ids Rate ids to check.
	 * @return boolean
	 */
	private function get_matching_rates( $rate_ids ) {
		// First, match entries in 'method_id:instance_id' format. Then, match entries in 'method_id' format by stripping off the instance ID from the candidates.
		return array_unique( array_merge( array_intersect( $this->enable_for_methods, $rate_ids ), array_intersect( $this->enable_for_methods, array_unique( array_map( 'wc_get_string_before_colon', $rate_ids ) ) ) ) );
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
			// Mark as processing or on-hold (payment won't be taken until delivery).
			$order->update_status( apply_filters( 'woocommerce_spodccg_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order ), __( 'Payment to be confirmed.', 'woocommerce' ) );
		} else {
			$order->payment_complete();
		}

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Change payment complete order status to completed for spodccg orders.
	 *
	 * @since  3.1.0
	 * @param  string         $status Current order status.
	 * @param  int            $order_id Order ID.
	 * @param  WC_Order|false $order Order object.
	 * @return string
	 */
	public function change_payment_complete_order_status( $status, $order_id = 0, $order = false ) {
		if ( $order && 'spodccg' === $order->get_payment_method() ) {
			$status = 'completed';
		}
		return $status;
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin  Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
		}
	}

  }
 

  