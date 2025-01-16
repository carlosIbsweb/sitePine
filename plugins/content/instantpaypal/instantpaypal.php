<?php
/** 
 * @author Joomla! Extensions Store
 * @package INSTANTPAYPAL
 * @copyright (C) 2013 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Direct Access to this location is not allowed.' );

/**
 * Class plugin
 *
 * @package INSTANTPAYPAL
 * @since 1.0
 */ 
jimport ( 'joomla.plugin.plugin' );
class plgContentInstantPaypal extends JPlugin { 
	/**
	 * jQuery inclusion for the Smart Checkout buttons
	 * 
	 * @access private
	 * @var boolean
	 */
	private $jqueryInclusion;
	
	/**
	 * Avoid doubled emails for the same order
	 *
	 * @access private
	 * @var boolean
	 */
	private $emailNotificationSent;
	
	/**
	 * Variable inclusion for the Smart Checkout buttons
	 *
	 * @access private
	 * @var boolean
	 */
	private $processingTransactionInclusion;
	
	private function sendEmailNotify(&$session, &$params, &$mainframe) {
		if($this->emailNotificationSent) {
			return;
		}
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$index = $app->input->getInt('instantpaypalindex', 0);
		$articleID = $app->input->getCmd('articlenamespace', 0);
		$productQty = $app->input->getInt('instantpaypalqty', 1);
		$customPriceAmount = $app->input->getString('instantpaypalcustomamount', null);
		$productName = $session->get('instantpaypal_prodname'.$index, null, 'instantpaypal' . $articleID);
		$productPrice = $session->get('instantpaypal_prodprice'.$index, null, 'instantpaypal' . $articleID) . ' ' . $params->get('currency_code', 'USD');
		$productTax = $session->get('instantpaypal_prodtax'.$index, null, 'instantpaypal' . $articleID);
		$productShipping = $session->get('instantpaypal_prodshipping'.$index, null, 'instantpaypal' . $articleID);
		
		if($customPriceAmount) {
			$productPrice = $customPriceAmount . $params->get('currency_code', 'USD');
		}
		
		// Miniform data
		$customerName = $app->getUserStateFromRequest('instantpaypal_customername', 'instantpaypal_customername', null);
		$customerEmail = $app->getUserStateFromRequest('instantpaypal_customeremail', 'instantpaypal_customeremail', null);
		$customerNote = '<div>Note: ' . $app->getUserStateFromRequest('instantpaypal_customernote', 'instantpaypal_customernote', null) . '</div>';
		
		$mailer = JFactory::getMailer();
		// Build e-mail message format
		$mailer->setSender(array($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname')));
		$mailer->setSubject(JText::_($params->get('email_notify_subject')));
		
		$bodyHeader = JText::_($params->get('email_notify_body'));
		$message_body = <<<BODY
			<div>$bodyHeader</div>
			<hr/>
			<div>
				<span>$productName: </span><span>$productPrice</span><span> - n.$productQty</span> $productTax $productShipping
			</div>
			<hr/>
			<div>
				<span>Name: {$user->get('name', $customerName)} <br/> Email: {$user->get('email', $customerEmail)}</span>
			</div>
			$customerNote
BODY;
		
		$mailer->setBody($message_body);
		$mailer->IsHTML(true);
		
		// Add recipients
		$recipients = $params->get('email_notify_address');
		if(strpos($recipients, ',')) {
			$recipients = explode(',', $recipients);
		}
		$mailer->addRecipient($recipients);
		  
		// Send the Mail
		$rs	= $mailer->Send();
		
		$this->emailNotificationSent = true;
	}
	
	private function runPlugin($context, &$article, &$params, $page = 0) { 
		// Exclude admin exec and not authorized
		$app = JFactory::getApplication ();
		$user = JFactory::getUser();
		$doc = JFactory::getDocument();
		/* @var $doc JDocumentHtml */
		$docType = $doc->getType();
		
		if ($app->isAdmin () || $app->input->get ( 'task' ) == 'edit' || $app->input->get ( 'layout' ) == 'edit') {
			return;
		}
		
		if(!$article instanceof stdClass || $context == 'com_content.categories') {
			return;
		}
		
		$session = JFactory::getSession();
		// Is module instance execution?
		$isModulePrefix = null;
		$isArticleInstance = @(bool)$article->id;
		if(!$isArticleInstance) {
			$isModulePrefix = '000';
			$DBO = JFactory::getDBO();
			$query = "SELECT MIN(id) FROM #__content";
			$article->id = $isModulePrefix . $DBO->setQuery($query)->loadResult();
		}
		
		// Detect email notify hook
		$sendEmailNotify = $app->input->getString('instantpaypaltask', false);
		if($sendEmailNotify === 'sendemailnotify' && $this->params->get('email_notify_send', true)) {
			$this->sendEmailNotify($session, $this->params, $app);
			return;
		}
		
		$matches = array ();
		$overrides = array ();
		$btnimg = '';
		$additionalFormHtml = null;
		$uniqueShipping = $this->params->get('global_unique_shipping', 1);
		
		if(!isset($article->text)) {
			if (isset($article->introtext)){
				$article->text = $article->introtext;
			}
			else {
				$article->text = '';
			}
		}
		
		// Check document type
		if (strcmp("html", $docType) != 0) {
			$article->text = preg_replace("/{instantpaypal}(.*?){\/instantpaypal}/i", '', $article->text);
			return;
		}
		// Output JS APP nel Document
		if($app->input->get('print')) {
			$article->text = preg_replace("/{instantpaypal}(.*?){\/instantpaypal}/i", '', $article->text);
			return;
		}
		
		// Avoid processing if article view is only selected
		if($this->params->get('showonly_viewarticle', 0) && $app->input->get('view') != 'article') {
			$article->text = preg_replace("/{instantpaypal}(.*?){\/instantpaypal}/i", '', $article->text );
			return null;
		}
		
		preg_match_all ( '/{instantpaypal}(.*?){\/instantpaypal}/', $article->text, $matches, PREG_PATTERN_ORDER );
		if (count ( $matches [0] )) {
			// Customer mini form info
			$customerSessionName = $app->getUserState('instantpaypal_customername') || $user->name;
			$customerSessionEmail = $app->getUserState('instantpaypal_customeremail') || $user->email;
			
			for($i = 0; $i < count ( $matches [0] ); $i ++) {
				// Reset resources
				$formHtml = null;
				$additionalFormHtml = null;
				$mode = null;
				// Init overrides element analysis
				$overridesArray = array();
				$overrides = strlen(trim($matches [1] [$i]) )? explode ( ",", trim($matches [1] [$i] )) : array(); 
				if(count($overrides)) {
					foreach ($overrides as $overrideParam) {
						$temp = explode ( "=", $overrideParam );
						$left = $temp[0];
						array_shift($temp);
						$right = implode('', $temp);
						$overridesArray[$left] = $right;
					}
				}
				 
				// Init overrides variables with default param fallback
				$action = $originalAction = array_key_exists('action', $overridesArray) ? $overridesArray['action'] : $this->params->get('button_type', 'pay');
				$price = array_key_exists('price', $overridesArray) ? round($overridesArray['price'], 2, PHP_ROUND_HALF_UP) : round($this->params->get('default_price', 0), 2, PHP_ROUND_HALF_UP);
				$productName = array_key_exists('productname', $overridesArray) ? $overridesArray['productname'] : $this->params->get('default_productname', 'ProductDemo');
				$showQty = array_key_exists('showquantity', $overridesArray) ? $overridesArray['showquantity'] : $this->params->get('global_showquantity', false);
				$editPrice = array_key_exists('editprice', $overridesArray) ? true : false;
				
				// Tax vars
				$taxAmount = array_key_exists('taxamount', $overridesArray) ? round($overridesArray['taxamount'], 2, PHP_ROUND_HALF_UP) : round($this->params->get('global_taxamount', 0), 2, PHP_ROUND_HALF_UP);
				$taxText = array_key_exists('taxtext', $overridesArray) ? $overridesArray['taxtext'] : $this->params->get('global_taxtext', 'Tax +');
				$taxType = array_key_exists('taxtype', $overridesArray) ? $overridesArray['taxtype'] : $this->params->get('global_taxtype', 'fixed');
				if(!(float)$taxAmount) {
					$taxAmount = 0;
				}
				
				// Shipping vars
				$shippingAmount = array_key_exists('shippingamount', $overridesArray) ? round($overridesArray['shippingamount'], 2, PHP_ROUND_HALF_UP) : round($this->params->get('global_shippingamount', 0), 2, PHP_ROUND_HALF_UP);
				$shippingText = array_key_exists('shippingtext', $overridesArray) ? $overridesArray['shippingtext'] : $this->params->get('global_shippingtext', 'Shipping +');
				$shippingType = array_key_exists('shippingtype', $overridesArray) ? $overridesArray['shippingtype'] : $this->params->get('global_shippingtype', 'single');
				if(!(float)$shippingAmount) {
					$shippingAmount = 0;
				}
				
				// Returning pages
				$returningProductPage = array_key_exists('returnurl', $overridesArray) ? $overridesArray['returnurl'] : $this->params->get('return_url', false);
				
				// Target PayPal endopint
				$formActionPP = $this->params->get ( 'sandbox_mode', 0) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
				
				// Target window on floating type
				$floatingTarget = null;
				if($this->params->get ( 'open_window', '_blank' ) === '_floating') {
					$widthXfloating = $this->params->get('floating_width', 960);
					$heightXfloating = $this->params->get('floating_height', 480);
					$floatingTarget = 'onSubmit="window.open(\'\', \'_floating\', \'width=' . $widthXfloating .'px,height=' . $heightXfloating . 'px\')"';
				}
				// TYPE DONATE
				if (strtolower ( $action ) == "donate") {
					$action = '_donations';
					$btnimg = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_donate' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
				} else if (preg_match('/cart/i', $action)) { // TYPE CART 
					// Setting dell'add mode to cart
					$mode = '<input type="hidden" name="add" value="1" />';
					
				 	if (strtolower ( $action ) == "fullcart") {
						$btnimg = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_cart' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
						$btnimgview = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_viewcart' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
					} else if (strtolower ( $action ) == "addtocart") {
						$btnimg = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_cart' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
						$btnimgview = ''; 
					} else if (strtolower ( $action ) == "showcart") {
						$btnimgview = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_viewcart' .  $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
						$btnimg = ''; 
					}
					
					if (strtolower ( $action ) == "fullcart" || strtolower ( $action ) == "showcart") { 
						// view button
						$additionalFormHtml = 	'<form style="margin-top: 10px" class="subform ' . $this->params->get ( 'css_form_class', '' ) . '" name="instantpaypal" action="' . $formActionPP . '" method="post" ' . $floatingTarget . ' target="' . $this->params->get ( 'open_window', '_blank' ) . '"> 
													<input type="hidden" name="business" value="' . $this->params->get ( 'paypal_email', '' ) . '" />  
													<input type="hidden" name="cmd" value="_cart" /> 
													<input type="hidden" name="display" value="1" />
													<input type="hidden" name="lc" value="' . $this->params->get ( 'country_code', 'US' ) . '" />
				                 					<input type="hidden" name="charset" value="utf-8" />
													<input type="image" name="submit" style="border: 0;" src="' . $btnimgview . '" alt="PayPal - The safer, easier way to pay online" /> 
												</form>'; 
					} 
					// Override cmd paypal
					$action = '_cart';
				} else if (strtolower ( $action ) == "pay") { // TYPE PAY
					$action = '_xclick';
					$btnimg = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_paynow' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
				} else if (strtolower ( $action ) == "smartcheckout") { // TYPE NEW SMARTCHECKOUT
					$errorCartUrl = JUri::current();
					$base = JUri::base();
					
					// Manage auto splitting names to auto fill PayPal fields firstname/lastname
					if($user->id) {
						$customerName = $user->name;
						$customerEmail = $user->email;
					} else {
						$customerName = $app->getUserState('instantpaypal_customername') ? $app->getUserState('instantpaypal_customername') : null;
						$customerEmail = $app->getUserState('instantpaypal_customeremail') ? $app->getUserState('instantpaypal_customeremail') : null;
					}
					if($customerName) {
						$names = explode(' ', $customerName);
						$lastname = array_pop($names);
						$firstname = implode(' ', $names);
					} else {
						$lastname = '';
						$firstname = '';
					}
					$emailAddressObject = null;
					if($customerEmail) {
						$emailAddressObject = ',email_address: "' . $customerEmail . '"';
					}
					
					$restApiClientId = $this->params->get('rest_api_clientid', 'sb'); // Default to sandbox
					$smartCheckoutShowCards = $this->params->get('smartcheckout_showcards', 0) ? '' : '&disable-card=visa,mastercard,amex,discover,jcb,elo,hiper';
					$smartCheckoutShowFunding = $this->params->get('smartcheckout_showfunding', 0) ? '' : '&disable-funding=sepa,bancontact,eps,giropay,sofort,ideal,mybank';
					$buttonsColor = $this->params->get('smartcheckout_buttonscolor', 'gold');
					$buttonShape = $this->params->get('smartcheckout_buttonshape', 'rect');
					$buttonLabel = $this->params->get('smartcheckout_buttonlabel', 'checkout');
					$currencyCode = $this->params->get ( 'currency_code', 'USD' );
					$productReturnPage = $returningProductPage ? 1 : 0;
					$doc->addScript ( "https://www.paypal.com/sdk/js?client-id=$restApiClientId&currency={$currencyCode}{$smartCheckoutShowCards}{$smartCheckoutShowFunding}" );
					
					// Include the standard javascript
					if($this->params->get ( 'include_jquery', 1 ) && !$this->jqueryInclusion) {
						jimport('joomla.version');
						$version = new JVersion();
						if((int) $version->getShortVersion() >= 3) JHtml::_('jquery.framework');
						else {
							$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js');
							$doc->addScriptDeclaration('jQuery.noConflict();');
						}
						$this->jqueryInclusion = true;
					}
					
					// Include the unique processing transaction variable
					$processingText = ucfirst(JString::str_ireplace('_', ' ', JString::strtolower(JText::_('PROCESSING_TRANSACTION_PLEASE_WAIT')))) . '...';
					$completedText = ucfirst(JString::str_ireplace('_', ' ', JString::strtolower(JText::_('TRANSACTION_COMPLETED_SUCCESSFULLY'))));
					$processingTransactionConst = '<div class="instantpaypal_transaction_processing"><img style="margin: 0 10px 5px 0"src="data:image/gif;base64,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" alt="loading" /><span>' . $processingText . '</span></div>';
					if(!$this->processingTransactionInclusion) {
						$doc->addScriptDeclaration("var instantPayPalButtonsProcessingTransaction='$processingTransactionConst';");
						$this->processingTransactionInclusion = true;
					}
					
					// If the tat is rate, recalculate it here for the amount
					if($taxType == 'rate') {
						$taxAmountSmartCheckout = round(($price * $taxAmount) / 100, 2, PHP_ROUND_HALF_UP);
					} else {
						$taxAmountSmartCheckout = $taxAmount;
					}
					
					$scriptContents = <<<PPJS
						jQuery(function($){
							var currentOrderId = 0;
							var duringError = false;
							var productQuantity = 1;
							var shippingType = '$shippingType';
								
							// Ensure that the PayPal object exists, for example because of an invalid Client ID
							if(typeof(paypal) === 'undefined') {
								return;
							}
								
							// Render the PayPal button into #instantpaypal-button-container
							paypal.Buttons({
								style : {
									layout:  'vertical',
								    color:   '$buttonsColor',
								    shape:   '$buttonShape',
								    label:   '$buttonLabel'
								},
							    onClick: function(data, actions) {
			                		// Check if the user compiled the coupon code field and disable the PayPal smart checkout
									if($('#quantityfield{$i}-{$article->id}').length) {
										productQuantity = parseInt($('#quantityfield{$i}-{$article->id}').val());
									}
									
			                		// Call the server to create and store the order, retrieve the order ID
			                		var targetFieldset = $('#instantpaypal-button-container-{$i}-{$article->id}').prev('.info').get(0);
			                		var customEvent = new jQuery.Event('click', { target: targetFieldset, jQueryCustomEvent: true });
			                        var isValidForm = sendEmailIframe(customEvent, '{$article->id}', $i);
			                        
			                        if(!isValidForm) {
			                        	return actions.reject();
			                        }
							    },
				                createOrder: function(data, actions) {
				                	var shippingAmountCalculated = shippingType == 'multiple' ? $shippingAmount * productQuantity : $shippingAmount;
				                	try {
										var amountObject = {
						                                    currency_code: "{$currencyCode}",
						                                    value: parseFloat(($price * productQuantity) + ($taxAmountSmartCheckout * productQuantity) + shippingAmountCalculated).toFixed(2),
						                                    breakdown: {
						                                        item_total: {
						                                            currency_code: "{$currencyCode}",
						                                            value: parseFloat($price * productQuantity).toFixed(2),
						                                        },tax_total: {
																	currency_code: "{$currencyCode}",
																	value: parseFloat($taxAmountSmartCheckout * productQuantity).toFixed(2)
																},shipping: {
						                                            currency_code: "{$currencyCode}",
						                                            value: parseFloat(shippingAmountCalculated).toFixed(2)
					                                        	}
						                                    }
						                                };
								
						                var fullObject = {
					                        purchase_units: [
					                            {
					                                amount: amountObject,
					                          		items: [
					                          			{
					                          				"name":"{$productName}",
						                          			"quantity":productQuantity,
						                          			"unit_amount":{"currency_code":"{$currencyCode}","value":"{$price}"},
						                          			"tax":{"currency_code":"{$currencyCode}","value":"{$taxAmountSmartCheckout}"}
														}
													]
					                            }
					                        ],
					                        payer: {
					                        	name:{
					                        		given_name: '$firstname',
					                        		surname: '$lastname'
					                        	}{$emailAddressObject}
					                        }
					                    };
								
					                    return actions.order.create(fullObject);
				                	} catch (e) {
				                		console.log(e);
				                	}
				                },
				                onApprove: function(data, actions) {
									// Add the waiter user interface while the payment is saved and the user is redirected to the purchased products area
									$('#instantpaypal-button-container-{$i}-{$article->id}').before(instantPayPalButtonsProcessingTransaction);
								
				                    return actions.order.capture().then(function(details) {
										// After the update order as paid successfully and completeSubscriber steps just redirect to the purchased products folder, everything is already done
										if($productReturnPage) {
											$('div.instantpaypal_transaction_processing').text('$completedText').css({'background':'#009cde','color':'#FFF','width':'fit-content','padding':'4px 8px','margin':'2px 0','border-radius':'3px'});
											window.location.href = '$returningProductPage';
										} else {
											$('div.instantpaypal_transaction_processing').text('$completedText').css({'background':'#009cde','color':'#FFF','width':'fit-content','padding':'4px 8px','margin':'2px 0','border-radius':'3px'});
											setTimeout(function(){
												$('div.instantpaypal_transaction_processing').remove();
											}, 2000);
										}
				                    });
				                },
								onError: function (err) {
							    	// Show an error page here, when an error occurs and track the failed order status to recontact customers
							    	console.log(err);
							  	}
				            }).render('#instantpaypal-button-container-{$i}-{$article->id}');
						});
PPJS;
								
					$doc->setMetaData ('X-UA-Compatible', 'IE=edge', 'http-equiv');
					$doc->addScriptDeclaration ( $scriptContents );
				} else { // DEFAULT TYPE XCLICK
					$action = '_xclick';
					$btnimg = 'https://www.paypal.com/' . $this->params->get ( 'button_path', 'en_US' ) . '/i/btn/btn_buynow' . $this->params->get ( 'default_btnsize', '_SM' ) . '.gif';
				}

				$bitMask = ($this->params->get('showinput_name', false) & !$customerSessionName) | ($this->params->get('showinput_email', false) & !$customerSessionEmail) | $this->params->get('showinput_note', false);
				if($this->params->get('showinput_miniform', false) && $bitMask) {
					$formHtml .= '<style type="text/css">fieldset.info {
																width: 30%;
																border-top: 1px solid #CCC !important;
																padding: 2px 2px 5px 2px;
															}
														 fieldset.info legend {
																font-size: 12px;
															}
														 fieldset.info label {
																font-size: 11px;
																width: 35px;
																display: inline-block;
															}
							</style>';
					$formHtml .= '<fieldset class="info"><legend>Info</legend>';
						
					if($this->params->get('showinput_name', false) && !$user->id && !$customerSessionName) {
						$requiredName = (int)$this->params->get('showinput_name') == 3 ? 'class="required"' : '';
						$requiredNameSign = $requiredName ? '*' : '';
						$formHtml .= '<div><label style="min-width:50px">Name ' . $requiredNameSign . '</label><input type="text" ' . $requiredName . ' data-role="infominiform" data-name="Name" name="instantpaypal_customername" value=""/></div>';
					}
					if($this->params->get('showinput_email', false) && !$user->id && !$customerSessionEmail) {
						$requiredEmail = (int)$this->params->get('showinput_email') == 3 ? 'class="required"' : '';
						$requiredEmailSign = $requiredEmail ? '*' : '';
						$formHtml .= '<div><label style="min-width:50px">Email ' . $requiredEmailSign . '</label><input type="text" ' . $requiredEmail . ' data-role="infominiform" data-name="Email" name="instantpaypal_customeremail" value=""/></div>';
					}
						
					if($this->params->get('showinput_note', false)) {
						$formHtml .= '<div><label style="min-width:50px">Note</label><input type="text" data-role="infominiform" name="instantpaypal_customernote" value=""/></div>';
					}
					$formHtml .= '</fieldset>';
				}
				
				// All cases except the Smart Checkout not using forms
				if(strtolower ( $action ) != "smartcheckout") {
					$priceAmount = $editPrice ? '<input style="width:50px" type="text" id="amountfield' . $i . '" name="amount" value="' . $price . '" />' . $this->params->get('currency_code', 'USD') : '<input type="hidden" name="amount" value="' . $price . '" />';
					$formHtml .= '<form class="' . $this->params->get ( 'css_form_class', '' ) . '" name="instantpaypal" action="' . $formActionPP . '" method="post" ' . $floatingTarget . ' target="' . $this->params->get ( 'open_window', '_blank' ) . '">
								  	<input type="hidden" name="business" value="' . $this->params->get ( 'paypal_email', '' ) . '" />
								  	<input type="hidden" name="cmd" value="' . $action . '" />' .
						  			$priceAmount .
								  	'<input type="hidden" name="item_name" value="' . $productName . '" />' .
								  	$mode .
								  	'<input type="hidden" name="currency_code" value="' . $this->params->get ( 'currency_code', 'USD' ) . '" /> 
								 	<input type="hidden" name="lc" value="' . $this->params->get ( 'country_code', 'US' ) . '" />
	                				<input type="hidden" name="charset" value="utf-8" />';
					
					
					if($this->params->get('auto_url', 1)) {
						$uriInstance = JUri::getInstance();
						$formHtml .= '<input type="hidden" name="return" value="' . $uriInstance->toString() . '" />';
						$formHtml .= '<input type="hidden" name="cancel_return" value="' .  $uriInstance->toString() . '" />';
					} else {
						if ($returningProductPage) { // hint, return url
							$formHtml .= '<input type="hidden" name="return" value="' . $returningProductPage . '" />';
						}
						
						if ($cancel_url = $this->params->get('cancel_url', false)) { // hint, return url
							$formHtml .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';
						} 
					}
					
					if($taxAmount && $action != '_donations') {
						$taxFormType = $taxType == 'fixed' ? 'tax' : 'tax_rate';
						$formHtml .= '<input type="hidden" name="' . $taxFormType . '" value="' . $taxAmount . '" />';
					}
					
					if($shippingAmount && $action != '_donations') {
						switch ($shippingType) {
							case 'single':
								$formHtml .= '<input type="hidden" id="shippingfield' . $i . '" name="shipping" value="' . $shippingAmount . '" />';
								break;
									
							case 'multiple':
								$formHtml .= '<input type="hidden" id="shippingfield' . $i . '" name="shipping" value="' . $shippingAmount . '" />';
								$formHtml .= '<input type="hidden" name="shipping2" value="' . $shippingAmount . '" />';
								break;
						}
					}
					
					$formHtml .= '<input type="image" onclick="sendEmailIframe(event, \'' . $article->id . '\', ' . $i .');" name="submit" style="border: 0;" src="' . $btnimg . '" alt="PayPal - The safer, easier way to pay online" />';
					
					if($showQty && $action != '_donations') {
						$formHtml .= '<div>' . $this->params->get('quantity_text', 'Quantity:') . '<input type="text" id="quantityfield' . $i . '-' . $article->id . '" name="quantity" size="2" style="max-width:30px;" value="1"/></div>';
					}
					 
					$formHtml .= '</form>';
				} else {
					$formHtml .= '<div id="instantpaypal-button-container-' . $i . '-' . $article->id . '" style="width:100px"></div>';
					if($showQty) {
						$formHtml .= '<div>' . $this->params->get('quantity_text', 'Quantity:') . '<input type="text" id="quantityfield' . $i . '-' . $article->id . '" name="quantity" size="2" style="max-width:30px;" value="1"/></div>';
					}
				}
					 
				$additionalInfo = null;
				$currencyOrPercentage = $taxType == 'fixed' ? $this->params->get('currency_code', 'USD') : '%';
				if($this->params->get('showxtdinfo', 1)) {
					$taxAmountString =  $taxAmount ? ' | <span>' . $taxText . $taxAmount . ' ' . $currencyOrPercentage . '</span>' : '';
					$shippingAmountString =  $shippingAmount ? ' | <span>' . $shippingText . $shippingAmount . ' ' . $this->params->get('currency_code', 'USD') . '</span>' : '';
					$priceString = $editPrice ? null : '| <span>' . $price . ' ' . $this->params->get('currency_code', 'USD') . '</span>';
					$additionalInfo = '<div class="' . $this->params->get('css_infoxtd_class', null) . '"><span>' . $productName . '</span> ' . $priceString . $taxAmountString . $shippingAmountString .'</div>'; 
				}
				  
				// Final show forms logic
				$finalForms = strtolower ( $originalAction ) != "showcart" ? $formHtml . $additionalInfo . $additionalFormHtml : $additionalFormHtml;
				// Replace unique per firm instance
				$instance = $matches [1] [$i];
				$article->text = $article->introtext = str_replace("{instantpaypal}$instance{/instantpaypal}", $finalForms, $article->text );
				
				// Put info on session for email notify later
				$session->set('instantpaypal_prodname'.$i, $productName, 'instantpaypal' . $article->id);
				$session->set('instantpaypal_prodprice'.$i, $price, 'instantpaypal' . $article->id);
				if($taxAmount) {
					$session->set('instantpaypal_prodtax'.$i, ('<span>' . $taxText . $taxAmount . ' ' . $currencyOrPercentage . '</span>'), 'instantpaypal' . $article->id);
				}
				if($shippingAmount) {
					$session->set('instantpaypal_prodshipping'.$i, ('<span>' . $shippingText . $shippingAmount . ' ' . $this->params->get('currency_code', 'USD') . '</span>'), 'instantpaypal' . $article->id);
				}
			}
			
			// Queue JS code
			if(!defined('INSTPP_IFRAME_JSINCLUDED')) {
				$baseUri = JUri::base();
				$jsCode = <<<JSCODE
								<script>
								//<![CDATA[
									var sendEmailIframe = function(eventObject, articleID, productIterationID) {
										// Try to get qty
										var qty = document.getElementById('quantityfield' + productIterationID + '-' + articleID);
										var amountCustomField = document.getElementById('amountfield' + productIterationID);
										var shipping = document.getElementById('shippingfield' + productIterationID);
										var qtyAmount = '';
										var customAmount = '';
										var miniFormQueryString = '';
										var elements2Remove = new Array();
										if(qty) {
											qtyAmount = '&instantpaypalqty=' + qty.value;
										}
										if(amountCustomField) {
											customAmount = '&instantpaypalcustomamount=' + amountCustomField.value;
										}
										
										// Manage unique cart shipping
										if(window.sessionStorage && $uniqueShipping) {
											if(window.sessionStorage.getItem('cart_shipping') == 1) {
												var node = document.getElementById('shippingfield' + productIterationID);
												if(node) {
													node.parentNode.removeChild(node);
												}
											}
											if(shipping) {
												window.sessionStorage.setItem('cart_shipping', 1);
											}
										}
			
										// Supports IE8+
										if (document.querySelectorAll) {
											if(eventObject.jQueryCustomEvent) {
												var targetFieldset = eventObject.target;
											} else {
												var normalizedTarget = (eventObject.currentTarget) ? eventObject.currentTarget : eventObject.srcElement;
												var targetFieldset = normalizedTarget.parentNode.previousSibling;
											}
											if(typeof(targetFieldset) !== 'undefined' && targetFieldset.className == 'info') {
												var miniFormFields = targetFieldset.querySelectorAll('input[data-role=infominiform]');
												for(var i=0; i<miniFormFields.length; i++) {
													if(miniFormFields[i].className == 'required' && !miniFormFields[i].value) {
														alert(miniFormFields[i].getAttribute('data-name') + ' required');
														if(eventObject.preventDefault) {
															eventObject.preventDefault();
														} else {
															eventObject.returnValue = false;
														}
														return false;
													}
													// Email validation
													if(miniFormFields[i].getAttribute('data-name') == 'Email' && miniFormFields[i].value) {
														var emailRE = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
														if(!emailRE.test(miniFormFields[i].value)) {
															alert(miniFormFields[i].getAttribute('data-name') + ' no valid');
															if(eventObject.preventDefault) {
																eventObject.preventDefault();
															} else {
																eventObject.returnValue = false;
															}
															return false;
														}
													}
			
													if(miniFormFields[i].value) {
														miniFormQueryString += '&' + miniFormFields[i].name + '=' + encodeURIComponent(miniFormFields[i].value);
			
														// Try to collect all already empty siblings fields
														if(miniFormFields[i].name != 'instantpaypal_customernote') {
															var namesToRemove = document.querySelectorAll('input[name=' + miniFormFields[i].name + ']');
															if(namesToRemove.length) {
																for(var n=0; n<namesToRemove.length; n++) {
																	elements2Remove.push(namesToRemove[n]);
																}
															}
														}
													}
												}
											}
											// Try to remove all already empty siblings fields
											if(elements2Remove.length) {
												for(var k=0; k<elements2Remove.length; k++) {
													var element2Remove = elements2Remove[k];
													element2Remove.parentNode.removeChild(element2Remove.previousSibling);
													element2Remove.parentNode.removeChild(element2Remove);
												}
											}
										}
			
										// Try to get info miniform fields
									 	iframe = document.createElement("IFRAME");
									    iframe.setAttribute("src","{$baseUri}index.php?option=com_content&view=article&id=" + articleID + "&articlenamespace=" + articleID + "&instantpaypaltask=sendemailnotify&instantpaypalindex=" + productIterationID + qtyAmount + customAmount + miniFormQueryString);
										iframe.setAttribute("width","0");
										iframe.setAttribute("height","0");
									   	document.getElementsByTagName("body")[0].appendChild(iframe);
									    		
									    return true;
									}
								//]]>
								</script>
JSCODE;
			$article->text .= $jsCode;
			$article->introtext = $article->text;
			define('INSTPP_IFRAME_JSINCLUDED', 1);
			}
		}
		return null;
	}
	
	/**
	 * onContentPrepare handler
	 *
	 * @access	public
	 * @return null
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		$app = JFactory::getApplication();
		if(!$app->isAdmin() && $this->params->get('includeevent', 'onContentAfterDisplay') == 'onContentPrepare') {
			$this->runPlugin($context, $article, $params, $page = 0);
		}
	}
	
	/**
	 * onContentAfterDisplay handler
	 *
	 * @access	public
	 * @return null
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0) {
		$app = JFactory::getApplication();
		if(!$app->isAdmin() && $this->params->get('includeevent', 'onContentAfterDisplay') == 'onContentAfterDisplay') {
			$this->runPlugin($context, $article, $params, $page = 0);
		}
	}
	
	/**
	 * Override registers Listeners to the Dispatcher
	 * It allows to stop a plugin execution based on the registered listeners
	 *
	 * @override
	 * @return  void
	 */
	public function registerListeners() {
		// Ensure compatibility excluding Joomla 4
		if(version_compare(JVERSION, '4', '>=')) {
			JFactory::getApplication()->enqueueMessage ("The plugin package that is installed doesn't match your actual Joomla version and is not fully compatible. The package for Joomla 3.x is currently installed but you are running Joomla 4.x, if you have just upgraded your Joomla website from the version 3.x to the version 4.x the plugin must also be upgraded accordingly.<br/>To upgrade the plugin, visit our store at <a target='_blank' href='https://storejextensions.org'>https://storejextensions.org</a>, download the package for Joomla 4.x  and install it over the current one", 'error');
			return;
		} elseif (method_exists(get_parent_class($this), 'registerListeners')) {
			parent::registerListeners();
		}
	}
	
	/**
	 * Plugin constructor
	 *
	 * @access public
	 */
	public function __construct(&$subject, $config = array()) {
		// Ensure compatibility excluding Joomla 4
		if(version_compare(JVERSION, '4', '>=')) {
			return false;
		}
		
		parent::__construct ( $subject, $config );
		
		$this->jqueryInclusion = false;
		$this->processingTransactionInclusion = false;
	}
}