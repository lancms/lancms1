<?php

$content .= "
            <div class=\"ticket pay-door clear overhidden\">
                <h3>" . _("Betal i døra") . "</h3>
                <p>Ved å betale i døra betaler du når du ankommer GlobeLAN og får kun reservere en plass med en billett.</p>

                <div class=\"actions pull-right\">
                    <button type=\"button\" class=\"btn-grey btn-small\" onclick=\"window.location = '?module=ticketorder';\">Avbryt</button>
                    <button type=\"button\" class=\"btn-green btn-small\" onclick=\"doPayment(\"door\");\">Fullfør</button>
                </div>
            </div>

            <div class=\"disclaimer\">
                Angrerettsloven gjelder ikke ved kjøp via GlobeLAN PartySys. Lovens kapittel fem paragraf 19 omhandler begrensninger i loven:

                <em>«Ved annet fjernsalg gjelder angreretten ikke for enkeltstående tjenester dersom selgeren ved avtaleinngåelsen forplikter seg til å levere tjenesten på et bestemt tidspunkt eller inennfor et bestemt tidsrom»</em>

                Enkeltstående tjenester har blitt tolket av myndighetene til å gjelde for eksempel underholdningsarrangement som teater, kino, konsert og datatreff.
            </div>";

            $content .= "<script src=\"/templates/ontime/js/payment.js\"></script>";
