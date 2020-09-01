<?php
/**
 * @package     Redshop.Library
 * @subpackage  Entity
 *
 * @copyright   Copyright (C) 2008 - 2020 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

namespace Redshop\Entity;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Order Payment Entity
 *
 * @package     Redshop.Library
 * @subpackage  Entity
 * @since       __DEPLOY_VERSION__
 */
class OrderPayment extends Entity
{
    /**
     * Get the associated table
     *
     * @param   string  $name  Main name of the Table. Example: Article for ContentTableArticle
     *
     * @return  \JTable
     * @since   __DEPLOY_VERSION__
     */
    public function getTable($name = null)
    {
        return \JTable::getInstance('Order_Payment', 'Table');
    }

    /**
     * Method for load plugin data of this payment
     *
     * @return  self
     *
     * @since   __DEPLOY_VERSION__
     */
    public function loadPlugin()
    {
        if (!$this->hasId() || !is_null($this->get('plugin', null))) {
            return $this;
        }

        if (!empty($this->get('payment_method_class'))) {
            // Get plugin information
            $plugin = \JPluginHelper::getPlugin('redshop_payment', $this->get('payment_method_class'));

            if ($plugin) {
                $plugin->params = new Registry($plugin->params);
            }

            $this->set('plugin', $plugin);
        }

        return $this;
    }
}
