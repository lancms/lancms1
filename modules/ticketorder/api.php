<?php

if ($action !== 'handleStripeCheckout') {
    http_response_code(404);
    exit(0);
}

$eventID = $sessioninfo->eventID;
$ticketManager = TicketManager::getInstance();

if(!config("enable_ticketorder", $eventID)) {
    http_response_code(404);
    exit(0);
}

// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey($stripePaymentConfig['privateKey']);

// You can find your endpoint's secret in your webhook settings
$endpoint_secret = $stripePaymentConfig['webhookSecret'];

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Error\SignatureVerification $e) {
    // Invalid signature
    http_response_code(400);
    exit(0);
}

// Handle the checkout.session.completed event
if ($event->type === 'checkout.session.completed') {
    $stripeCreatedTimestamp = $event->data->created;
    $session = $event->data->object;
    $now = new DateTimeImmutable();

    $ticketTypes = [];
    $userIDs = [];
    $ticketIDs = [];

    $tickets = $ticketManager->getTicketsByOrderReference($session->id);
    if (count($tickets) > 0) {
        foreach ($tickets as $ticket) {
            /** @var Ticket $ticket */
            if (!$ticket->isPaid()) {
                $ticket->setPaid();
                $ticket->setPaidTime($now);
                $ticket->commitChanges();

                $ticketIDs[] = $ticket->getTicketID();
                $userIDs[] = $ticket->getUserID();
                $ticketType = $ticket->getTicketType();
                $ticketTypes[$ticketType->getTicketTypeID()] = $ticketType;
            }
        }
    }

    $userIDs = array_unique($userIDs, SORT_NUMERIC);

    if (count($ticketTypes) !== 1 || count($userIDs) !== 1) {
        http_response_code(401);
        exit(0);
    }

    $userID = reset($userIDs);

    /** @var TicketType $ticketType */
    $ticketType = reset($ticketTypes);

    $amount = count($tickets);
    $price = floor($ticketType->getPrice() * $amount);

    // Log this
    TicketManager::getInstance()->logTicketPurchase(
        $userID,
        $eventID,
        (count($ticketIDs) > 0 ? implode(", ", $ticketIDs) : ""),
        $now->getTimestamp(),
        $stripeCreatedTimestamp,
        $session->id,
        Ticket::TICKET_STATUS_PAID,
        $ticketType,
        $price,
        $amount
    );

    $glLog = array(
        "userID" => $userID,
        "eventID" => $eventID,
        "ticketIDs" => (count($ticketIDs) > 0 ? implode(", ", $ticketIDs) : ""),
        "time" => $now->getTimestamp(),
        "stripeTime" => $stripeCreatedTimestamp,
        "stripeRef" => $session->id,
        "status" => Ticket::TICKET_STATUS_PAID,
        "ticketType" => $ticketType,
        "price" => $price,
        "amount" => $amount
    );

    log_add("ticketorder", "handleticketpurchase", serialize($glLog));
}

http_response_code(200);
