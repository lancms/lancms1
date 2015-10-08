<?php

namespace Lancms\Kiosk;

use Lancms\Kiosk\Api\Product;

class LanProduct extends \SqlObject implements Product
{

    public function __construct($id)
    {
        parent::__construct("kiosk_wares", "ID", $id);
    }

    /**
     * Provides the ID of this single product.
     *
     * @return int
     */
    public function getProductID()
    {
        return $this->getObjectID();
    }
    
    /**
     * Provides the price for this single product.
     * 
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->_getField("price", 0, 2);
    }
    
    /**
     * Provides the name for this product.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_getField("name");
    }

    public function getWareType()
    {
        return $this->_getField("wareType");
    }
    
}
