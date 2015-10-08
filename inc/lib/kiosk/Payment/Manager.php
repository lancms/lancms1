<?php

namespace Lancms\Kiosk\Payment;

use Lancms\Kiosk\Api\PaymentMethod;
use Lancms\Kiosk\Api\KioskSession;

class Manager
{
    
    protected $_paymentMethods;

    public function __construct()
    {
        // no-op
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function addPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->_paymentMethods[$paymentMethod->getUniqueName()] = $paymentMethod;
    }

    /**
     * @return PaymentMethod|null
     */
    public function getPurchaseMethod($uniqueName)
    {
        return (array_key_exists($uniqueName, $this->_paymentMethods) ? $this->_paymentMethods[$uniqueName] : null);
    }

}
