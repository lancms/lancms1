<?php

namespace Lancms\Kiosk\Api;

/**
 * Represents a payment method.
 * 
 * @author edvin
 * @package kiosk.api
 */
interface PaymentMethod
{

    /**
     * Provides a unique name for this payment method.
     * 
     * @return string
     */
    public function getUniqueName();

    /**
     * Provides a display name for this payment method.
     * 
     * @return string
     */
    public function getDisplayName();

    /**
     * Called when this payment method is used.
     * 
     * @return PurchaseResult Return a result object.
     */
    public function purchase(KioskSession $session, \User $user);

}
