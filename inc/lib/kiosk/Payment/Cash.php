<?php

namespace Lancms\Kiosk\Payment;

use Lancms\Kiosk\Api\PaymentMethod;

class Cash implements PaymentMethod
{

    /**
     * Provides a unique name for this payment method.
     * 
     * @return string
     */
    public function getUniqueName()
    {
        return "cash";
    }

    /**
     * Provides a display name for this payment method.
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return _("Cash payment");
    }

    /**
     * Called when this payment method is used.
     * 
     * @return PurchaseResult Return a result object.
     */
    public function purchase(KioskSession $session, \User $user)
    {
        // Log this purchase.
    }

}
