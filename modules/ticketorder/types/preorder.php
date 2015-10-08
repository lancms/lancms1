<?php

$content .= "

    <div id=\"payment-form\" class='payment clear overhidden'>
        <h3>Hvordan ønsker du å betale?</h3>
        <p>Pris <strong>" . number_format($priceNormal) . " kr</strong> for <strong>" . $amount . "x " . $ticketType->getName() . "</strong></p>
        <div class=\"payment-options table no-colour\">
            <div class=\"payment-option row\">
                <div class=\"payment-radio cell\">
                    <input type=\"radio\" name=\"pay_method_bla\" id=\"payment-1\" value=\"stripe\" />
                </div>
                <div class=\"payment-info cell\">
                    <label for=\"payment-1\">
                        <div class='name'>" . _("Betal med kort") . "</div>
                        <div class='description'>" . _("Med dette valget betaler du for billett(ene) nå og får velge plass på alle.") . "</div>
                    </label>
                </div>
            </div>
            <div class=\"payment-option row\">
                <div class=\"payment-radio cell\">
                    <input type=\"radio\" name=\"pay_method_bla\" id=\"payment-2\" value=\"door\" />
                </div>
                <div class=\"payment-info cell\">
                    <label for=\"payment-2\">
                        <div class='name'>" . _("Betal i døra") . "</div>
                        <div class='description'>" . _("Med dette valget betaler du når du ankommer i døra til GlobeLAN 25. Du kan kun reservere en plass her å nå.") . "</div>
                    </label>
                </div>
            </div>
        </div>
        <div class=\"pull-right submit-button\">
            <button type=\"button\" class=\"btn-grey\" onclick=\"window.location = '?module=ticketorder';\">Avbryt</button>
            <input type=\"button\" class=\"btn-small\" name=\"select\" onclick=\"return handlePaymentSubmit();\" value=\"" . _("Gå videre") . "\" />
        </div>
    </div>

    <div class=\"ticket pay-stripe gone clear overhidden\">
        <h3>" . _("Betal med kort") . "</h3>
        <p>Trykk på den blå knappen for å starte betalingen</p>
        <form action=\"?module=ticketorder&action=handleOrderTicket\" method=\"post\" id=\"payment-chooser-form2\">
            <input type=\"hidden\" name=\"pay_method\" value=\"stripe\" />
            <input type=\"hidden\" name=\"ttID\" value=\"" . $ticketType->getTicketTypeID() . "\" />

            <div class=\"purchase-button\">
                <script
                    src=\"https://checkout.stripe.com/checkout.js\" class=\"stripe-button\"
                    data-key=\"" . $stripePaymentConfig["privateKey"] . "\"
                    data-amount=\"$price\"
                    data-name=\"" . $stripePaymentConfig["companyName"] . "\"
                    data-description=\"" . $ticketType->getName() . " (" . $priceNormal . " NOK)\"
                    data-currency=\"NOK\"
                    data-image=\"" . $stripePaymentConfig["imageLogo"] . "\">
                </script>
            </div>
            <div class=\"actions\" style=\"text-align:center;\">
                <button type=\"button\" class=\"btn-grey btn-small\" onclick=\"cancelPayment();\">Avbryt</button>
            </div>
        </form>
    </div>

    <div class=\"ticket pay-door gone clear overhidden\">
        <h3>" . _("Betal i døra") . "</h3>
        <p>Ved å betale i døra betaler du når du ankommer GlobeLAN og får kun reservere en plass med en billett.</p>

        <div class=\"actions pull-right\">
        <form action=\"?module=ticketorder&action=handleOrderTicket\" method=\"post\" id=\"payment-chooser-form1\">
            <input type=\"hidden\" name=\"pay_method\" value=\"door\" />
            <input type=\"hidden\" name=\"ttID\" value=\"" . $ticketType->getTicketTypeID() . "\" />
            
            <button type=\"button\" class=\"btn-grey btn-small\" onclick=\"cancelPayment();\">Avbryt</button>
            <button type=\"submit\" class=\"btn-green btn-small\">Fullfør</button>
        </form>
        </div>
    </div>

<div class=\"disclaimer\">
    Angrerettsloven gjelder ikke ved kjøp via GlobeLAN PartySys. Lovens kapittel fem paragraf 19 omhandler begrensninger i loven:

    <em>«Ved annet fjernsalg gjelder angreretten ikke for enkeltstående tjenester dersom selgeren ved avtaleinngåelsen forplikter seg til å levere tjenesten på et bestemt tidspunkt eller inennfor et bestemt tidsrom»</em>

    Enkeltstående tjenester har blitt tolket av myndighetene til å gjelde for eksempel underholdningsarrangement som teater, kino, konsert og datatreff.
</div>";

$content .= "<script src=\"/templates/ontime/js/payment.js\"></script>";