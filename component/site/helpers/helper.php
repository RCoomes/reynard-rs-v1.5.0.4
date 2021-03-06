<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JLoader::load('RedshopHelperProduct');
JLoader::load('RedshopHelperUser');

class redhelper
{
	public $_table_prefix = null;

	public $_db = null;

	public $_isredCRM = null;

	protected static $redshopMenuItems;

	protected static $isRedProductFinder = null;

	public function __construct()
	{
		$this->_table_prefix = '#__redshop_';
		$this->_db           = JFactory::getDbo();
	}

	/**
	 * Quote an array of values.
	 *
	 * @param   array   $values     The values.
	 * @param   string  $nameQuote  Name quote, can be possible q, quote, qn, quoteName
	 *
	 * @return  array  The quoted values
	 */
	public static function quote(array $values, $nameQuote = 'q')
	{
		$db = JFactory::getDbo();

		return array_map(
			function ($value) use ($db, $nameQuote) {
				return $db->$nameQuote($value);
			},
			$values
		);
	}

	/**
	 * Set Operand For Values
	 *
	 * @param   float   $leftValue   Left value
	 * @param   string  $operand     Operand
	 * @param   float   $rightValue  Right value
	 *
	 * @return float
	 */
	public static function setOperandForValues($leftValue, $operand, $rightValue)
	{
		switch ($operand)
		{
			case '+':
				$leftValue += $rightValue;
				break;
			case '-':
				$leftValue -= $rightValue;
				break;
			case '*':
				$leftValue *= $rightValue;
				break;
			case '/':
				$leftValue /= $rightValue;
				break;
		}

		return $leftValue;
	}

	/**
	 * Get Redshop Menu Items
	 *
	 * @return array
	 */
	public function getRedshopMenuItems()
	{
		if (!is_array(self::$redshopMenuItems))
		{
			self::$redshopMenuItems = JFactory::getApplication()->getMenu()->getItems('component', 'com_redshop');
		}

		return self::$redshopMenuItems;
	}

	/**
	 * add item to cart from db ...
	 *
	 * @return  void
	 */
	public function dbtocart()
	{
		JLoader::load('RedshopHelperCart');
		$session = JFactory::getSession();
		$cart    = $session->get('cart');
		$user    = JFactory::getUser();

		if ($user->id && !isset($cart['idx']))
		{
			$rscarthelper = new rsCarthelper;
			$rscarthelper->dbtocart();
		}
	}

	/**
	 * Delete shipping rate when shipping method is not available
	 *
	 * @return  void
	 */
	public function removeShippingRate()
	{
		$db = JFactory::getDbo();

		$query = "SELECT DISTINCT(shipping_class)  FROM " . $this->_table_prefix . "shipping_rate ";
		$this->_db->setQuery($query);
		$data = $this->_db->loadColumn();

		if (count($data) > 0)
		{
			$query_plg = "SELECT element FROM #__extensions WHERE folder='redshop_shipping'";
			$this->_db->setQuery($query_plg);
			$plg_ship_elm = $this->_db->loadColumn();

			$diff_ship = array_diff($data, $plg_ship_elm);
			sort($diff_ship);

			for ($i = 0; $i < count($diff_ship); $i++)
			{
				$query = "DELETE  FROM " . $this->_table_prefix . "shipping_rate WHERE shipping_class = " . $db->quote($diff_ship[$i]);
				$this->_db->setQuery($query);
				$this->_db->execute();
			}
		}
	}

	public function getPlugins($folder = 'redshop')
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__extensions "
			. "WHERE  enabled = '1' "
			. "AND LOWER(`folder`) = " . $db->quote(strtolower($folder)) . " "
			. "ORDER BY ordering ASC ";
		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	public function getallPlugins($folder = 'redshop')
	{
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__extensions "
			. "WHERE LOWER(`folder`) = " . $db->quote(strtolower($folder)) . " "
			. "ORDER BY ordering ASC ";
		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	public function orderPaymentNotYetUpdated($dbConn, $order_id, $tid)
	{
		$db = JFactory::getDbo();

		$res   = false;
		$query = "SELECT COUNT(*) `qty` FROM `" . $this->_table_prefix . "order_payment` "
			. "WHERE `order_id` = " . (int) $db->getEscaped($order_id) . " "
			. "AND order_payment_trans_id = " . $db->quote($tid);
		$db->setQuery($query);
		$order_payment = $db->loadResult();

		if ($order_payment == 0)
		{
			$res = true;
		}

		return $res;
	}

	/**
	 * Check Menu Query
	 *
	 * @param   object  $oneMenuItem  Values current menu item
	 * @param   array   $queryItems   Name query check
	 *
	 * @return bool
	 */
	public function checkMenuQuery($oneMenuItem, $queryItems)
	{
		foreach ($queryItems as $key => $value)
		{
			if (!isset($oneMenuItem->query[$key])
				|| (is_array($value) && !in_array($oneMenuItem->query[$key], $value))
				|| $oneMenuItem->query[$key] != $value)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get Item Id
	 *
	 * @param   int  $productId   Product Id
	 * @param   int  $categoryId  Category Id
	 *
	 * @return int|mixed
	 */
	public function getItemid($productId = 0, $categoryId = 0)
	{
		if ($categoryId)
		{
			foreach ($this->getRedshopMenuItems() as $oneMenuItem)
			{
				if ($this->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'category', 'cid' => $categoryId)))
				{
					return $oneMenuItem->id;
				}
			}
		}

		if ($productId)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('category_id')
				->from($db->qn('#__redshop_product_category_xref', 'cx'))
				->where('product_id = ' . (int) $productId);
			$db->setQuery($query);

			if ($categories = $db->loadColumn())
			{
				foreach ($this->getRedshopMenuItems() as $oneMenuItem)
				{
					if ($this->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'category', 'cid' => $categories)))
					{
						return $oneMenuItem->id;
					}
				}
			}
		}

		$input = JFactory::getApplication()->input;
		$option = $input->getCmd('option', '');

		if ($option != 'com_redshop')
		{
			foreach ($this->getRedshopMenuItems() as $oneMenuItem)
			{
				if ($this->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'category')))
				{
					return $oneMenuItem->id;
				}
			}

			foreach ($this->getRedshopMenuItems() as $oneMenuItem)
			{
				if ($this->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop')))
				{
					return $oneMenuItem->id;
				}
			}
		}

		return $input->getInt('Itemid', 0);
	}

	/**
	 * Get Category Itemid
	 *
	 * @param   int  $categoryId  Category id
	 *
	 * @return mixed
	 */
	public function getCategoryItemid($categoryId = 0)
	{
		if ($categoryId)
		{
			foreach (self::getRedshopMenuItems() as $oneMenuItem)
			{
				if (self::checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'category', 'layout' => 'detail', 'cid' => (int) $categoryId)))
				{
					return $oneMenuItem->id;
				}
			}
		}
		else
		{
			foreach (self::getRedshopMenuItems() as $oneMenuItem)
			{
				if (self::checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'category')))
				{
					return $oneMenuItem->id;
				}
			}
		}

		return null;
	}

	public function convertLanguageString($arr)
	{
		for ($i = 0; $i < count($arr); $i++)
		{
			$txt   = $arr[$i]->text;
			$ltext = JText::_($txt);

			if ($ltext != $txt)
			{
				$arr[$i]->text = $ltext;
			}
			elseif ($arr[$i]->country_jtext != "")
			{
				$arr[$i]->text = $arr[$i]->country_jtext;
			}
		}

		$tmpArray = array();

		for ($i = 0; $i < count($arr); $i++)
		{
			$txt            = $arr[$i]->text;
			$val            = $arr[$i]->value;
			$tmpArray[$val] = $txt;
		}

		asort($tmpArray);
		$x = 0;

		foreach ($tmpArray AS $val => $txt)
		{
			$arr[$x]->text  = $txt;
			$arr[$x]->value = $val;
			$x++;
		}

		return $arr;
	}

	/**
	 * shopper Group portal info
	 *
	 * @return  object  Shopper Group Ids Object
	 */
	public function getShopperGroupPortal()
	{
		$userHelper = new rsUserhelper;
		$user = JFactory::getUser();
		$shopperGroupId = $userHelper->getShopperGroup($user->id);

		if ($result = $userHelper->getShopperGroupList($shopperGroupId))
		{
			return $result[0];
		}

		return false;
	}

	/**
	 * shopper Group category ACL
	 *
	 * @param   int  $cid  Category id
	 *
	 * @return  null|object
	 */
	public function getShopperGroupCategory($cid = 0)
	{
		$user = JFactory::getUser();
		$userHelper = new rsUserhelper;
		$shopperGroupId = $userHelper->getShopperGroup($user->id);

		if ($shopperGroupData = $userHelper->getShopperGroupList($shopperGroupId))
		{
			if (isset($shopperGroupData[0]) && $shopperGroupData[0]->shopper_group_categories)
			{
				$categories = explode(',', $shopperGroupData[0]->shopper_group_categories);

				if (array_search((int) $cid, $categories) !== false)
				{
					return $shopperGroupData[0];
				}
			}
		}

		return null;
	}

	public function getShopperGroupProductCategory($pid = 0)
	{
		$user = JFactory::getUser();

		$query = "SELECT p.product_id,cx.category_id FROM `" . $this->_table_prefix . "product` AS p "
			. "LEFT JOIN " . $this->_table_prefix . "product_category_xref AS cx ON p.product_id=cx.product_id "
			. "WHERE p.product_id=" . (int) $pid ;
		$this->_db->setQuery($query);
		$prodctcat = $this->_db->loadObjectList();
		$catflag   = false;

		for ($i = 0; $i < count($prodctcat); $i++)
		{
			$cid            = $prodctcat[$i]->category_id;
			$shoppercatdata = $this->getShopperGroupCategory($cid);

			if (count($shoppercatdata) <= 0 && $catflag == false)
			{
				$catflag = true;
			}
		}

		return $catflag;
	}

	// 	Order by list
	public function getOrderByList()
	{
		$order_data           = array();
		$order_data[0] = new stdClass;
		$order_data[0]->value = "p.product_name ASC";
		$order_data[0]->text  = JText::_('COM_REDSHOP_PRODUCT_NAME');

		$order_data[1] = new stdClass;
		$order_data[1]->value = "p.product_price ASC";
		$order_data[1]->text  = JText::_('COM_REDSHOP_PRODUCT_PRICE_ASC');

		$order_data[2] = new stdClass;
		$order_data[2]->value = "p.product_price DESC";
		$order_data[2]->text  = JText::_('COM_REDSHOP_PRODUCT_PRICE_DESC');

		$order_data[3] = new stdClass;
		$order_data[3]->value = "p.product_number ASC";
		$order_data[3]->text  = JText::_('COM_REDSHOP_PRODUCT_NUMBER');

		$order_data[4] = new stdClass;
		$order_data[4]->value = "p.product_id DESC";
		$order_data[4]->text  = JText::_('COM_REDSHOP_NEWEST');

		$order_data[5] = new stdClass;
		$order_data[5]->value = "pc.ordering ASC";
		$order_data[5]->text  = JText::_('COM_REDSHOP_ORDERING');

		return $order_data;
	}

	public function getManufacturerOrderByList()
	{
		$order_data           = array();
		$order_data[0] = new stdClass;
		$order_data[0]->value = "mn.manufacturer_name ASC";
		$order_data[0]->text  = JText::_('COM_REDSHOP_ALPHABETICALLY');

		$order_data[1] = new stdClass;
		$order_data[1]->value = "mn.manufacturer_id DESC";
		$order_data[1]->text  = JText::_('COM_REDSHOP_NEWEST');

		$order_data[2] = new stdClass;
		$order_data[2]->value = "mn.ordering ASC";
		$order_data[2]->text  = JText::_('COM_REDSHOP_ORDERING');

		return $order_data;
	}

	public function getRelatedOrderByList()
	{
		$order_data           = array();
		$order_data[0] = new stdClass;
		$order_data[0]->value = "p.product_name ASC";
		$order_data[0]->text  = JText::_('COM_REDSHOP_PRODUCT_NAME_ASC');

		$order_data[1] = new stdClass;
		$order_data[1]->value = "p.product_name DESC";
		$order_data[1]->text  = JText::_('COM_REDSHOP_PRODUCT_NAME_DESC');

		$order_data[2] = new stdClass;
		$order_data[2]->value = "p.product_price ASC";
		$order_data[2]->text  = JText::_('COM_REDSHOP_PRODUCT_PRICE_ASC');

		$order_data[3] = new stdClass;
		$order_data[3]->value = "p.product_price DESC";
		$order_data[3]->text  = JText::_('COM_REDSHOP_PRODUCT_PRICE_DESC');

		$order_data[4] = new stdClass;
		$order_data[4]->value = "p.product_number ASC";
		$order_data[4]->text  = JText::_('COM_REDSHOP_PRODUCT_NUMBER_ASC');

		$order_data[5] = new stdClass;
		$order_data[5]->value = "p.product_number DESC";
		$order_data[5]->text  = JText::_('COM_REDSHOP_PRODUCT_NUMBER_DESC');

		$order_data[6] = new stdClass;
		$order_data[6]->value = "r.ordering ASC";
		$order_data[6]->text  = JText::_('COM_REDSHOP_ORDERING_ASC');

		$order_data[7] = new stdClass;
		$order_data[7]->value = "r.ordering DESC";
		$order_data[7]->text  = JText::_('COM_REDSHOP_ORDERING_DESC');

		$order_data[8] = new stdClass;
		$order_data[8]->value = "e.data_txt ASC";
		$order_data[8]->text  = JText::_('COM_REDSHOP_DATEPICKER_ASC');

		$order_data[9] = new stdClass;
		$order_data[9]->value = "e.data_txt DESC";
		$order_data[9]->text  = JText::_('COM_REDSHOP_DATEPICKER_DESC');

		return $order_data;
	}

	public function getAccessoryOrderByList()
	{
		$order_data           = array();
		$order_data[0] = new stdClass;
		$order_data[0]->value = "child_product_id ASC";
		$order_data[0]->text  = JText::_('COM_REDSHOP_PRODUCT_ID_ASC');

		$order_data[1] = new stdClass;
		$order_data[1]->value = "child_product_id DESC";
		$order_data[1]->text  = JText::_('COM_REDSHOP_PRODUCT_ID_DESC');

		$order_data[2] = new stdClass;
		$order_data[2]->value = "accessory_id ASC";
		$order_data[2]->text  = JText::_('COM_REDSHOP_ACCESSORY_ID_ASC');

		$order_data[3] = new stdClass;
		$order_data[3]->value = "accessory_id DESC";
		$order_data[3]->text  = JText::_('COM_REDSHOP_ACCESSORY_ID_DESC');

		$order_data[4] = new stdClass;
		$order_data[4]->value = "newaccessory_price ASC";
		$order_data[4]->text  = JText::_('COM_REDSHOP_ACCESSORY_PRICE_ASC');

		$order_data[5] = new stdClass;
		$order_data[5]->value = "newaccessory_price DESC";
		$order_data[5]->text  = JText::_('COM_REDSHOP_ACCESSORY_PRICE_DESC');

		$order_data[6] = new stdClass;
		$order_data[6]->value = "ordering ASC";
		$order_data[6]->text  = JText::_('COM_REDSHOP_ORDERING_ASC');

		$order_data[7] = new stdClass;
		$order_data[7]->value = "ordering DESC";
		$order_data[7]->text  = JText::_('COM_REDSHOP_ORDERING_DESC');

		return $order_data;
	}

	//  function to get preorder option list
	public function getPreOrderByList()
	{
		$preorder_data = array();
		$preorder_data[0] = new stdClass;
		$preorder_data[0]->value = "global";
		$preorder_data[0]->text  = JText::_('COM_REDSHOP_GLOBAL');

		$preorder_data[1] = new stdClass;
		$preorder_data[1]->value = "yes";
		$preorder_data[1]->text  = JText::_('COM_REDSHOP_YES');

		$preorder_data[2] = new stdClass;
		$preorder_data[2]->value = "no";
		$preorder_data[2]->text  = JText::_('COM_REDSHOP_NO');

		return $preorder_data;
	}

	//  function to get child product option list
	public function getChildProductOption()
	{
		$childproduct_data = array();
		$childproduct_data[0] = new stdClass;
		$childproduct_data[0]->value = "product_name";
		$childproduct_data[0]->text  = JText::_('COM_REDSHOP_CHILD_PRODUCT_NAME');

		$childproduct_data[1] = new stdClass;
		$childproduct_data[1]->value = "product_number";
		$childproduct_data[1]->text  = JText::_('COM_REDSHOP_CHILD_PRODUCT_NUMBER');

		return $childproduct_data;
	}

	//  function to get state abbrivation option list
	public function getStateAbbrivationByList()
	{
		$state_data           = array();
		$state_data[0] = new stdClass;
		$state_data[0]->value = "2";
		$state_data[0]->text  = JText::_('COM_REDSHOP_TWO_LETTER_ABBRIVATION');

		$state_data[1] = new stdClass;
		$state_data[1]->value = "3";
		$state_data[1]->text  = JText::_('COM_REDSHOP_THREE_LETTER_ABBRIVATION');

		return $state_data;
	}

	// Get checkout Itemid
	public function getCheckoutItemid()
	{
		$userhelper         = new rsUserhelper;
		$Itemid             = DEFAULT_CART_CHECKOUT_ITEMID;
		$shopper_group_data = $userhelper->getShoppergroupData();

		if (count($shopper_group_data) > 0 && $shopper_group_data->shopper_group_cart_checkout_itemid != 0)
		{
			$Itemid = $shopper_group_data->shopper_group_cart_checkout_itemid;
		}

		if ($Itemid == 0)
		{
			$Itemid = JRequest::getInt('Itemid');
		}

		return $Itemid;
	}

	// Get cart Itemid
	public function getCartItemid()
	{
		$userhelper         = new rsUserhelper;
		$Itemid             = DEFAULT_CART_CHECKOUT_ITEMID;
		$shopper_group_data = $userhelper->getShoppergroupData();

		if (count($shopper_group_data) > 0 && $shopper_group_data->shopper_group_cart_itemid != 0)
		{
			$Itemid = $shopper_group_data->shopper_group_cart_itemid;
		}

		return $Itemid;
	}

	/**
	 *  Generate thumb image
	 *
	 *  @param   string  $section          Image section
	 *  @param   string  $ImageName        Image name
	 *  @param   string  $thumbWidth       Thumb width
	 *  @param   string  $thumbHeight      Thumb height
	 *  @param   string  $enableWatermark  Enable watermark
	 *
	 * @return  string
	 */
	public function watermark($section, $ImageName = '', $thumbWidth = '', $thumbHeight = '', $enableWatermark = WATERMARK_PRODUCT_IMAGE)
	{
		JLoader::load('RedshopHelperAdminImages');
		$pathMainImage = $section . '/' . $ImageName;

		try
		{
			// If main image not exists - display noimage
			if (!file_exists(REDSHOP_FRONT_IMAGES_RELPATH . $pathMainImage))
			{
				$pathMainImage = 'noimage.jpg';
				throw new Exception;
			}

			// If watermark not exists or disable - display simple thumb
			if ($enableWatermark <= 0
				|| !file_exists(REDSHOP_FRONT_IMAGES_RELPATH . 'product/' . WATERMARK_IMAGE))
			{
				throw new Exception;
			}

			// If width and height not set - use with and height original image
			if ((int) $thumbWidth == 0 && (int) $thumbHeight == 0)
			{
				list($thumbWidth, $thumbHeight) = getimagesize(REDSHOP_FRONT_IMAGES_RELPATH . $pathMainImage);
			}

			$imageNameWithPrefix = JFile::stripExt($ImageName) . '_w' . (int) $thumbWidth . '_h' . (int) $thumbHeight . '_i'
				. JFile::stripExt(basename(WATERMARK_IMAGE)) . '.' . JFile::getExt($ImageName);
			$destinationFile = REDSHOP_FRONT_IMAGES_RELPATH . $section . '/thumb/' . $imageNameWithPrefix;

			if (JFile::exists($destinationFile))
			{
				return REDSHOP_FRONT_IMAGES_ABSPATH . $section . '/thumb/' . $imageNameWithPrefix;
			}

			$file_path = JPATH_SITE . '/components/com_redshop/assets/images/product/' . WATERMARK_IMAGE;
			$filename = RedShopHelperImages::generateImages($file_path, '', 'thumb', 'product', $thumbWidth, $thumbHeight, 1);
			$filename_path_info = pathinfo($filename);
			$watermark = REDSHOP_FRONT_IMAGES_RELPATH . 'product/thumb/' . $filename_path_info['basename'];
			ob_start();
			RedShopHelperImages::resizeImage(
				REDSHOP_FRONT_IMAGES_RELPATH . $pathMainImage, $thumbWidth, $thumbHeight, USE_IMAGE_SIZE_SWAPPING, 'browser', false
			);
			$contents = ob_get_contents();
			ob_end_clean();

			if (!JFile::write($destinationFile, $contents))
			{
				return REDSHOP_FRONT_IMAGES_ABSPATH . $section . "/" . $ImageName;
			}

			switch (JFile::getExt(WATERMARK_IMAGE))
			{
				case 'gif':
					$dest = imagecreatefromjpeg($destinationFile);
					$src = imagecreatefromgif($watermark);
					list($width, $height) = getimagesize($destinationFile);
					list($markwidth, $markheight) = getimagesize($watermark);
					imagecopymerge($dest, $src, ($width - $markwidth) >> 1, ($height - $markheight) >> 1, 0, 0, $markwidth, $markheight, 50);
					imagejpeg($dest, $destinationFile);
					break;
				case 'png':
					$im = imagecreatefrompng($watermark);

					switch (JFile::getExt($destinationFile))
					{
						case 'gif':
							$im2 = imagecreatefromgif($destinationFile);
							break;
						case 'jpg':
							$im2 = imagecreatefromjpeg($destinationFile);
							break;
						case 'png':
							$im2 = imagecreatefrompng($destinationFile);
							break;
						default:
							throw new Exception;
					}

					imagecopy($im2, $im, (imagesx($im2) / 2) - (imagesx($im) / 2), (imagesy($im2) / 2) - (imagesy($im) / 2), 0, 0, imagesx($im), imagesy($im));
					$waterless = imagesx($im2) - imagesx($im);
					$rest = ceil($waterless / imagesx($im) / 2);

					for ($n = 1; $n <= $rest; $n++)
					{
						imagecopy(
							$im2, $im, ((imagesx($im2) / 2) - (imagesx($im) / 2)) - (imagesx($im) * $n),
							(imagesy($im2) / 2) - (imagesy($im) / 2), 0, 0, imagesx($im), imagesy($im)
						);

						imagecopy(
							$im2, $im, ((imagesx($im2) / 2) - (imagesx($im) / 2)) + (imagesx($im) * $n),
							(imagesy($im2) / 2) - (imagesy($im) / 2), 0, 0, imagesx($im), imagesy($im)
						);
					}

					imagejpeg($im2, $destinationFile);
					break;
				default:
					throw new Exception;
			}

			return REDSHOP_FRONT_IMAGES_ABSPATH . $section . '/thumb/' . $imageNameWithPrefix;
		}
		catch (Exception $e)
		{
			if ($e->getMessage())
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}

			if ((int) $thumbWidth == 0 && (int) $thumbHeight == 0)
			{
				$filename = REDSHOP_FRONT_IMAGES_ABSPATH . $pathMainImage;
			}
			else
			{
				$file_path = JPATH_SITE . '/components/com_redshop/assets/images/' . $pathMainImage;
				$filename = RedShopHelperImages::generateImages($file_path, '', 'thumb', $section, $thumbWidth, $thumbHeight, USE_IMAGE_SIZE_SWAPPING);
				$filename_path_info = pathinfo($filename);
				$filename = REDSHOP_FRONT_IMAGES_ABSPATH . $section . '/thumb/' . $filename_path_info['basename'];
			}

			return $filename;
		}
	}

	public function clickatellSMS($order_id)
	{
		if (CLICKATELL_ENABLE <= 0)
		{
			return;
		}

		$db = JFactory::getDbo();

		$shippinghelper = new shipping;

		$query = "SELECT * FROM " . $this->_table_prefix . "order_users_info AS oui "
			. "LEFT JOIN " . $this->_table_prefix . "orders AS o ON o.order_id = oui.order_id "
			. "WHERE oui.order_id = " . (int) $order_id . " "
			. "AND address_type='ST' ";
		$this->_db->setQuery($query);
		$orderData = $this->_db->loadobject();

		$query = "SELECT payment_method_name, oy.payment_method_id FROM " . $this->_table_prefix . "order_payment AS oy "
			. "LEFT JOIN " . $this->_table_prefix . "orders AS o ON o.order_id = oy.order_id "
			. "LEFT JOIN " . $this->_table_prefix . "payment_method AS p ON p.payment_method_id = oy.payment_method_id "
			. "WHERE oy.order_id = " . (int) $order_id;
		$this->_db->setQuery($query);
		$paymentData       = $this->_db->loadobject();
		$paymentName       = $paymentData->payment_method_name;
		$payment_method_id = $paymentData->payment_method_id;
		$redTemplate       = new Redtemplate;
		$TemplateDetail    = $redTemplate->getTemplate("clicktell_sms_message");

		$order_shipping_class = 0;
		$order_shipping       = explode("|", $shippinghelper->decryptShipping(str_replace(" ", "+", $orderData->ship_method_id)));

		if (isset($order_shipping[0]))
		{
			$order_shipping_class = $order_shipping[0];
		}

		$p_where = " AND (FIND_IN_SET( " . $db->quote($payment_method_id) . ", payment_methods ))";
		$s_where = " AND (FIND_IN_SET( " . $db->quote($order_shipping_class) . ", shipping_methods ))";

		$orderby = " ORDER BY `template_id` DESC LIMIT 0,1";
		$query   = "SELECT * FROM " . $this->_table_prefix . "template AS t "
			. "WHERE t.template_section = 'clicktell_sms_message' "
			. "AND (FIND_IN_SET( " . $db->quote($orderData->order_status) . ", order_status )) ";
		$to      = $orderData->phone;
		$this->_db->setQuery($query . $p_where . $orderby);
		$payment_methods = $this->_db->loadobject();
		$message         = $this->replaceMessage($payment_methods->template_desc, $orderData, $paymentName);

		if ($message)
		{
			$this->sendmessage(urlencode($message), $to);
		}

		$this->_db->setQuery($query . $s_where . $orderby);
		$shipping_methods = $this->_db->loadobject();

		$message = $this->replaceMessage($shipping_methods->template_desc, $orderData, $paymentName);

		if ($message)
		{
			$this->sendmessage(urlencode($message), $to);
		}

		if (CLICKATELL_ORDER_STATUS == $orderData->order_status)
		{
			$message = $this->replaceMessage($TemplateDetail[0]->template_desc, $orderData, $paymentName);

			if ($message)
			{
				$this->sendmessage(urlencode($message), $to);
			}
		}
	}

	public function sendmessage($text, $to)
	{
		// Clickatell_username
		$user     = CLICKATELL_USERNAME;

		// Clickatell_password
		$password = CLICKATELL_PASSWORD;

		// Clickatell_api_id
		$api_id   = CLICKATELL_API_ID;
		$baseurl  = "http://api.clickatell.com";

		// Auth call
		$url  = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";

		// Do auth call
		$ret  = file($url);

		// Split our response. return string is on first line of the data returned
		$sess = explode(":", $ret[0]);

		if ($sess[0] == "OK")
		{
			// Remove any whitespace
			$sess_id = trim($sess[1]);
			$url     = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

			// Do sendmsg call
			$ret  = file($url);
			$send = explode(":", $ret[0]);

			if ($send[0] == "ID")
			{
				echo "success message ID: " . $send[1];
			}
			else
			{
				JError::raiseWarning(21, "send message failed: ");
			}
		}
		else
		{
			JError::raiseWarning(21, "Authentication failure: " . $ret[0]);
		}
	}

	public function replaceMessage($message, $orderData, $paymentName)
	{
		$shippinghelper  = new shipping;
		$shipping_method = '';
		$details         = explode("|", $shippinghelper->decryptShipping(str_replace(" ", "+", $orderData->ship_method_id)));

		if (count($details) > 1)
		{
			$ext = "";

			if (array_key_exists(2, $details))
			{
				$ext = " (" . $details[2] . ")";
			}

			$shipping_method = $details[1] . $ext;
		}

		$producthelper = new producthelper;

		$userData = $producthelper->getUserInformation($orderData->user_id);

		$message = str_replace('{order_id}', $orderData->order_id, $message);
		$message = str_replace('{order_status}', $orderData->order_status, $message);
		$message = str_replace('{customer_name}', $userData->firstname, $message);
		$message = str_replace('{payment_status}', $orderData->order_payment_status, $message);
		$message = str_replace('{order_comment}', $orderData->customer_note, $message);
		$message = str_replace('{shipping_method}', $shipping_method, $message);
		$message = str_replace('{payment_method}', $paymentName, $message);

		return $message;
	}

	public function getsslLink($link, $applySSL)
	{
		$uri = JURI::getInstance($link);

		if ($applySSL)
		{
			$uri->setScheme('https');
		}
		else
		{
			$uri->setScheme('http');
		}

		$link = JFilterOutput::cleanText($uri->toString());

		return $link;
	}

	public function sslLink($link, $applySSL = 1)
	{
		if (!SSL_ENABLE_IN_BACKEND || $applySSL == 0)
		{
			return $link;
		}
		else
		{
			$url  = JURI::base();
			$link = $url . $link;
			$link = $this->getsslLink($link, $applySSL);
		}

		return $link;
	}

	public function getEconomicAccountGroup($accountgroup_id = 0, $front = 0)
	{
		$and = '';

		if ($accountgroup_id != 0)
		{
			$and .= 'AND ea.accountgroup_id = ' . (int) $accountgroup_id . ' ';
		}

		if ($front != 0)
		{
			$and .= 'AND ea.published="1" ';
		}

		$query = 'SELECT ea.*, ea.accountgroup_id AS value, ea.accountgroup_name AS text FROM ' . $this->_table_prefix . 'economic_accountgroup AS ea '
			. 'WHERE 1=1 '
			. $and;
		$this->_db->setQuery($query);
		$list = $this->_db->loadObjectlist();

		return $list;
	}

	public function isredProductfinder()
	{
		if (self::$isRedProductFinder === null)
		{
			// Get redshop from joomla component table
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('enabled')
				->from($db->qn('#__extensions'))
				->where('element = ' . $db->q('com_redproductfinder'));
			$redProductFinderPath = JPATH_ADMINISTRATOR . '/components/com_redproductfinder';

			if (!is_dir($redProductFinderPath) || $db->setQuery($query)->loadResult() == 0)
			{
				self::$isRedProductFinder = false;
			}
			else
			{
				self::$isRedProductFinder = true;
			}
		}

		return self::$isRedProductFinder;
	}

	/**
	 * check redCRM is installed or not.
	 *
	 * TODO: set session variable 'isredcrmuser'
	 * Set as boolean - check login user is redCRM contact person as well
	 *
	 * @return   boolean
	 *
	 * @since    1.0
	 */
	public function isredCRM()
	{
		$session = JFactory::getSession();

		// Get redshop from joomla component table
		$isredCRM = $session->get('isredCRM');

		if (is_null($isredCRM) && !empty($isredCRM))
		{
			$query = "SELECT extension_id FROM `#__extensions` WHERE `element` LIKE '%com_redcrm%'";
			$this->_db->setQuery($query);
			$this->_isredCRM = $this->_db->loadResult();
		}

		$redcrm_path = JPATH_ADMINISTRATOR . '/components/com_redcrm';

		if (!is_dir($redcrm_path) && !$this->_isredCRM)
		{
			$this->_isredCRM = false;
		}
		else
		{
			$user = JFactory::getUser();
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/configuration.php';
			$crmConfig = new crmConfig;
			$crmConfig->config();
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/helper.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/debitor.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/product.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/supplier_order.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_redcrm/helpers/order.php';

			$crmHelper = new crmHelper;

			$session = JFactory::getSession();

			if ($crmHelper->isredCRMUser($user->id))
			{
				$session->set('isredcrmuser', true);
			}
			else
			{
				$session->set('isredcrmuser', false);
			}

			$session->set('isredcrmuser_debitor', $crmHelper->isredCRMUserdebitor($user->id));

			$this->_isredCRM = true;
		}

		$session->set('isredCRM', $this->_isredCRM);

		return $this->_isredCRM;
	}
}
