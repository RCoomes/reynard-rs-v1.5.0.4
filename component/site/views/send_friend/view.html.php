<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


JLoader::load('RedshopHelperAdminCategory');

class RedshopViewSend_friend extends RedshopView
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		// Request variables
		$id     = JRequest::getInt('id');
		$Itemid = JRequest::getInt('Itemid');
		$pid    = JRequest::getInt('pid');

		$params = $app->getParams('com_redshop');

		$pathway  = $app->getPathway();

		// Include Javascript

		JHtml::script('com_redshop/attribute.js', false, true);
		JHtml::script('com_redshop/json.js', false, true);

		JHtml::stylesheet('com_redshop/scrollable-navig.css', array(), true);
		$data = $this->get('data');

		$template = $this->get('template');

		// Next/Prev navigation end

		$this->data = $data;
		$this->template = $template;

		$this->params = $params;
		parent::display($tpl);
	}
}
