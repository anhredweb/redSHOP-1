<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class shipping_rateViewshipping_rate extends JView
{
	public function display($tpl = null)
	{
		global $mainframe, $context;
		$context = 'shipping_rate';
		$uri = JFactory::getURI();
		$shippinghelper = new shipping;

		$lists['order'] = $mainframe->getUserStateFromRequest($context . 'filter_order', 'filter_order', 'shipping_rate_id');
		$lists['order_Dir'] = $mainframe->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', '');
		$id = $mainframe->getUserStateFromRequest($context . 'extension_id', 'extension_id', '0');

		$shipping = $shippinghelper->getShippingMethodById($id);

		$shipping_rates = $this->get('Data');
		$total = $this->get('Total');
		$pagination = $this->get('Pagination');

		$shippingpath = JPATH_ROOT . DS . 'plugins' . DS . $shipping->folder . DS . $shipping->element . '.xml';
		$myparams = new JRegistry($shipping->params, $shippingpath);
		$is_shipper = $myparams->get('is_shipper');
		$shipper_location = $myparams->get('shipper_location');

		$jtitle = ($shipper_location) ? JText::_('COM_REDSHOP_SHIPPING_LOCATION') : JText::_('COM_REDSHOP_SHIPPING_RATE');
		JToolBarHelper::title($jtitle . ' <small><small>[ ' . $shipping->name . ' ]</small></small>', 'redshop_shipping_rates48');
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();

		if ($is_shipper)
		{
			JToolBarHelper::customX('copy', 'copy.png', 'copy_f2.png', JText::_('COM_REDSHOP_TOOLBAR_COPY'), true);
		}

		JToolBarHelper::deleteList();
		JToolBarHelper::cancel('cancel', 'Close');

		$this->assignRef('lists', $lists);
		$this->assignRef('shipping_rates', $shipping_rates);
		$this->assignRef('shipping', $shipping);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('is_shipper', $is_shipper);
		$this->assignRef('shipper_location', $shipper_location);
		$this->assignRef('request_url', $uri->toString());

		parent::display($tpl);
	}
}
