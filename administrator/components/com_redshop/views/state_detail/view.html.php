<?php
/**
 * @package     redSHOP
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

class state_detailVIEWstate_detail extends JViewLegacy
{
    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();

        JToolBarHelper::title(JText::_('COM_REDSHOP_STATE_DETAIL'), 'redshop_region_48');
        $uri   = JFactory::getURI();
        $user  = JFactory::getUser();
        $model = $this->getModel('state_detail');

        JToolBarHelper::save();
        JToolBarHelper::apply();
        $lists  = array();
        $detail = $this->get('data');
        $isNew  = ($detail->state_id < 1);

        // 	fail if checked out not by 'me'
        if ($model->isCheckedOut($user->get('id')))
        {
            $msg = JText::sprintf('DESCBEINGEDITTED', JText::_('COM_REDSHOP_THE_DETAIL'), $detail->title);
            $mainframe->redirect('index.php?option=' . $option, $msg);
        }

        $text = $isNew ? JText::_('COM_REDSHOP_NEW') : JText::_('COM_REDSHOP_EDIT');
        $db   = jFactory::getDBO();
        JToolBarHelper::title(JText::_('COM_REDSHOP_STATE') . ': <small><small>[ ' . $text . ' ]</small></small>', 'redshop_region_48');
        require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'helper.php');
        $redhelper = new redhelper();
        $q         = "SELECT  country_id as value,country_name as text,country_jtext from #__" . TABLE_PREFIX . "_country ORDER BY country_name ASC";
        $db->setQuery($q);
        $countries = $db->loadObjectList();
        $countries = $redhelper->convertLanguageString($countries);

        $temps           = array();
        $temps[0]        = new stdClass;
        $temps[0]->value = "0";
        $temps[0]->text  = JText::_('COM_REDSHOP_SELECT');
        $countries       = @array_merge($temps, $countries);

        $lists['country_id'] = JHTML::_('select.genericlist', $countries, 'country_id', 'class="inputbox" size="1" ', 'value', 'text', $detail->country_id);

        $state_data = $redhelper->getStateAbbrivationByList();

        $lists['show_state'] = JHTML::_('select.genericlist', $state_data, 'show_state', 'class="inputbox" size="1" ', 'value', 'text', $detail->show_state);

        if ($isNew)
        {
            JToolBarHelper::cancel();
        }
        else
        {

            //EDIT - check out the item
            $model->checkout($user->get('id'));

            JToolBarHelper::cancel('cancel', 'Close');
        }

        JToolBarHelper::title(JText::_('COM_REDSHOP_state') . ': <small><small>[ ' . $text . ' ]</small></small>', 'redshop_region_48');

        $this->assignRef('detail', $detail);
        $this->assignRef('lists', $lists);
        $this->request_url = $uri->toString();

        parent::display($tpl);
    }
}

