<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


require_once 'components/com_redshop/models/configuration.php';

class RedshopModelWizard extends RedshopModelConfiguration
{
	public $_tax_rates = null;

	public function getTaxRates()
	{
		$query = "SELECT tax_group_id,tax_rate_id,tax_country,tax_rate FROM " . $this->_table_prefix . "tax_rate WHERE tax_group_id = 1";

		return $this->_getList($query);
	}
}
