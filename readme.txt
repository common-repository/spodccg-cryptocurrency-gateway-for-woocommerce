=== Spodccg - CryptoCurrency Gateway For Woocommerce ===
Contributors: stixen84
Tags: Cryptocurrency, Woocommerce
Donate link: https://www.paypal.com/donate?business=JBLNTNZBHX9NN&no_recurring=1&currency_code=AUD 
Requires at least: 5.8.0
Tested up to: 6.6.2
Stable tag: 1.2.3
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple Cryptocurrency payment gateway for Woocommerce.

== Description ==

**PLEASE NOTE: THIS PLUGIN ONLY WORKS WITH THE CLASSIC WOOCOMMERCE CHECKOUT**

Spodccg - CryptoCurrency Gateway For Woocommerce enables sellers to accept cryptocurrency as payment for their products on woocommerce stores. 

Within the settings you can choose ANY cryptocurrency by using its ticker (symbol) to output the current exchange rate.

It uses real time exchange rates provided by coinmarketcap.com api. Sellers enter in their receiving address and a QR code image (optional) and then it's up to the buyer to process the transaction using their wallets. 

Once they have sent the crypto they enter in a transaction ID (optional) and place the order. They will get sent the processing order email as standard by Woocommerce. The seller will need to then confirm the transaction on their end and complete the order.

== Installation ==

From your WordPress dashboard

1. Plugins > Add New
2. Search for "Spodccg" and Install
3. Activate Spodccg from your Plugins page
4. Get your free API key from: <a href="https://pro.coinmarketcap.com/signup/" target="_blank">https://pro.coinmarketcap.com/signup/</a>
5. Go to Woocommerce > Settings > Payments and click on manage next to CryptoCurrency Gateway
6. Enable the plugin and add your API key.
7. Find the ticker symbol to the cryptocurrency of your choice by going to <a href="https://coinmarketcap.com/all/views/all/" target="_blank">https://coinmarketcap.com/all/views/all/</a> 
<br>It will be in the Symbol column and is usually a 3 to 6 letter representation of the cryptocurrency.
8. Add your RECEIVING ADDRESS.
9. If your wallet provides a QR Code Image, upload this to your Wordpress media library and copy paste the URL to it in the field provided.
10. Enable the transaction id field if you want the customer to add the txid in. (optional)
11. Following on with the transaction id, you should find the relevant blockchain explorer link and add this into the settings aswell. (optional)
12. Add in any other details you want for the checkout page.
13. Click on 'Save Changes' and you're DONE!

Important: if you are using my other plugin specifically for Cardano (Spoddano), make sure to sign up for a different api key
if possible to avoid reaching your daily limit of api calls.

== Frequently Asked Questions ==

= Can I use any cryptocurrency? =

Yes! aslong as the symbol is on coinmarketcap.com the API will find the price quote and output accordingly.

= Where do i get the API key from? =

coinmarketcap.com offers a free basic API key to call the current exchange rate of the currency used in your store. 
<br>Get yours here: <a href="https://pro.coinmarketcap.com/signup/" target="_blank">https://pro.coinmarketcap.com/signup/</a>

= Where do i get the ticker symbol from? =

Only go to this list to find the symbol and enter it into the field. <br><a href="https://coinmarketcap.com/all/views/all/" target="_blank">https://coinmarketcap.com/all/views/all/</a>
<br>It will be in the Symbol column and is usually a 3 to 6 letter representation of the cryptocurrency.

= How do I find the blockchain explorer? =

You can find this by doing a google search for your chosen crypto and 'Blockchain Explorer' eg: "Bitcoin Blockchain explorer".
<br>This is only needed if the Transaction ID field is enabled.

= Where does one enter the transaction ID to confirm the transaction? =

After you get sent the transaction ID you can follow this up on the Blockchain Explorer by clicking on the link provided in the orders dashboard or admin email.

If there are multiple transactions for the specified txID, do a search for your address within the list of "To" addresses.

= Support =
If you find an issue or bug, please email me at steven@spiraloutdesigns.com

== Screenshots ==

1. Payment Settings
2. Checkout page settings
3. The checkout panel
4. Email invoice with details


== Changelog ==

= 1.2.3 =
* Switch Sessions to use Transients instead, better performance, less api calls

= 1.2.2 =
* Fix issue with low priced products not calculating correctly

= 1.2.1 =
* Fix session not clearing when updating admin settings
* Fix issue with very very low value crypto price not being output correctly

= 1.2.0 =
* Rework API retrieve function to only make the call once
    -Increase performance of checkout page
    -Less credits required for the coinmarketcap API

= 1.0.1 =
* Fix address breaking out of container

= 1.0.0 =
* First Stable version released