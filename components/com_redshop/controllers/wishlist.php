<?php
/**
 * @package     redSHOP
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die('Restricted access');

/**
 * wishlist Controller
 *
 * @static
 * @package        redSHOP
 * @since          1.0
 */
class wishlistController extends JControllerLegacy
{
    /**
     * createsave wishlist function
     *
     * @access public
     * @return void
     */
    function createsave()
    {
        $user  = &JFactory::getUser();
        $model = & $this->getModel("wishlist");

        $post ['wishlist_name'] = JRequest :: getVar('txtWishlistname');
        $post ['user_id']       = $user->id;
        $post ['cdate']         = time();
        if ($model->store($post))
        {
            echo "<div>" . JText::_('COM_REDSHOP_PRODUCT_SAVED_IN_WISHLIST_SUCCESSFULLY') . "</div>";
        }
        else
        {
            echo "<div>" . JText::_('COM_REDSHOP_PRODUCT_NOT_SAVED_IN_WISHLIST') . "</div>";
        }
        if (JRequest::getVar('loginwishlist') == 1)
        {
            $wishreturn = JRoute::_('index.php?option=com_redshop&view=wishlist&task=viewwishlist&Itemid=' . JRequest::getVar('Itemid'), false);
            $this->setRedirect($wishreturn);
        }
        else
        {
            ?>
        <script language="javascript">
            var t = setTimeout("window.parent.SqueezeBox.close();window.parent.location.reload();", 2000);
        </script>
        <?php
        }
    }

    /**
     * savewishlist function
     *
     * @access public
     * @return void
     */
    function savewishlist()
    {
        global $mainframe;
        $cid    = JRequest :: getInt('cid');
        $model  = & $this->getModel("wishlist");
        $option = JRequest :: getVar('option');
        if ($model->savewishlist())
        {
            echo "<div>" . JText::_('COM_REDSHOP_PRODUCT_SAVED_IN_WISHLIST_SUCCESSFULLY') . "</div>";
        }
        else
        {
            echo "<div>" . JText::_('COM_REDSHOP_PRODUCT_NOT_SAVED_IN_WISHLIST') . "</div>";
        }

        ?>
    <script language="javascript">
        var t = setTimeout("window.parent.SqueezeBox.close();window.parent.location.reload()", 2000);
    </script>
    <?php
    }

    /**
     * delete wishlist function
     *
     * @access public
     * @return void
     */
    function delwishlist()
    {
        global $mainframe;

        $user  = &JFactory::getUser();
        $post  = JRequest :: get('post');
        $model = & $this->getModel("wishlist");

        $Itemid = JRequest::getVar('Itemid');

        $option = JRequest::getVar('option');

        $post = JRequest::get('request');

        if ($model->check_user_wishlist_authority($user->id, $post["wishlist_id"]))
        {
            if ($model->delwishlist($user->id, $post["wishlist_id"]))
            {
                $msg = JText::_('COM_REDSHOP_WISHLIST_DELETED_SUCCESSFULLY');
            }
            else
            {
                $msg = JText::_('COM_REDSHOP_ERROR_IN_DELETING_WISHLIST');
            }
        }
        else
        {
            $msg = JText::_('COM_REDSHOP_YOU_ARE_NOT_AUTHORIZE_TO_DELETE');
        }

        $link = JRoute::_("index.php?option=" . $option . "&view=wishlist&task=viewwishlist&Itemid=" . $Itemid, false);
        ;

        $mainframe->redirect($link, $msg);
    }

    function mysessdelwishlist()
    {
        $post                = array();
        $post['wishlist_id'] = JRequest :: getVar('wishlist_id');
        $mydel               = JRequest :: getVar('mydel');
        $model               = & $this->getModel("wishlist");
        $option              = JRequest::getVar('option');
        $Itemid              = JRequest::getVar('Itemid');

        if ($mydel != '')
        {
            if ($model->mysessdelwishlist($post["wishlist_id"]))
            {
                $msg = JText::_('COM_REDSHOP_WISHLIST_DELETED_SUCCESSFULLY');
            }

            $link = JRoute::_("index.php?mydel=1&option=" . $option . "&view=wishlist&task=viewwishlist&Itemid=" . $Itemid, false);
            $this->setRedirect($link, $msg);
        }
    }
}
