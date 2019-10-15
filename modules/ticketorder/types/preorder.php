<?php

if (isset($_POST['select'], $_POST['pay_method_bla'])) {
    switch ($_POST['pay_method_bla']) {
        case 'stripe':
            $schemeAndHost = $request->getSchemeAndHttpHost();

            \Stripe\Stripe::setApiKey($stripePaymentConfig["secretKey"]);
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => $ticketType->getName(),
                    'description' => $ticketType->getDescription(),
                    'amount' => $ticketType->getPrice() . "00",
                    'currency' => 'nok',
                    'quantity' => $amount,
                ]],
                'client_reference_id' => base64_encode($user->getUserID() . '|' . $ticketType->getTicketTypeID() . '|' . $amount),
                'success_url' => $schemeAndHost . '/?module=ticketorder&action=handleStripeSuccess&ttID=' . $ticketType->getTicketTypeID() . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $schemeAndHost . '/?module=ticketorder&action=cancel',
            ]);

            if (empty($session->id)) {
                throw new RuntimeException('Missing session ID from stripe.');
            }

        	// create pending tickets for this intent.
        	$newTicketMd5s = $user->validateAddTicketType($ticketType, $amount);
        	$newTickets = $ticketManager->getTicketsByMD5($newTicketMd5s);

        	foreach ($newTickets as $newTicket) {
        		// connect these tickets with the current stripe session.
        		$newTicket->setOrderReference($session->id);
        		$newTicket->commitChanges();
        	}

            $content .= $twigEnvironment->render('ticketorder/preorder-stripe.twig', [
        		'ticketType' => $ticketType,
        		'checkoutSessionId' => $session->id,
        		'stripePaymentConfigPrivateKey' => $stripePaymentConfig['privateKey'],
        	]);
            return;

        case 'door':
            $content .= $twigEnvironment->render('ticketorder/preorder-door.twig', [
        		'ticketType' => $ticketType,
        	]);
            return;

        default:
            break;
    }
}

$content .= $twigEnvironment->render('ticketorder/preorder.twig', [
    'priceNormal' => $priceNormal,
    'amount' => $amount,
    'ticketType' => $ticketType,
    'ticketOrderAllowPreorderPayOnArrival' => $ticketOrderAllowPreorderPayOnArrival,
]);

$content .= <<<END
<div class="disclaimer">
    Angrerettsloven gjelder ikke ved kjøp via GlobeLAN PartySys. Lovens kapittel fem paragraf 19 omhandler begrensninger i loven:
    <em>«Ved annet fjernsalg gjelder angreretten ikke for enkeltstående tjenester dersom selgeren ved avtaleinngåelsen forplikter seg til å levere tjenesten på et bestemt tidspunkt eller inennfor et bestemt tidsrom»</em>
    Enkeltstående tjenester har blitt tolket av myndighetene til å gjelde for eksempel underholdningsarrangement som teater, kino, konsert og datatreff.
</div>
END;
