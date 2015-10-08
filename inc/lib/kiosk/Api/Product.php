<?php

namespace Lancms\Kiosk\Api;

/**
 * Interface for a product in the kiosk system
 * 
 * @author edvin
 * @package kiosk.api
 */
interface Product
{

    /**
     * Provides the ID of this single product.
     *
     * @return int
     */
    public function getProductID();
    
    /**
     * Provides the price for this single product.
     * 
     * @return float
     */
    public function getPrice();
    
    /**
     * Provides the name for this product.
     * 
     * @return string
     */
    public function getName();

}
