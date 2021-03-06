<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.html.pagination');

class RedshopViewWrapper extends RedshopView
{
	/**
	 * The current user.
	 *
	 * @var  JUser
	 */
	public $user;

	public $lists = array();

	/**
	 * The request url.
	 *
	 * @var  string
	 */
	public $request_url;

	public function display($tpl = null)
	{
		$product_id = JRequest::getVar('product_id');

		$uri      = JFactory::getURI();
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();

		$document->setTitle(JText::_('COM_REDSHOP_WRAPPER'));

		$data       = $this->get('Data');
		$pagination = $this->get('Pagination');

		JToolBarHelper::title(JText::_('COM_REDSHOP_WRAPPER'), 'redshop_wrapper48');
		JToolbarHelper::addNew();
		JToolbarHelper::EditList();
		JToolBarHelper::deleteList();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		$context = 'wrapper_id';
		$this->lists['order'] = $app->getUserStateFromRequest($context . 'filter_order', 'filter_order', 'c.ordering');
		$this->lists['order_Dir'] = $app->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', '');

		$this->user = JFactory::getUser();
		$this->data = $data;
		$this->product_id = $product_id;
		$this->pagination = $pagination;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}
}
