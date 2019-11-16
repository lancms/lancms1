<?php

/**
 * Arrival module, this is a rewrite of the old module.
 */

use \Symfony\Component\HttpFoundation\Request;

$ticketManager = TicketManager::getInstance();
$usertable = $sql_prefix . "_users";
$ticketstable = $sql_prefix."_tickets";
$tickettypestable = $sql_prefix."_ticketTypes";

$thisModule = "arrival";
$action = isset($_GET['action']) ? $_GET['action'] : null;

$acl_ticket = acl_access("ticketadmin", "", $sessioninfo->eventID);
$acl_seating = acl_access("seating", "", $sessioninfo->eventID);

// Check if user has permission to tickets
if ($acl_ticket == 'No')
    printNoAccessError();

$request = Request::createFromGlobals();
$requestGet = $request->query;
$requestPost = $request->request;

$content .= "
        <h1 class=\"page-title\">" . _("Arrival") . "</h1>
        <div class=\"arrival\">";

switch ($action) {

    //==================================================================================
    // Ticket detail display
    case "changeuser":

        $ticketID = $requestGet->has("ticket") ? $requestGet->get("ticket") : null;
        if (strlen(trim($ticketID)) < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = $ticketManager->getTicketByMD5($ticketID);
            $changeType = (isset($_GET['type']) ? $_GET['type'] : 'user');

            /* HANDLERS */
            if (isset($_GET['set']) && is_numeric($_GET['set']) == true && intval($_GET['set']) > 0) {
                $ret = "index.php?module=$thisModule&action=ticketdetail&ticket=" . $ticketID . "&changed=" . $changeType;

                switch ($changeType) {
                    case "user":
                        $ticket->setUser($_GET['set']);
                        break;

                    case "owner":
                        $ticket->setOwner($_GET['set']);
                        break;

                    default: break;
                }

                $ticket->commitChanges();

                header("Location: $ret");
                die();
            }
            /* VIEW */

            if ($changeType == 'user') {
                $content .= "<p>" . _("Changing user on this ticket, search after a user and then click the name.") . "</p>";
            } else {
                $content .= "<p>" . _("Changing owner on this ticket, search after a user and then click the name.") . "</p>";
            }

            $userQueryValue = "";
            if (isset($_POST['query'])) {
                $userQueryValue = htmlspecialchars($_POST['query']);
            }

            $content .= '
            <div class="searchfield">
                <form class="normal inline" action="index.php?module=' . $thisModule . '&action=changeuser&type=' . $changeType . '&ticket=' . $ticketID . '" method="post">
                    <div class="form-group">
                        <input type="text" id="query-arrival" name="query" value="' . $userQueryValue . '" />
                        <input type="submit" class="btn" name="doSearch" value="' . _("Search") . '" />
                    </div>
                </form>
            </div>';

            $searchString = db_escape($_POST['query']);
            if (strlen($_POST['query']) > 0) {
                $str = db_escape(htmlspecialchars($_POST['query']));

                $result = UserManager::getInstance()->searchUsers($str);

                if (count($result) > 0) {
                    $content .= "<table class=\"table ticket-table\"><thead><tr><th>" . _("Name") . "</th></tr></thead><tbody>";
                    foreach ($result as $key => $value) {
                        $name = $value->firstName . ' ' . $value->lastName . ' (' . $value->nick . ')';
                        $content .= "
                            <tr><td>
                                <a href=\"index.php?module=$thisModule&amp;action=changeuser&amp;type=$changeType&amp;set=" . $value->ID . "&amp;ticket=$ticketID\">$name</a>
                            </td></tr>";
                    }
                    $content .= "</tbody></table>";
                } else {
                    $content .= "<div class=\"empty-text\">" . _("No users found") . "</div>";
                }
            }
        }

        break;

    //==================================================================================
    // Ticket detail display
    case "ticketdetail":

        $ticketID = $requestGet->has("ticket") ? $requestGet->get("ticket") : null;
        if (strlen(trim($ticketID)) < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = $ticketManager->getTicketsByMD5(array($ticketID));
            if (count($ticket) < 1) {
                $content .= "<p>Fant ikke billetten.</p>";
                die();
            }

            $ticket = $ticket[0];

            /* HANDLERS */
            if ($requestGet->get("handle")) {
                $ret = "index.php?module=$thisModule&action=ticketdetail&ticket=" . $ticketID;

                switch ($requestGet->get('handle')) {
                    case "markpaid":
                        $ticket->setPaid();
                        $ret .= "&setaspaid=true";
                        break;

                    case "markunpaid":
                        $ticket->setUnPaid();
                        $ret .= "&setasunpaid=true";
                        break;

                    case 'markarrived':
                        $ticket->setIsArrived();
                        $ticket->commitChanges();
                        $ret .= '&setasarrived=true';
                        break;

                    case "setused":
                        $ticket->setUsed();
                        $ret .= "&setasused=true";
                        break;

                    case "delete":
                        $ticket->setDeleted();
                        $ticket->removeSeat(); // Remove seat.
                        $ret .= "&deleted=true";
                        break;

                    default:
                        break;
                }

                header("Location: $ret");
                die();
            }

            /* VIEW */
            $owner = $ticket->getOwner();
            $user  = $ticket->getUser();

            // Ticket status
            switch ($ticket->getStatus()) {
                case Ticket::TICKET_STATUS_USED:
                    $status = _('Used');
                    break;

                default:
                    $status = _('Unused');
                    break;
            }

            $content .= "<div class=\"ticket-detail\"><div class=\"actions\">";

            // Used button
            if ($ticket->isPaid() && $ticket->getStatus() != Ticket::TICKET_STATUS_USED) {
                $markUsedButton = _("Mark used");

                $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=ticketdetail&amp;ticket=" . $ticketID . "&amp;handle=setused\">";
                $content .= "<input type=\"submit\" name=\"setaspaid\" class=\"btn-grey\" value=\"$markUsedButton\" />";
                $content .= "</form>";
            }

            // Paid button
            if (!$ticket->isPaid()) {
                $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=ticketdetail&amp;ticket=" . $ticketID . "&amp;handle=markpaid\">";
                $content .= "<input type=\"submit\" name=\"setaspaid\" class=\"btn-grey\" value=\"" . _("Mark paid") . "\" />";
                $content .= "</form>";
            }

            // Change owner
            $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=changeuser&amp;type=owner&amp;ticket=" . $ticketID . "\">";
            $content .= "<input type=\"submit\" name=\"changeowner\" class=\"btn-grey\" value=\"Change owner\" />";
            $content .= "</form>";

            // Change user
            $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=changeuser&amp;type=user&amp;ticket=" . $ticketID . "\">";
            $content .= "<input type=\"submit\" name=\"changeuser\" class=\"btn-grey\" value=\"Change user\" />";
            $content .= "</form>";

            // Change seating
            if ($acl_seating == 'Write' || $acl_seating == 'Admin') {
                $content .= "<form method=\"post\" action=\"index.php?module=seating&amp;ticketID=" . $ticket->getTicketID() . "\">";
                $content .= "<input type=\"submit\" name=\"changeseat\" class=\"btn-grey\" value=\"Change seat\" />";
                $content .= "</form>";
            }

            // Delete ticket
            if ($acl_ticket == 'Admin' && $ticket->getStatus() != Ticket::TICKET_STATUS_DELETED) {
                $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=$action&amp;ticket=" . $ticketID . "&amp;handle=delete\">";
                $content .= "<input type=\"submit\" name=\"deleteticket\" class=\"btn-red\" value=\"Delete\" />";
                $content .= "</form>";
            }

            $content .= "</div>";

            $isBirthdayValid = false;
            $isAddressValid = false;
            $birthday = null;
            $canMarkArrived = true;

            $street = trim($user->getStreetAddress());
            $postNumber = trim($user->getPostNumber());

            if ($street !== '' && strlen($postNumber) === 4) {
                $isAddressValid = true;
            }

            try {
                $birthYear = $user->getBirthYear();
                $birthMonth = $user->getBirthMonth();
                $birthDay = $user->getBirthDay();
                $asString = $birthYear . '-' . $birthMonth . '-' . $birthDay;

                $birthday = DateTimeImmutable::createFromFormat('Y-n-j', $asString);
                if ($birthday instanceof DateTimeImmutable && $birthday->format('Y-n-j') === $asString) {
                    $isBirthdayValid = true;
                }
            } catch (\Exception $e) {
                if ($e instanceof ErrorException) throw $e;
            }

            if (!$isBirthdayValid || !$isAddressValid) {
                $content .= '<div class="alert alert-danger">';
                $content .= '<div>There has been detected some issues with ';

                if (!$isBirthdayValid) {
                    $content .= '<strong>' . mb_strtolower(_('Birthday')) . '</strong>';

                    if (!$isAddressValid) {
                        $content .= ' and ';
                    }
                }
                if (!$isAddressValid) {
                    $content .= '<strong>' . mb_strtolower(_('Address')) . '</strong>';
                }

                $canMarkArrived = false;

                $content .= ' on the user of this ticket. </div>';
                $content .= '<div>Please correct this info by clicking the name of the user of this ticket.</div>';
                $content .= '</div>';
            }

            $today = new DateTimeImmutable();
            $legalAge = $today->sub(new DateInterval('P18Y'));
            if ($isBirthdayValid
                && $birthday instanceof DateTimeImmutable
                && $birthday > $legalAge
                && (trim($user->getGuardianName()) === ''
                || trim($user->getGuardianCellPhone()) === '')) {
                $content .= '<div class="alert alert-danger">';
                $content .= _('This person is not of age (18+) and has no guardian contact entered. This must be filled in before allowing arrival.');
                $content .= '</div>';
                $canMarkArrived = false;
            }

            $content .= '<style type="text/css">.info-row td{background-color: #d9edf7;} .invalid-row td{background-color:#f2dede;}</style>';

            if ($request->query->getBoolean('markedarrived')) {
                $content .= '<div class="alert alert-success">' . _('This ticket is now marked as arrived, ribbons can now be issued') . '</div>';
            }

            if ($ticket->getStatus() !== Ticket::TICKET_STATUS_ARRIVED) {
                $content .= '<div class="alert alert-info">
                    <div>Verify the address and birthday of this user before giving access to the event.</div>
                    <div>After the user information is verified, ensure to mark this ticket as used.</div>
                </div>';

                if ($canMarkArrived && $request->query->getBoolean('markarrived')) {
                    $ticket->setIsArrived();
                    $ticket->commitChanges();
                    log_add('arrival', 'mark_arrived');
                    header('Location: index.php?module=' . $thisModule . '&action=ticketdetail&ticket=' . $ticketID . '&markedarrived=true');
                    die();
                }

                $content .= '<div style="width:100%; margin:1rem 0; text-align: center">
                    <form action="index.php?module=' . $thisModule . '&amp;action=ticketdetail&amp;markarrived=true&amp;ticket=' . $ticketID . '" method="post">
                        <button type="submit" ' . (!$canMarkArrived ? 'disabled' : '') . ' class="btn btn-lg" style="padding:1.2rem; font-size:120%;">Mark as arrived</button>
                    </form>
                </div>';
            }

            $content .= "<div class=\"ticket-status\"><table class=\"table\">";
            $content .= "<tr><td><strong>" . _("ID") . "</strong></td><td>" . $ticket->getMd5ID() . "</td></tr>";

            $hasSeatString = _("No");
            if ($ticket->hasSeat() == true) {
                $hasSeatString = _("Yes");
                // get seat
                $seat = $ticket->getSeat();
                $hasSeatString .= ", <a href=\"?module=seating&ticketID=&seatX=" . $seat->getSeatX() . "&seatY=" . $seat->getSeatY() . "\">goto map</a>";
            }

            $content .= "<tr><td><strong>" . _("Seated") . "</strong></td><td>" . $hasSeatString . "</td></tr>";

            if ($ticket->getStatus() == Ticket::TICKET_STATUS_DELETED) {
                $content .= "<tr><td colspan=\"2\" class=\"deleted\">" . _("Ticket has been deleted") . "</td></tr>";
            } else {
                $content .= "<tr><td><strong>" . _("Status") . "</strong></td><td>" . $status . "</td></tr>";

                $content .= '<tr>';

                if ($ticket->hasArrived()) {
                    $content .= '<td colspan="2" class="arrived">' . _('Arrived') . '</td>';
                } else if ($ticket->isPaid()) {
                    $content .= '<td colspan="2" class="paid">' . _('Paid') . '</td>';
                } else {
                    $content .= '<td colspan="2" class="unpaid">' . _('Unpaid') . '</td>';
                }

                $content .= '</tr>';
            }

            $content .= "</table></div><div class=\"ticket-users\"><table class=\"table\">";

            // Owner information.
            $content .= "<tr><td class=\"head grey\" colspan=\"2\">" . _("Owner") . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Name") . "</strong></td><td><a href=\"index.php?module=profile&amp;arrivalref[ticket]=" . $ticketID . "&amp;user=" . $owner->getUserID() . "\">" . $owner->getFullName() . "</a></td></tr>";
            $content .= "<tr><td><strong>" . _("Nick") . "</strong></td><td>" . $owner->getNick() . "</td></tr>";
            if ($user->equals($owner) == false) {
                $content .= "<tr><td><strong>" . _("Email") . "</strong></td><td>" . $owner->getEmail() . "</td></tr>";
                $content .= "<tr><td><strong>" . _("Address") . "</strong></td><td>" . $owner->getStreetAddress() . "<br />" . $owner->getPostNumber() . " " . $owner->getPostPlace() . "</td></tr>";
                $content .= "<tr><td><strong>" . _("Birthday") . "</strong></td><td>" . date("d.M.Y", $owner->getBirthdayTimestamp()) . "</td></tr>";
            }

            // User information.
            $content .= "<tr><td class=\"head grey\" colspan=\"2\">" . _("User") . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Name") . "</strong></td><td><a href=\"index.php?module=profile&amp;arrivalref[ticket]=" . $ticketID . "&amp;user=" . $user->getUserID() . "\">" . $user->getFullName() . "</a></td></tr>";
            $content .= "<tr><td><strong>" . _("Nick") . "</strong></td><td>" . $user->getNick() . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Email") . "</strong></td><td>" . $user->getEmail() . "</td></tr>";
            $content .= "<tr class=\"" . (!$isAddressValid ? 'invalid-row' : 'info-row') . "\"><td><strong>" . _("Address") . "</strong></td><td>" . $user->getStreetAddress() . "<br />" . $user->getPostNumber() . " " . $user->getPostPlace() . "</td></tr>";
            $content .= "<tr class=\"" . (!$isBirthdayValid ? 'invalid-row' : 'info-row') . "\"><td><strong>" . _("Birthday") . "</strong></td><td>" . date("d.M.Y", $user->getBirthdayTimestamp()) . "</td></tr>";


            $content .= "</table></div></div>";
        }

        break;

    //==================================================================================
    // Add ticket on a user.
    case "addticket":
        // This page allows someone to add a ticket on an user.

        $userIds = ($request->request->has('userids') ? $request->request->get('userids') : array());
        $userIds = array_filter(
            array_map('trim', $userIds),
            function($item) {
                return is_numeric($item) && $item > 0;
            }
        );

        $ticketTypeIds = ($request->request->has('tickettypes') ? $request->request->get('tickettypes') : array());
        $ticketTypeIds = array_filter(
            array_map('trim', $ticketTypeIds),
            function($item) {
                return is_numeric($item) && $item > 0;
            }
        );

        $searchResultUsers = array();

        if (count($userIds) > 0) {
            $users = UserManager::getInstance()->getUsersByID($userIds);
            $dataForTwig = array();

            // Has selected ticket types?
            if (count($ticketTypeIds) > 0) {
                $ticketTypes = $ticketManager->getTicketTypes($ticketTypeIds);

                // Add the ticket types per user.
                foreach ($users as $user) {
                    $dataForTwig[$user->getUserID()] = array(
                        'name' => sprintf('%s (%s)', $user->getFullName(), $user->getNick()),
                        'tickets' => array(),
                    );

                    // Loop each ticket type.
                    foreach ($ticketTypes as $ticketType) {
                        // If we can validateAddTicketType, add it.
                        $dataForTwig[$user->getUserID()]['tickets'][] = array(
                            'result' => $user->validateAddTicketType($ticketType),
                            'name' => $ticketType->getName(),
                        );
                    }
                }

                $content .= $twigEnvironment->render(
                    'arrival/addticket_summary.twig',
                    array(
                        'module' => $thisModule,
                        'data' => $dataForTwig,
                    )
                );
            } else {
                // Has selected some users.
                // TODO: Show tickets.
                $ticketTypes = $ticketManager->getTicketTypes();

                $content .= $twigEnvironment->render(
                    'arrival/addticket_step2.twig',
                    array(
                        'module' => $thisModule,
                        'userIds' => $userIds,
                        'users' => $users,
                        'tickets' => $ticketTypes,
                    )
                );
            }
        } else {
            $query = ($request->request->has('query') ? $request->request->get('query') : null);
            $filterCrew = ($request->request->has('f_crew') ? $request->request->getBoolean('f_crew') : false);

            // Allow to search for everyone, so we can add crew ticket on everyone. :-D
            if (strlen($query) > 2) {
                $searchResultUsers = UserManager::getInstance()->searchUsers($query);
            } else if (( ! is_null($query)) && ($filterCrew)) {
                // Only get all if we want to filter by crew.
                $searchResultUsers = user_getall(array('ID', 'nick', 'firstName', 'lastName'));
            }

            // Should we filter by crew?
            if ($filterCrew) {
                $searchResultUsers = array_filter($searchResultUsers, function($user) {
                    return is_user_crew($user->ID);
                });
            }

            $content .= $twigEnvironment->render(
                'arrival/addticket_search.twig',
                array(
                    'module' => $thisModule,
                    'query' => $query,
                    'filterCrew' => $filterCrew,
                    'searchResult' => $searchResultUsers,
                )
            );
        }

        break;

    //==================================================================================
    // Search users
    default:

        $scopeSelected = 'search_tickets';
        if ($requestGet->has("scope") && $requestGet->getAlpha("scope") == 'all') {
            $scopeSelected = 'search_all';
        }

        $userQueryValue = "";
        if ($requestGet->has("query")) {
            $userQueryValue = htmlspecialchars($requestGet->get("query"));
        }

        $content .= '
        <div class="links"><a href="index.php?module='.$thisModule.'&amp;action=addticket">Legg til billett på bruker</a></div>
        <br />
        <p>' . _("Administer tickets on users.") . '</p>
        <div class="front">
            <form class="normal inline" action="index.php" method="get">
                <input type="hidden" name="module" value="' . $thisModule . '" />
                <input type="hidden" name="action" value="searchUser" />
                <div class="form-group">
                    <input type="text" id="query-arrival" name="query" value="' . $userQueryValue . '" />
                    <input type="submit" class="btn" name="doSearch" value="' . _("Search") . '" />
                </div>
                <div class="form-group">
                    <label for="search_tickets"><input type="radio" id="search_tickets" name="scope" value="tickets"' . ($scopeSelected == 'search_tickets' ? ' checked' : '') . ' /> ' . _("Search users with tickets") . '</label>
                </div>
                <div class="form-group">
                    <label for="search_all"><input type="radio" id="search_all" name="scope" value="all"' . ($scopeSelected == 'search_all' ? ' checked' : '') . ' /> ' . _("Search all users") . '</label>
                </div>
            </form>
        </div>';

        // Check if from has been submitted.
        if ($requestGet->has("doSearch")) {
            $scope = $requestGet->has("scope") ? $requestGet->get("scope") : 'tickets';

            $result = array();
            $resultCount = 0;

            // Verify there is a search query
            $searchString = db_escape($userQueryValue);
            if (strlen(trim($searchString)) > 0 || $scope == "tickets") {
                $query = null;

                if ($scope == 'all') {
                    $query = sprintf("SELECT nick, firstName, lastName as lastName, ID FROM %s WHERE
                        (nick LIKE '%%%s%%' OR
                        firstName LIKE '%%%s%%' OR
                        lastName LIKE '%%%s%%' OR
                        CONCAT(firstName, ' ', lastName) LIKE '%%%s%%' OR
                        EMail LIKE '%%%s%%'
                        ) ORDER BY ID", $usertable, $searchString, $searchString, $searchString, $searchString, $searchString);
                } else if ($scope == "tickets") {
                    $query = sprintf("SELECT DISTINCT u.nick as nick, u.firstName as firstName, u.lastName as lastName,
                        u.ID as ID FROM %s as u, %s as t WHERE t.eventID=%s AND t.user=u.ID AND
                        (u.nick LIKE '%%%s%%' OR
                        u.firstName LIKE '%%%s%%' OR
                        u.lastName LIKE '%%%s%%' OR
                        CONCAT(u.firstName, ' ', u.lastName) LIKE '%%%s%%' OR
                        EMail LIKE '%%%s%%'
                        ) ORDER BY u.ID", $usertable, $ticketstable, $sessioninfo->eventID, $searchString, $searchString, $searchString, $searchString, $searchString);
                }

                $result = db_query($query);
                $num    = db_num($result);

                $content .= "<table class=\"table ticket-table\"><thead><tr><th>" . _("Name") . "</th><th>" . _("Tickets") . "</th></tr></thead><tbody>";
                if ($num > 0) {
                    $i = 0;
                    while ($row = db_fetch($result)) {
                        $cssClass = ($i++ % 2 == 0 ? 'odd' : 'even');

                        $tickets = $ticketManager->getTicketsOfUser($row->ID, null);
                        $content .= "<tr class=\"$cssClass\"><td>" . $row->firstName . " " . $row->lastName . " (" . $row->nick . ")</td><td>";

                        // Output tickets
                        if (count($tickets) > 0) {
                            $content .= "<div class=\"tickets-list\">";
                            foreach ($tickets as $value) {
                                $extraCss = "";

                                if ($value->getStatus() == Ticket::TICKET_STATUS_DELETED) {
                                    $extraCss = " deleted";
                                } else if ($value->isPaid()) {
                                    $extraCss = " paid";
                                } else if ($value->isPaid() == false) {
                                    $extraCss = " unpaid";
                                }

                                $content .= "<div class=\"ticket-label$extraCss\">";
                                $content .= "<a href=\"index.php?module=arrival&amp;action=ticketdetail&amp;ticket=" . $value->getMd5ID() . "\">" . $value->getTicketType()->getName() . ' (' . ($value->getStatus() !== Ticket::TICKET_STATUS_ARRIVED ? 'Not arrived' : 'Arrived') . ')' . "</a>";
                                $content .= "</div>";
                            }
                            $content .= "</div>";
                        } else {
                            $content .= "<p><em>" . _("No tickets found") . "</em></p>";
                        }

                        $content .= "</td></tr>";
                    }
                }
                $content .= "</tbody></table>";
            }


        }

        break;
}

$content .= "</div>";
