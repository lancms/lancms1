<?php

$content .= "

    <div class=\"ticket pay-stripe clear overhidden\">
        <h3>" . _("Betal med kort") . "</h3>
        <p>Trykk på den blå knappen for å starte betalingen</p>
        <form action=\"?module=usertickets&action=handlePayTicket\" method=\"post\" id=\"payment-chooser-form2\">
            <input type=\"hidden\" name=\"pay_method\" value=\"stripe\" />
            <input type=\"hidden\" name=\"ticketID\" value=\"" . $ticket->getTicketID() . "\" />

            <div class=\"purchase-button\">
                <script
                    src=\"https://checkout.stripe.com/checkout.js\" class=\"stripe-button\"
                    data-key=\"" . $stripePaymentConfig["privateKey"] . "\"
                    data-amount=\"" . $ticket->getTicketType()->getPrice() . "00\"
                    data-name=\"" . $stripePaymentConfig["companyName"] . "\"
                    data-description=\"" . $ticket->getTicketType()->getName() . " (" . $ticket->getTicketType()->getPrice() . " NOK)\"
                    data-currency=\"NOK\"
                    data-image=\"" . $stripePaymentConfig["imageLogo"] . "\">
                </script>
            </div>
            <div class=\"actions\" style=\"text-align:center;\">
                <button type=\"button\" class=\"btn-grey btn-small\" onclick=\"window.location = 'index.php?module=usertickets';\">Avbryt</button>
            </div>
        </form>
    </div>

<div class=\"disclaimer\">
    Angrerettsloven gjelder ikke ved kjøp via GlobeLAN PartySys. Lovens kapittel fem paragraf 19 omhandler begrensninger i loven:

    <em>«Ved annet fjernsalg gjelder angreretten ikke for enkeltstående tjenester dersom selgeren ved avtaleinngåelsen forplikter seg til å levere tjenesten på et bestemt tidspunkt eller inennfor et bestemt tidsrom»</em>

    Enkeltstående tjenester har blitt tolket av myndighetene til å gjelde for eksempel underholdningsarrangement som teater, kino, konsert og datatreff.
</div>";
