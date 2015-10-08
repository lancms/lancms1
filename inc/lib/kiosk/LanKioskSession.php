<?php

namespace Lancms\Kiosk;

use Lancms\Kiosk\Api\Kiosk;
use Lancms\Kiosk\Api\KioskSession;
use Lancms\Kiosk\Api\Product;

/**
 * Class LanKioskSession
 * 
 * @author edvin
 * @package kiosk
 */
class LanKioskSession implements KioskSession
{

    protected $_kiosk;
    protected $_products;
    protected $_kioskStaffUser;

    public function __construct(Kiosk $kiosk, \User $staffUser)
    {
        $this->_kiosk = $kiosk;
        $this->_kioskStaffUser = $staffUser;
        $this->_products = array();
    }

    public function __sleep()
    {
        return array("_kiosk", "_kioskStaffUser", "_products");
    }

    public function save()
    {
        $_SESSION[$this->_kioskStaffUser->getUserID() . "_kiosk"] = serialize($this);
    }

    public static function loadFromSession($staffUserID)
    {
        $key = $staffUserID . "_kiosk";
        if (!isset($_SESSION[$key])) {
            throw new \Exception("Not found in session.");
        }

        $kioskSession = unserialize($_SESSION[$key]);
        return $kioskSession;
    }

    /**
     * @param Kiosk $kiosk
     * @param \User $staffUser
     * @return LanKioskSession|mixed
     * @throws \Exception
     */
    public static function create(Kiosk $kiosk = null, \User $staffUser = null)
    {
        if ($kiosk === null) {
            $kiosk = new LanKiosk();
        }

        if ($staffUser === null) {
            $staffUser = \UserManager::getInstance()->getOnlineUser();
        }

        try {
            return self::loadFromSession($staffUser->getUserID());
        } catch (\Exception $e) {
            // no-op
        }

        return new self($kiosk, $staffUser);
    }

    /**
     * Provides the Kiosk this session belongs to.
     * 
     * @return Kiosk
     */
    public function getKiosk()
    {
        return $this->_kiosk;
    }

    /**
     * Provides all the products in this session in an array.
     * 
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Indicates if there are any products in this session.
     * 
     * @return bool
     */
    public function hasProducts()
    {
        return is_array($this->getProducts()) && count($this->getProducts()) > 0;
    }

    /**
     * Adds a new product to this session.
     * 
     * @param Product $product The product being added.
     * @param int $amount Amount of this product.
     * @return bool The result, if added true is returned.
     */
    public function addProduct(Product $product, $amount = 1)
    {
        if (isset($this->_products[$product->getProductID()])) {
            $this->_products[$product->getProductID()]["amount"]++;
        } else {
            $this->_products[$product->getProductID()] = array("object" => $product, "amount" => $amount);
        }
    }

    /**
     * Removes a product from this session
     * 
     * @param Product $product The product being removed.
     * @return bool The result, if removed true is returned.
     */
    public function removeProduct(Product $product)
    {
        // Make sure there are products in this session.
        if ($this->hasProducts()) {
            // Loop each product and check
            foreach ($this->_products as $key=>$thisProduct) {
                if ($thisProduct["object"]->getProductID() == $product->getProductID()) {
                    if ($thisProduct["amount"] > 1) {
                        $this->_products[$key]["amount"]--;
                    } else {
                        unset($this->_products[$key]);
                    }
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Provides the total price of each product with amount as a float.
     * 
     * @return float
     */
    public function getTotalPrice()
    {
        // Make sure there are products in this session.
        if (!$this->hasProducts())
            return 0.0;

        // Set initial price at 0.0.
        $price = 0.0;

        // Loop each product and add it to price.
        foreach ($this->getProducts() as $product) {
            $price += $product->getPrice();
        }

        // Return the price.
        return $price;
    }

    /**
     * End this session. Payment should have happend before calling it.
     * 
     * @return bool The result
     */
    public function end()
    {
        $_SESSION[$this->_kioskStaffUser->getUserID() . "_kiosk"] = null;
    }

}
