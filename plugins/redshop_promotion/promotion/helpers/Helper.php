<?php
/**
 * @package     RedShop
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2020 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once ('Helper.php');

/**
 * Plugin Promotion Helper
 *
 * @since  __DEPLOY_VERSION__
 */
class Helper
{
    /**
     * @param $promotion
     * @param $cart
     * @return bool
     * @since  __DEPLOY_VERSION__
     */
    public static function getConditionProductAmount(&$promotion, &$cart) {
        $conditionAmount = false;
        $conditionManufacturer = true;
        $conditionCategory = true;
        $conditionProduct = true;
        $conditionTime = true;

        if (isset($promotion->product)) {
            $conditionProduct = false;
        }

        if (isset($promotion->manufacturer)) {
            $conditionManufacturer = false;
        }

        if (isset($promotion->category)) {
            $conditionCategory = false;
        }

        $count = 0;

        for ($i = 0; $i < $cart['idx']; $i++) {
            $product = \Redshop\Product\Product::getProductById($cart[$i]['product_id']);

            if (isset($promotion->product) && in_array($product->product_id, $promotion->product)) {
                $conditionProduct = true;
                $count += $cart[$i]['quantity'];
            }

            if (isset($promotion->category) && in_array($product->category_id, $promotion->category)) {
                $conditionCategory = true;
                $count += $cart[$i]['quantity'];
            }

            if (isset($promotion->manufacturer) && in_array($product->manufacturer_id, $promotion->manufacturer)) {
                $conditionManufacturer = true;
                $count += $cart[$i]['quantity'];
            }
        }

        if ($count >= $promotion->condition_amount) {
            $conditionAmount = true;
        }

        return $conditionAmount && $conditionProduct && $conditionManufacturer && $conditionCategory && $conditionTime;
    }

    /**
     * @param $promotion
     * @param $cart
     * @return |null
     * @since  __DEPLOY_VERSION__
     */
    public static function getConditionOrderVolume(&$promotion, &$cart) {
        $orderVolume = $promotion->order_volume ?? 0;
        $condition = false;

        if ($orderVolume > 0) {
            $operand = '<=';

            switch (trim($operand)) {
                case '<=':
                    $condition = $orderVolume <= Helper::getCartSubTotalExcludeVAT($cart);
                    break;
                default:
                    break;
            }
        }

        return $condition;
    }

    /**
     * @param $cart
     * @return float
     * @since  __DEPLOY_VERSION__
     */
    public static function getCartSubTotalExcludeVAT(&$cart) {
        return $cart['product_subtotal_excl_vat'] ?? 0.0;
    }

    /**
     * @param $promotion
     * @return bool
     * @since  __DEPLOY_VERSION__
     */
    public static function isPromotionApplied(&$promotion) {
        return $promotion->isApplied ?? false;
    }
}