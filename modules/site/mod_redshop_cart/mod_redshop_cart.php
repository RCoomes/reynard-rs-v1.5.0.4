<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_cart
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$option = JRequest::getCmd('option');
JLoader::import('redshop.library');

$show_with_vat = trim($params->get('show_with_vat', 0));
$button_text = trim($params->get('button_text', ''));
$show_shipping_line = ($params->get('show_shipping_line', 0));

$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_redshop_cart/css/cart.css");

if ($option != 'com_redshop')
{
	require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
	JLoader::load('RedshopHelperAdminConfiguration');
	$Redconfiguration = new Redconfiguration;
	$Redconfiguration->defineDynamicVars();
}

JLoader::load('RedshopHelperProduct');
JLoader::load('RedshopHelperHelper');
JLoader::load('RedshopHelperCart');

$show_empty_btn = 0;

if ($params->get("checkout_empty") != 0)
{
	$show_empty_btn = 1;
}

// Helper object
$helper = new redhelper;
$helper->dbtocart();

$output_view = $params->get('cart_output', 'simple');
$session = JFactory::getSession();
$cart = $session->get('cart');

if (count($cart) <= 0 || $cart == "")
{
	$cart = array();
}

$idx = 0;

if ((!array_key_exists("idx", $cart) || (array_key_exists("idx", $cart) && $cart['idx'] < 1)) && $params->get("use_cookies_value") == 1)
{
	if (isset($_COOKIE['redSHOPcart']) && ($_COOKIE['redSHOPcart'] != ''))
	{
		$session->set('cart', unserialize(stripslashes($_COOKIE['redSHOPcart'])));
		$cart = $session->get('cart');
	}
}

if (!array_key_exists("quotation_id", $cart))
{
	if (isset($cart['idx']))
	{
		$idx = $cart['idx'];
	}
}

$product_quntity = 0;

for ($i = 0; $i < $idx; $i++)
{
	$product_quntity += $cart[$i]['quantity'];
}

$count = $product_quntity;
$total = 0;
$shipping = 0;

if ($idx)
{
	$total    = $cart['mod_cart_total'];
	$shipping = $cart['shipping'];

	if ($show_with_vat == 0)
	{
		if (isset($cart['shipping_vat']) === false)
		{
			$cart['shipping_vat'] = 0;
		}

		$shippingVat = $cart['shipping_vat'];
		$shipping    = $shipping - $shippingVat;
	}
}

$session->set('cart', $cart);
require JModuleHelper::getLayoutPath('mod_redshop_cart');
