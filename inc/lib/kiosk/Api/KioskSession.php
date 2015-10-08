<?php

namespace Lancms\Kiosk\Api;

/**
 * Interface KioskSession represents a session of a single "trade" where one customer has one set of products resulting in a total price.
 * The sessions should be stored in a database so they are traceable later.
 * 
 * @author edvin
 * @package kiosk.api
 */
interface KioskSession
{

    /**
     * Provides the Kiosk this session belongs to.
     * 
     * @return Kiosk
     */
    public function getKiosk();

    /**
     * Provides all the products in this session in an array.
     * 
     * @return Product[]
     */
    public function getProducts();

    /**
     * Indicates if there are any products in this session.
     * 
     * @return bool
     */
    public function hasProducts();

    /**
     * Adds a new product to this session.
     * 
     * @param Product $product The product being added.
     * @param int $amount Amount of this product.
     * @return bool The result, if added true is returned.
     */
    public function addProduct(Product $product, $amount = 0);

    /**
     * Removes a product from this session, will just use "===" on each product in this session if any.
     * 
     * @param Product $product The product being removed.
     * @return bool The result, if removed true is returned.
     */
    public function removeProduct(Product $product);

    /**
     * Provides the total price of each product with amount as a float.
     * 
     * @return float
     */
    public function getTotalPrice();

    /**
     * End this session. Payment should have happend before calling it.
     * 
     * @return bool The result
     */
    public function end();

}
