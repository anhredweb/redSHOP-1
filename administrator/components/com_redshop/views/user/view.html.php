<?php
/**
 * @package     redSHOP
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die ('Restricted access');

class userViewuser extends JViewLegacy
{
    function display($tpl = null)
    {
        global $mainframe, $context;

        $context                   = 'user_info_id';
        $userhelper                = new rsUserhelper();
        $uri                       = JFactory::getURI();
        $sync                      = JRequest::getVar('sync');
        $spgrp_filter              = JRequest::getVar('spgrp_filter', '', 'request', 'string');
        $tax_exempt_request_filter = JRequest::getVar('tax_exempt_request_filter', '', 'request', 'string');

        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_REDSHOP_USER'));

        JToolBarHelper::title(JText::_('COM_REDSHOP_USER_MANAGEMENT'), 'redshop_user48');

        if ($sync)
        {
            $this->setLayout('user_sync');
            $sync_user = $userhelper->userSynchronization();
            $this->assignRef('sync_user', $sync_user);
        }
        else
        {
            $this->setLayout('default');
            JToolBarHelper::addNewX();
            JToolBarHelper::editListX();
            JToolBarHelper::deleteList();
        }

        $filter_order        = $mainframe->getUserStateFromRequest($context . 'filter_order', 'filter_order', 'users_info_id');
        $filter_order_Dir    = $mainframe->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', '');
        $lists ['order']     = $filter_order;
        $lists ['order_Dir'] = $filter_order_Dir;

        $user           = $this->get('Data');
        $pagination     = $this->get('Pagination');
        $shopper_groups = $userhelper->getShopperGroupList();

        $temps           = array();
        $temps[0]        = new stdClass;
        $temps[0]->value = 0;
        $temps[0]->text  = JText::_('COM_REDSHOP_SELECT');
        $shopper_groups  = array_merge($temps, $shopper_groups);

        $lists['shopper_group'] = JHTML::_('select.genericlist', $shopper_groups, 'spgrp_filter', 'class="inputbox" size="1" onchange="document.adminForm.submit()"', 'value', 'text', $spgrp_filter);

        $optiontax_req               = array();
        $optiontax_req[]             = JHTML::_('select.option', 'select', JText::_('COM_REDSHOP_SELECT'));
        $optiontax_req[]             = JHTML::_('select.option', '1', JText::_('COM_REDSHOP_yes'));
        $optiontax_req[]             = JHTML::_('select.option', '0', JText::_('COM_REDSHOP_no'));
        $lists['tax_exempt_request'] = JHTML::_('select.genericlist', $optiontax_req, 'tax_exempt_request_filter', 'class="inputbox" size="1" onchange="document.adminForm.submit()"', 'value', 'text', $tax_exempt_request_filter);

        $this->assignRef('lists', $lists);
        $this->assignRef('user', $user);
        $this->assignRef('pagination', $pagination);
        $this->request_url = $uri->toString();

        parent::display($tpl);
    }
}
