<?php

namespace Lancms\Kiosk\Payment;

use Lancms\Kiosk\Api\PaymentMethod;

class Credit implements PaymentMethod
{

    /**
     * Provides a unique name for this payment method.
     * 
     * @return string
     */
    public function getUniqueName()
    {
        return "credit";
    }

    /**
     * Provides a display name for this payment method.
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return _("Crew credit");
    }

    /**
     * Called when this payment method is used.
     * 
     * @return PurchaseResult Return a result object.
     */
    public function purchase(KioskSession $session, \User $user)
    {
        
    }

}
