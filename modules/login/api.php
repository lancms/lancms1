<?php

if (!isset($apiAuthenticationApps)) {
    $apiAuthenticationApps = [];
}

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

if (!$request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
    http_response_code(405);
    exit(0);
}

$appId = $request->headers->get('X-Lancms-App-ID', '');
if (empty($appId)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'cause' => 'invalid_app1']);
    exit(0);
}

if (!isset($apiAuthenticationApps[$appId])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'cause' => 'invalid_app2']);
    exit(0);
}

// validate the secret key
$appSecretKey = $apiAuthenticationApps[$appId]['secret_key'];
$providedSecretKey = $request->headers->get('X-Lancms-App-Secret', '');
if (empty($appSecretKey) || empty($providedSecretKey) || $appSecretKey !== $providedSecretKey) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'cause' => 'invalid_sec']);
    exit(0);
}

$allowedEventIds = $apiAuthenticationApps[$appId]['eventIDs'];
$eventID = (int) $request->headers->get('X-Lancms-EventID');

if (!in_array($eventID, $allowedEventIds, true)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'cause' => 'invalid_event']);
    exit(0);
}

$statusCode = 200;
$responseContent = [];

$username = (string) $request->request->get('username');
$password = (string) $request->request->get('password');

if (!empty($username) && !empty($password)) {
    $user = UserManager::getInstance()->getUserByNick($username);

    if ($user instanceof User && md5($password) === $user->getPassword() && $user->isEmailConfirmed()) {
        $userTickets = TicketManager::getInstance()->getTicketsOfUser($user->getUserID(), $eventID);
        $ticketInfo = [];

        foreach ($userTickets as $ticket) {
            $seat = $ticket->getSeat();

            $ticketInfo[$ticket->getTicketID()] = [
                'id' => $ticket->getTicketID(),
                'eventID' => $ticket->getEventID(),
                'typeID' => $ticket->getTicketTypeID(),
                'name' => $ticket->getTicketType()->getName(),
                'isPaid' => $ticket->isPaid(),
                'hasArrived' => $ticket->hasArrived(),
                'status' => $ticket->getStatus(),
                'seat' => $seat ? [
                    'x' => $seat->getSeatX(),
                    'y' => $seat->getSeatY(),
                ] : null,
            ];
        }

        $responseContent['status'] = 'success';
        $responseContent['user'] = [
            'id' => $user->getUserID(),
            'nick' => $user->getNick(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'isCrew' => $user->isCrew($eventID),
            'eventTickets' => $ticketInfo,
        ];
        $statusCode = 200;
    } else {
        $responseContent = ['status' => 'error', 'cause' => 'invalid_credentials'];
    }
} else {
    $responseContent = ['status' => 'error', 'cause' => 'missing_username_or_password'];
}

$encoded = json_encode($responseContent);
header('Content-type: application/json');
header('Content-length: ' . strlen($encoded));
http_response_code($statusCode);
echo $encoded;
exit(0);
