<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


/**
 * Class categoryModelcategory
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelCategory extends RedshopModel
{
	public $_id = null;

	public $_data = null;

	public $_product = null;

	public $_template = null;

	public $_limit = null;

	public $_slidercount = 0;

	public $_maincat = null;

	public $count_no_user_field = 0;

	public $minmaxArr = array(0, 0);

	// @ToDo In feature, when class Category extends RedshopModelList, replace filter_fields in constructor
	public $filter_fields = array(
		'p.product_name', 'product_name',
		'p.product_price', 'product_price',
		'p.product_price', 'product_price',
		'p.product_number', 'product_number',
		'p.product_id', 'product_id',
		'pc.ordering', 'ordering'
	);

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$params = $app->getParams('com_redshop');
		$layout = $input->getCmd('layout', 'detail');
		$print  = $input->getCmd('print', '');
		$Id     = $input->getInt('cid', 0);

		if (!$print && !$Id)
		{
			$Id = (int) $params->get('cid');
		}

		// Different context depending on the view
		if (empty($this->context))
		{
			$this->context = strtolower('com_redshop.category.' . $this->getName() . '.' . $layout . '.' . $Id);
		}

		parent::__construct();
		$this->producthelper = new producthelper;

		$this->setId((int) $Id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = '', $direction = '')
	{
		$app = JFactory::getApplication();
		$params = $app->getParams('com_redshop');
		$selectedTemplate = DEFAULT_CATEGORYLIST_TEMPLATE;
		$layout = $app->input->getCmd('layout', 'detail');

		if ($this->_id)
		{
			$selectedTemplate  = (int) $params->get('category_template', 0);
			$mainCat = $this->_loadCategory();

			if (!$selectedTemplate && isset($mainCat->category_template))
			{
				$selectedTemplate = $mainCat->category_template;
			}
		}

		$categoryTemplate = $app->getUserStateFromRequest($this->context . '.category_template', 'category_template', $selectedTemplate, 'int');
		$this->setState('category_template', $categoryTemplate);

		if ($_POST)
		{
			$manufacturerId = $app->input->post->getInt('manufacturer_id', 0);

			if ($manufacturerId != $app->getUserState($this->context . '.manufacturer_id', $app->input->get->getInt('manufacturer_id', 0)))
			{
				$app->redirect(
					JRoute::_(
						'index.php?option=com_redshop&view=category&layout=' . $layout . '&cid=' . $this->_id . '&manufacturer_id=' . $manufacturerId
						. '&Itemid=' . $app->input->getInt('Itemid', 0),
						true
					)
				);
			}
		}
		else
		{
			$manufacturerId = $app->input->getInt('manufacturer_id', 0);
			$app->setUserState($this->context . '.manufacturer_id', $manufacturerId);
		}

		$this->setState('manufacturer_id', $manufacturerId);

		// Get default ordering
		$orderBySelect = $params->get('order_by', DEFAULT_PRODUCT_ORDERING_METHOD);
		$editTimestamp = $params->get('editTimestamp', 0);
		$userTimestamp = $app->getUserState($this->context . '.editTimestamp', 0);
		list($ordering, $direction) = explode(' ', $orderBySelect);

		if ($editTimestamp > $userTimestamp)
		{
			$app->setUserState($this->context . '.order_by', $orderBySelect);
		}

		$app->setUserState($this->context . '.editTimestamp', time());
		$value = $app->getUserStateFromRequest($this->context . '.order_by', 'order_by', $orderBySelect);
		$orderingParts = explode(' ', $value);

		if (count($orderingParts) >= 2)
		{
			// Latest part will be considered the direction
			$fullDirection = end($orderingParts);

			if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
			{
				$this->setState('list.direction', $fullDirection);
			}

			unset($orderingParts[count($orderingParts) - 1]);

			// The rest will be the ordering
			$fullOrdering = implode(' ', $orderingParts);

			if (in_array($fullOrdering, $this->filter_fields))
			{
				$this->setState('list.ordering', $fullOrdering);
			}
		}
		else
		{
			$this->setState('list.ordering', $ordering);
			$this->setState('list.direction', $direction);
		}

		$limit = 0;

		if (isset($this->_template[0]->template_desc)
			&& !strstr($this->_template[0]->template_desc, "{show_all_products_in_category}")
			&& strstr($this->_template[0]->template_desc, "{pagination}")
			&& strstr($this->_template[0]->template_desc, "perpagelimit:"))
		{
			$perpage = explode('{perpagelimit:', $this->_template[0]->template_desc);
			$perpage = explode('}', $perpage[1]);
			$limit   = intval($perpage[0]);
		}
		else
		{
			if ($this->_id)
			{
				$limit = 0;
				$item = $app->getMenu()->getActive();

				if ($item)
				{
					$limit = (int) $item->params->get('maxproduct', 0);
				}

				if (!$limit)
				{
					$limit = $this->_maincat->products_per_page;
				}
			}

			if (!$limit)
			{
				$limit = MAXCATEGORY;
			}
		}

		$this->setState('list.limit', $limit);
		$value = $app->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	public function setId($id)
	{
		$this->_id   = $id;
		$this->_data = null;
	}

	public function _buildQuery()
	{
		$app             = JFactory::getApplication();
		$menu            = $app->getMenu();
		$item            = $menu->getActive();
		$manufacturer_id = (isset($item)) ? intval($item->params->get('manufacturer_id')) : 0;
		$manufacturer_id = $app->input->getInt('manufacturer_id', $manufacturer_id, '', 'int');

		$layout  = $app->input->getCmd('layout');
		$orderby = ($layout != "categoryproduct") ? $this->_buildContentOrderBy() : "";
		$groupby = $and = $left = "";

		if ($manufacturer_id)
		{
			$left    = "LEFT JOIN #__redshop_product_category_xref AS pcx ON pcx.category_id = c.category_id "
				. "LEFT JOIN #__redshop_product AS p ON p.product_id = pcx.product_id "
				. "LEFT JOIN #__redshop_manufacturer AS m ON m.manufacturer_id = p.manufacturer_id ";
			$and     = "AND m.manufacturer_id = " . (int) $manufacturer_id . " ";
			$groupby = "GROUP BY c.category_id ";
		}

		$query = "SELECT c.* FROM #__redshop_category AS c "
			. "LEFT JOIN #__redshop_category_xref AS cx ON cx.category_child_id=c.category_id "
			. $left
			. "WHERE c.published = 1 "
			. "AND cx.category_parent_id = " . (int) $this->_id . " "
			. $and
			. $groupby
			. $orderby;

		return $query;
	}

	public function _buildContentOrderBy()
	{
		if (DEFAULT_CATEGORY_ORDERING_METHOD)
		{
			$orderby = " ORDER BY " . DEFAULT_CATEGORY_ORDERING_METHOD;
		}
		else
		{
			$orderby = " ORDER BY c.ordering";
		}

		return $orderby;
	}

	public function _loadCategory()
	{
		$this->_maincat = RedshopHelperCategory::getCategoryById($this->_id);

		return $this->_maincat;
	}

	public function getCategorylistProduct($category_id = 0)
	{
		$app   = JFactory::getApplication();
		$menu  = $app->getMenu();
		$item  = $menu->getActive();
		$limit = (isset($item)) ? intval($item->params->get('maxproduct')) : 0;

		$order_by = (isset($item)) ? $item->params->get('order_by', 'p.product_name ASC') : 'p.product_name ASC';

		$query = "SELECT * FROM #__redshop_product AS p "
			. "LEFT JOIN #__redshop_product_category_xref AS pc ON pc.product_id=p.product_id "
			. "LEFT JOIN #__redshop_category AS c ON c.category_id=pc.category_id "
			. "LEFT JOIN #__redshop_manufacturer AS m ON m.manufacturer_id=p.manufacturer_id "
			. "WHERE p.published = 1 AND p.expired = 0 "
			. "AND pc.category_id = " . (int) $category_id . " "
			. "AND p.product_parent_id = 0  order by "
			. $order_by . " LIMIT 0," . $limit;

		$this->_product = $this->_getList($query);

		return $this->_product;
	}

	/**
	 * Method get Product of Category
	 *
	 * @param   int   $minmax    default variable is 0
	 * @param   bool  $isSlider  default variable is false
	 *
	 * @return mixed
	 */
	public function getCategoryProduct($minmax = 0, $isSlider = false)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$orderBy = $this->buildProductOrderBy();

		if ($minmax && !(strstr($orderBy, "p.product_price ASC") || strstr($orderBy, "p.product_price DESC")))
		{
			$orderBy = "p.product_price ASC";
		}

		$query = $db->getQuery(true);

		$manufacturerId = $this->getState('manufacturer_id');
		$endlimit = $this->getState('list.limit');
		$limitstart = $this->getState('list.start');
		$sort = "";

		// Shopper group - choose from manufactures Start
		$rsUserhelper               = new rsUserhelper;
		$shopperGroupManufactures = $rsUserhelper->getShopperGroupManufacturers();

		if ($shopperGroupManufactures != "")
		{
			$shopperGroupManufactures = explode(',', $shopperGroupManufactures);
			JArrayHelper::toInteger($shopperGroupManufactures);
			$shopperGroupManufactures = implode(',', $shopperGroupManufactures);
			$query->where('p.manufacturer_id IN (' . $shopperGroupManufactures . ')');
		}

		// Shopper group - choose from manufactures End

		if ($manufacturerId && $manufacturerId > 0)
		{
			$query->where('p.manufacturer_id = ' . (int) $manufacturerId);
		}

		$query->select('p.product_id')
			->from($db->qn('#__redshop_product', 'p'))
			->leftJoin('#__redshop_product_category_xref AS pc ON pc.product_id = p.product_id')
			->where(
				array(
					'p.published = 1', 'p.expired = 0',
					'pc.category_id = ' . (int) $this->_id,
					'p.product_parent_id = 0'
				)
			)
			->order($orderBy);

		$finder_condition = $this->getredproductfindertags();

		if ($finder_condition != '')
		{
			$finder_condition = str_replace("AND", "", $finder_condition);
			$query->where($finder_condition);
		}

		$queryCount = clone $query;
		$queryCount->clear('select')
			->select('COUNT(DISTINCT(p.product_id))');

		// First steep get product ids
		if ($minmax != 0 || $isSlider)
		{
			$db->setQuery($query);
		}
		else
		{
			$db->setQuery($query, $limitstart, $endlimit);
		}

		$this->_product = array();

		if ($productIds = $db->loadColumn())
		{
			// Third steep get all product relate info
			$query->clear()
				->where('p.product_id IN (' . implode(',', $productIds) . ')')
				->order('FIELD(p.product_id, ' . implode(',', $productIds) . ')');

			$query = RedshopHelperProduct::getMainProductQuery($query, $user->id)
				->select(
					array(
						'pc.ordering', 'c.*', 'm.*',
						'CONCAT_WS(' . $db->q('.') . ', p.product_id, ' . (int) $user->id . ') AS concat_id'
					)
				)
				->leftJoin('#__redshop_category AS c ON c.category_id = pc.category_id')
				->leftJoin('#__redshop_manufacturer AS m ON m.manufacturer_id = p.manufacturer_id')
				->where('pc.category_id = ' . (int) $this->_id);

			if ($products = $db->setQuery($query)->loadObjectList('concat_id'))
			{
				RedshopHelperProduct::setProduct($products);
				$this->_product = array_values($products);
			}
		}

		$priceSort = false;

		if (strstr($orderBy, "p.product_price ASC"))
		{
			$priceSort = true;

			for ($i = 0; $i < count($this->_product); $i++)
			{
				$ProductPriceArr                  = $this->producthelper->getProductNetPrice($this->_product[$i]->product_id);
				$this->_product[$i]->productPrice = $ProductPriceArr['product_price'];
			}

			$this->_product = $this->columnSort($this->_product, 'productPrice', 'ASC');
		}
		elseif (strstr($orderBy, "p.product_price DESC"))
		{
			$priceSort = true;
			$sort      = "DESC";

			for ($i = 0; $i < count($this->_product); $i++)
			{
				$ProductPriceArr                  = $this->producthelper->getProductNetPrice($this->_product[$i]->product_id);
				$this->_product[$i]->productPrice = $ProductPriceArr['product_price'];
			}

			$this->_product = $this->columnSort($this->_product, 'productPrice', 'DESC');
		}

		if ($minmax > 0)
		{
			$min = 0;

			if (!empty($priceSort))
			{
				if ($sort == "DESC")
				{
					$max = $this->_product[0]->productPrice + 100;
					$min = $this->_product[count($this->_product) - 1]->productPrice;
				}
				else
				{
					$min = $this->_product[0]->productPrice;
					$max = $this->_product[count($this->_product) - 1]->productPrice + 100;
				}
			}
			else
			{
				$ProductPriceArr = $this->producthelper->getProductNetPrice($this->_product[0]->product_id);
				$min             = $ProductPriceArr['product_price'];
				$ProductPriceArr = $this->producthelper->getProductNetPrice($this->_product[count($this->_product) - 1]->product_id);
				$max             = $ProductPriceArr['product_price'];

				if ($min >= $max)
				{
					$min = $this->_product[0]->product_price;
					$max = $max + 100;
				}
			}

			$this->_product[0]->minprice = floor($min);
			$this->_product[0]->maxprice = ceil($max);
			$this->setMaxMinProductPrice(array(floor($min), ceil($max)));
		}
		elseif ($isSlider)
		{
			$newProduct = array();

			for ($i = 0; $i < count($this->_product); $i++)
			{
				$ProductPriceArr                 = $this->producthelper->getProductNetPrice($this->_product[$i]->product_id);
				$this->_product[$i]->sliderprice = $ProductPriceArr['product_price'];

				if ($this->_product[$i]->sliderprice >= $this->minmaxArr[0] && $this->_product[$i]->sliderprice <= $this->minmaxArr[1])
				{
					$newProduct[] = $this->_product[$i];
				}
			}

			$this->_product = $newProduct;
			$this->_total   = count($this->_product);
		}
		else
		{
			$db->setQuery($queryCount);
			$this->_total = $db->loadResult();
		}

		return $this->_product;
	}

	public function columnSort($unsorted, $column, $sort)
	{
		$sorted = $unsorted;

		if ($sort == "ASC")
		{
			for ($i = 0; $i < count($sorted) - 1; $i++)
			{
				for ($j = 0; $j < count($sorted) - 1 - $i; $j++)
				{
					if ($sorted[$j]->$column > $sorted[$j + 1]->$column)
					{
						$tmp            = $sorted[$j];
						$sorted[$j]     = $sorted[$j + 1];
						$sorted[$j + 1] = $tmp;
					}
				}
			}
		}
		else
		{
			for ($i = 0; $i < count($sorted) - 1; $i++)
			{
				for ($j = 0; $j < count($sorted) - 1 - $i; $j++)
				{
					if ($sorted[$j]->$column < $sorted[$j + 1]->$column)
					{
						$tmp            = $sorted[$j];
						$sorted[$j]     = $sorted[$j + 1];
						$sorted[$j + 1] = $tmp;
					}
				}
			}
		}

		return $sorted;
	}

	/**
	 * Method get string order by of product when choose category
	 *
	 * @return  string
	 */
	public function buildProductOrderBy()
	{
		$db = JFactory::getDbo();
		list($filterOrder, $filterOrderDir) = explode(' ', DEFAULT_PRODUCT_ORDERING_METHOD);
		$filterOrder = $this->getState('list.ordering', $filterOrder);
		$filterOrderDir = $this->getState('list.direction', $filterOrderDir);

		return $db->escape($filterOrder . ' ' . $filterOrderDir);
	}

	public function getData()
	{
		$app = JFactory::getApplication();

		global $context;

		$endlimit   = $this->getState('list.limit');
		$limitstart = $this->getState('list.start');
		$layout     = JRequest::getVar('layout');
		$query      = $this->_buildQuery();

		if ($layout == "categoryproduct")
		{
			$menu        = $app->getMenu();
			$item        = $menu->getActive();
			$endlimit    = (isset($item)) ? intval($item->params->get('maxcategory')) : 0;
			$limit       = $app->getUserStateFromRequest($context . 'limit', 'limit', $endlimit, 5);
			$this->_data = $this->_getList($query, $limitstart, $endlimit);

			return $this->_data;
		}

		if ($this->_id)
		{
			$this->_data = $this->_getList($query);
		}
		else
		{
			if (!strstr($this->_template[0]->template_desc, "{show_all_products_in_category}") && strstr($this->_template[0]->template_desc, "{pagination}"))
			{
				$this->_data = $this->_getList($query, $limitstart, $endlimit);
			}
			else
			{
				if (strstr($this->_template[0]->template_desc, "{show_all_products_in_category}"))
				{
					$this->_data = $this->_getList($query);
				}
				else
				{
					$this->_data = $this->_getList($query, 0, MAXCATEGORY);
				}
			}
		}

		return $this->_data;
	}

	public function getCategoryPagination()
	{
		$endlimit          = $this->getState('list.limit');
		$limitstart        = $this->getState('list.start');
		$this->_pagination = new JPagination($this->getTotal(), $limitstart, $endlimit);

		return $this->_pagination;
	}

	public function getCategoryProductPagination()
	{
		$app = JFactory::getApplication();
		$menu     = $app->getMenu();
		$item     = $menu->getActive();
		$endlimit = (isset($item)) ? intval($item->params->get('maxcategory')) : 0;

		$limitstart        = $this->getState('list.start');
		$this->_pagination = new JPagination($this->getTotal(), $limitstart, $endlimit);

		return $this->_pagination;
	}

	public function getTotal()
	{
		$query        = $this->_buildQuery();
		$this->_total = $this->_getListCount($query);

		return $this->_total;
	}

	public function getCategoryTemplate()
	{
		$category_template = $this->getState('category_template');

		$redTemplate = new Redtemplate;

		if ($this->_id)
		{
			$selected_template = $this->_maincat->category_template;

			if (isset($category_template) && $category_template != '')
			{
				$selected_template .= "," . $category_template;
			}

			if ($this->_maincat->category_more_template != "")
			{
				$selected_template .= "," . $this->_maincat->category_more_template;
			}

			$alltemplate = $redTemplate->getTemplate("category", $selected_template);
		}
		else
		{
			$alltemplate = $redTemplate->getTemplate("frontpage_category");
		}

		return $alltemplate;
	}

	public function loadCategoryTemplate()
	{
		$category_template = (int) $this->getState('category_template');
		$redTemplate       = new Redtemplate;

		$selected_template = DEFAULT_CATEGORYLIST_TEMPLATE;
		$template_section  = "frontpage_category";

		if ($this->_id)
		{
			$template_section = "category";

			if (isset($category_template) && $category_template != 0)
			{
				$selected_template = $category_template;
			}
			elseif (isset($this->_maincat->category_template))
			{
				$selected_template = $this->_maincat->category_template;
			}
		}

		$category_template_id = JRequest::getInt('category_template', $selected_template, '', 'int');
		$this->_template      = $redTemplate->getTemplate($template_section, $category_template_id);

		return $this->_template;
	}

	public function getManufacturer($mid = 0)
	{
		$and = "";
		$cid = JRequest::getVar('cid');

		if ($mid != 0)
		{
			$and = " AND m.manufacturer_id = " . (int) $mid . " ";
		}

		$query = "SELECT DISTINCT(m.manufacturer_id ),m.* FROM #__redshop_manufacturer AS m "
			. "LEFT JOIN #__redshop_product AS p ON m.manufacturer_id  = p.manufacturer_id ";

		if ($cid != 0)
		{
			$query .= "LEFT JOIN #__redshop_product_category_xref AS pcx ON p.product_id  = pcx.product_id ";
			$and .= " AND pcx.category_id = " . (int) $cid . " ";
		}

		$query .= "WHERE p.manufacturer_id != 0 AND m.published = 1 " . $and . "ORDER BY m.ordering ASC";
		$this->_db->setQuery($query);
		$list = $this->_db->loadObjectList();

		return $list;
	}

	public function setMaxMinProductPrice($minmax = array(0, 0))
	{
		$this->minmaxArr = $minmax;
	}

	public function getMaxMinProductPrice()
	{
		return $this->minmaxArr;
	}

	/**
	 * Function to get Product List Array with searched Letter
	 *
	 * @return array
	 */
	public function getAllproductArrayListwithfirst($letter, $fieldid)
	{
		$endlimit = $this->getState('list.limit');

		$limitstart = $this->getState('list.start');
		$query      = $this->_buildfletterQuery($letter, $fieldid);

		if (strstr($this->_template[0]->template_desc, "{pagination}"))
		{
			$product_lists = $this->_getList($query, $limitstart, $endlimit);
		}
		else
		{
			$product_lists = $this->_getList($query, $limitstart, $endlimit);
		}

		return $product_lists;
	}

	public function _buildfletterQuery($letter, $fieldid)
	{
		$db = JFactory::getDbo();
		$query = "SELECT p.*, fd.* FROM #__redshop_product AS p ";
		$query .= " LEFT JOIN #__redshop_fields_data AS fd ON fd.itemid = p.product_id";
		$query .= " WHERE  fd.data_txt LIKE " . $db->quote($letter . '%') . " AND fd.fieldid = "
			. (int) $fieldid . "  AND  fd.section=1 AND p.published =1 ORDER BY product_name ";

		return $query;
	}

	public function getfletterPagination($letter, $fieldid)
	{
		$endlimit          = $this->getState('list.limit');
		$limitstart        = $this->getState('list.start');
		$this->_pagination = new JPagination($this->getfletterTotal($letter, $fieldid), $limitstart, $endlimit);

		return $this->_pagination;
	}

	public function getfletterTotal($letter, $fieldid)
	{
		if (empty ($this->_total))
		{
			$query        = $this->_buildfletterQuery($letter, $fieldid);
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	public function getredproductfindertags()
	{
		global $context;

		$app = JFactory::getApplication();

		$setproductfinderobj = new redhelper;
		$setproductfinder    = $setproductfinderobj->isredProductfinder();
		$finder_condition    = "";

		if ($setproductfinder)
		{
			$query = "SELECT id FROM #__redproductfinder_filters WHERE published=1";
			$this->_db->setQuery($query);
			$rs_filters = $this->_db->loadColumn();

			if (count($rs_filters) > 0)
			{
				$this->_is_filter_enable = true;
			}

			$tag = '';

			for ($f = 0; $f < count($rs_filters); $f++)
			{
				$tmp_tag = $app->getUserStateFromRequest($context . 'tag' . $rs_filters[$f], 'tag' . $rs_filters[$f], '');

				if (is_array($tmp_tag))
				{
					$tag = $tmp_tag;
				}
				elseif ($tmp_tag != "" && $tmp_tag != "0")
				{
					$tag[] = $tmp_tag;
				}
			}

			$finder_where     = "";
			$finder_query     = "";

			$findercomponent      = JComponentHelper::getComponent('com_redproductfinder');
			$productfinderconfig  = new JRegistry($findercomponent->params);
			$finder_filter_option = $productfinderconfig->get('redshop_filter_option');

			if ($tag)
			{
				if (is_array($tag))
				{
					if (count($tag) > 1 || $tag[0] != 0)
					{
						$finder_query = "SELECT product_id FROM #__redproductfinder_associations AS a,#__redproductfinder_association_tag AS at ";
						$finder_where = "";

						if (count($tag) > 1)
						{
							$i = 1;

							for ($t = 1; $t < count($tag); $t++)
							{
								$finder_query .= " LEFT JOIN #__redproductfinder_association_tag AS at" . $t . " ON at" . $t . ".association_id=at.association_id";
								$finder_where[] = " at" . $t . ".tag_id = " . (int) $tag[$t] . " ";
								$i++;
							}
						}

						$finder_query .= " WHERE a.id = at.association_id AND at.tag_id = " . (int) $tag[0] . " ";

						if (is_array($finder_where))
						{
							$finder_where = " AND " . implode(" AND ", $finder_where);
						}

						$finder_query .= $finder_where;
						$this->_db->setQuery($finder_query);
						$rs              = $this->_db->loadColumn();
						$finder_products = "";

						if (!empty($rs))
						{
							// Sanitise ids
							JArrayHelper::toInteger($rs);

							$finder_products = implode("','", $rs);
						}

						$finder_condition        = " AND p.product_id IN('" . $finder_products . "')";
						$this->_is_filter_enable = true;
					}

					if (count($tag) == 1 && $tag[0] == 0)
					{
						$finder_condition = "";
					}
				}
			}
		}

		return $finder_condition;
	}
}
