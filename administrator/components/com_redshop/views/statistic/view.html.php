<?php
/**
 * @package     redSHOP
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die ('restricted access');

jimport('joomla.html.pagination');

class statisticViewstatistic extends JViewLegacy
{
    function display($tpl = null)
    {
        global $mainframe, $context;

        $uri    = JFactory::getURI();
        $layout = JRequest::getVar('layout');

        $startdate = JRequest::getVar('startdate');
        $enddate   = JRequest::getVar('enddate');

        $filteroption = JRequest::getVar('filteroption');
        $lists        = array();
        $option       = array();
        $option[]     = JHTML::_('select.option', '0"selected"', JText::_('COM_REDSHOP_Select'));
        $option[]     = JHTML::_('select.option', '1', JText::_('COM_REDSHOP_DAILY'));
        $option[]     = JHTML::_('select.option', '2', JText::_('COM_REDSHOP_WEEKLY'));
        $option[]     = JHTML::_('select.option', '3', JText::_('COM_REDSHOP_MONTHLY'));
        $option[]     = JHTML::_('select.option', '4', JText::_('COM_REDSHOP_YEARLY'));

        $lists['filteroption'] = JHTML::_('select.genericlist', $option, 'filteroption', 'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $filteroption);

        $redshopviewer      = array();
        $pageviewer         = array();
        $avgorderamount     = array();
        $popularsell        = array();
        $bestsell           = array();
        $newprod            = array();
        $neworder           = array();
        $totalturnover      = array();
        $amountorder        = array();
        $amountprice        = array();
        $amountspentintotal = array();

        $limitstart = $mainframe->getUserStateFromRequest($context . 'limitstart', 'limitstart', '0');
        $limit      = $mainframe->getUserStateFromRequest($context . 'limit', 'limit', '10');

        if ($layout == 'turnover')
        {
            $this->setLayout('turnover');
            $title         = JText::_('COM_REDSHOP_TOTAL_TURNOVER');
            $totalturnover = $this->get('TotalTurnover');
            $total         = count($totalturnover);
        }
        elseif ($layout == 'pageview')
        {
            $this->setLayout('pageview');
            $title      = JText::_('COM_REDSHOP_TOTAL_PAGEVIEWERS');
            $pageviewer = $this->get('PageViewer');
            $total      = count($pageviewer);
        }
        elseif ($layout == 'amountorder')
        {
            $this->setLayout('amountorder');
            $title       = JText::_('COM_REDSHOP_TOP_CUSTOMER_AMOUNT_OF_ORDER');
            $amountorder = $this->get('AmountOrder');
            $total       = count($amountorder);
        }
        elseif ($layout == 'avrgorder')
        {
            $this->setLayout('avrgorder');
            $title          = JText::_('COM_REDSHOP_AVG_ORDER_AMOUNT_CUSTOMER');
            $avgorderamount = $this->get('AvgOrderAmount');
            $total          = count($avgorderamount);
        }
        elseif ($layout == 'amountprice')
        {
            $this->setLayout('amountprice');
            $title       = JText::_('COM_REDSHOP_TOP_CUSTOMER_AMOUNT_OF_PRICE_PER_ORDER');
            $amountprice = $this->get('AmountPrice');
            $total       = count($amountprice);
        }
        elseif ($layout == 'amountspent')
        {
            $this->setLayout('amountspent');
            $title              = JText::_('COM_REDSHOP_TOP_CUSTOMER_AMOUNT_SPENT_IN_TOTAL');
            $amountspentintotal = $this->get('AmountSpentInTotal');
            $total              = count($amountspentintotal);
        }
        elseif ($layout == 'bestsell')
        {
            $this->setLayout('bestsell');
            $title    = JText::_('COM_REDSHOP_BEST_SELLERS');
            $bestsell = $this->get('BestSellers');
            $total    = count($bestsell);
        }
        elseif ($layout == 'popularsell')
        {
            $this->setLayout('popularsell');
            $title       = JText::_('COM_REDSHOP_MOST_VISITED_PRODUCTS');
            $popularsell = $this->get('MostPopular');
            $total       = count($popularsell);
        }
        elseif ($layout == 'newprod')
        {
            $this->setLayout('newprod');
            $title   = JText::_('COM_REDSHOP_NEWEST_PRODUCTS');
            $newprod = $this->get('NewProducts');
            $total   = count($newprod);
        }
        elseif ($layout == 'neworder')
        {
            $this->setLayout('neworder');
            $title    = JText::_('COM_REDSHOP_NEWEST_ORDERS');
            $neworder = $this->get('NewOrders');
            $total    = count($neworder);
        }
        else
        {
            $this->setLayout('default');
            $title         = JText::_('COM_REDSHOP_TOTAL_VISITORS');
            $redshopviewer = $this->get('RedshopViewer');
            $total         = count($redshopviewer);
        }

        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_REDSHOP_STATISTIC'));

        JToolBarHelper::title(JText::_('COM_REDSHOP_STATISTIC') . " :: " . $title, 'redshop_statistic48');

        $pagination = new JPagination($total, $limitstart, $limit);
        $this->assignRef('pagination', $pagination);

        $this->assignRef('startdate', $startdate);
        $this->assignRef('enddate', $enddate);

        $this->assignRef('popularsell', $popularsell);
        $this->assignRef('bestsell', $bestsell);
        $this->assignRef('avgorderamount', $avgorderamount);
        $this->assignRef('newprod', $newprod);
        $this->assignRef('neworder', $neworder);
        $this->assignRef('totalturnover', $totalturnover);
        $this->assignRef('amountorder', $amountorder);
        $this->assignRef('amountprice', $amountprice);
        $this->assignRef('amountspentintotal', $amountspentintotal);
        $this->assignRef('redshopviewer', $redshopviewer);
        $this->assignRef('pageviewer', $pageviewer);
        $this->assignRef('lists', $lists);
        $this->assignRef('filteroption', $filteroption);
        $this->assignRef('layout', $layout);
        $this->request_url = $uri->toString();

        parent::display($tpl);
    }
}


