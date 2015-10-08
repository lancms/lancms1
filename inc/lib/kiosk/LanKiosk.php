<?php

namespace Lancms\Kiosk;

use Lancms\Kiosk\Api\Kiosk;

/**
 * Class LanKiosk
 * 
 * @author edvin
 * @package kiosk
 */
class LanKiosk implements Kiosk
{

    /**
     * Provides the name of this kiosk.
     * 
     * @return string
     */
    public function getName()
    {
        return "Lan Kiosk";
    }

    /**
     * Indicates if this kiosk is open.
     * 
     * @return bool
     */
    public function isOpen()
    {
        return true;
    }

    /**
     * Indicates if this kiosk allows to add to a credit.
     * 
     * @return bool
     */
    public function allowCredit()
    {
        return true;
    }

    /**
     * @param $productID
     * @return LanProduct|null
     */
    public function getProductByID($productID)
    {
        if (!is_numeric($productID) || $productID < 1)
            return null;

        $query = db_query(sprintf("SELECT `ID`,`wareType`,`name`,`price` FROM `%s_kiosk_wares` WHERE `ID` = %d LIMIT 0,1", db_prefix(), $productID));
        if (db_num($query) < 1) {
            return null;
        }

        $result = db_fetch_assoc($query);
        $product = new LanProduct($productID);
        $product->fillInfo($result);
        return $product;
    }

}
