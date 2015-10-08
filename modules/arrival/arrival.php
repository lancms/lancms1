<?php

/**
 * Arrival module, this is a rewrite of the old module.
 */

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

$content .= "
        <h1 class=\"page-title\">" . _("Arrival") . "</h1>
        <div class=\"arrival\">";

switch ($action) {

    //==================================================================================
    // Ticket detail display
    case "changeuser":

        $ticketID = isset($_GET['ticket']) && is_numeric($_GET['ticket']) ? intval($_GET['ticket']) : -1;
        if ($ticketID < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = $ticketManager->getTicket($ticketID);
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
                        <input type="submit" name="doSearch" value="' . _("Search") . '" />
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

        if (!$requestGet->has("ticket")) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = $ticketManager->getTicketsByMD5(array($requestGet->get("ticket")));
            if (count($ticket) < 1) {
                $content .= "<p>Fant ikke billetten.</p>";
                die();
            }

            $ticket = $ticket[0];

            /* HANDLERS */
            if (isset($_GET['handle'])) {
                $ret = "index.php?module=$thisModule&action=ticketdetail&ticket=" . $ticketID;

                switch ($_GET['handle']) {
                    case "markpaid":
                        $ticket->setPaid();
                        $ret .= "&setaspaid=true";
                        break;

                    case "markunpaid":
                        $ticket->setUnPaid();
                        $ret .= "&setasunpaid=true";
                        break;

                    case "setused":
                        $ticket->setUsed();
                        $ret .= "&setasused=true";
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
            $status = _("Unused");
            if ($ticket->getStatus() == Ticket::TICKET_STATUS_USED) {
                $status = _("Used");
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
            $paidHandle = "markpaid";
            $paidHandleButton = _("Mark paid");
            if ($ticket->isPaid()) {
                $paidHandle = "markunpaid";
                $paidHandleButton = _("Mark unpaid");
            }

            $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=ticketdetail&amp;ticket=" . $ticketID . "&amp;handle=$paidHandle\">";
            $content .= "<input type=\"submit\" name=\"setaspaid\" class=\"btn-grey\" value=\"$paidHandleButton\" />";
            $content .= "</form>";

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
                $content .= "<form method=\"post\" action=\"index.php?module=seating&amp;ticket=" . $ticketID . "\">";
                $content .= "<input type=\"submit\" name=\"changeseat\" class=\"btn-grey\" value=\"Change seat\" />";
                $content .= "</form>";
            }

            // Delete ticket
            if ($acl_ticket == 'Admin') {
                $content .= "<form method=\"post\" action=\"index.php?module=$thisModule&amp;action=delete&amp;ticket=" . $ticketID . "\">";
                $content .= "<input type=\"submit\" name=\"deleteticket\" class=\"btn-red\" value=\"Delete\" />";
                $content .= "</form>";
            }

            $content .= "</div><div class=\"ticket-status\"><table class=\"table\">";
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
                $content .= "<tr><td colspan=\"2\" class=\"" . ($ticket->isPaid() ? ' paid' : ' unpaid') . "\">" . ($ticket->isPaid() ? _("Ticket is paid") : _("Ticket is not paid")) . "</td></tr>";
            }

            $content .= "</table></div><div class=\"ticket-users\"><table class=\"table\">";

            // Owner information.
            $content .= "<tr><td class=\"head grey\" colspan=\"2\">" . _("Owner") . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Name") . "</strong></td><td>" . $owner->getNick() . "</td></tr>";
            if ($user->equals($owner) == false) {   
                $content .= "<tr><td><strong>" . _("Email") . "</strong></td><td>" . $owner->getEmail() . "</td></tr>";
                $content .= "<tr><td><strong>" . _("Address") . "</strong></td><td>" . $owner->getStreetAddress() . "<br />" . $owner->getPostNumber() . " " . $owner->getPostPlace() . "</td></tr>";
                $content .= "<tr><td><strong>" . _("Birthday") . "</strong></td><td>" . date("d.M.Y", $owner->getBirthdayTimestamp()) . "</td></tr>";
            }

            // User information.
            $content .= "<tr><td class=\"head grey\" colspan=\"2\">" . _("User") . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Name") . "</strong></td><td>" . $user->getNick() . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Email") . "</strong></td><td>" . $user->getEmail() . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Address") . "</strong></td><td>" . $user->getStreetAddress() . "<br />" . $user->getPostNumber() . " " . $user->getPostPlace() . "</td></tr>";
            $content .= "<tr><td><strong>" . _("Birthday") . "</strong></td><td>" . date("d.M.Y", $user->getBirthdayTimestamp()) . "</td></tr>";


            $content .= "</table></div></div>";
        }

        break;

    //==================================================================================
    // Search users
    default:

        $scopeSelected = 'search_all';
        if (isset($_POST['scope']) && $_POST['scope'] == 'tickets')
            $scopeSelected = 'search_tickets';

        $userQueryValue = "";
        if (isset($_POST['query'])) {
            $userQueryValue = htmlspecialchars($_POST['query']);
        }

        $content .= '
        <p>' . _("Administer tickets on users.") . '</p>
        <div class=\"front\">
            <form class="normal inline" action="index.php?module=' . $thisModule . '&action=searchUser" method="post">
                <div class="form-group">
                    <input type="text" id="query-arrival" name="query" value="' . $userQueryValue . '" />
                    <input type="submit" name="doSearch" value="' . _("Search") . '" />
                </div>
                <div class="form-group">
                    <label for="search_all"><input type="radio" id="search_all" name="scope" value="all"' . ($scopeSelected == 'search_all' ? ' checked' : '') . ' /> ' . _("Search all users") . '</label>
                </div>
                <div class="form-group">
                    <label for="search_tickets"><input type="radio" id="search_tickets" name="scope" value="tickets"' . ($scopeSelected == 'search_tickets' ? ' checked' : '') . ' /> ' . _("Search users with tickets") . '</label>
                </div>
            </form>
        </div>';

        // Check if from has been submitted.
        if (isset($_POST['doSearch'])) {
            $scope = isset($_POST['scope']) ? $_POST['scope'] : 'all';

            $result = array();
            $resultCount = 0;

            // Verify there is a search query
            $searchString = db_escape($_POST['query']);
            if (strlen($_POST['query']) > 0 || $scope == "tickets") {
                $query = null;

                $str = db_escape(htmlspecialchars($_POST['query']));

                if ($scope == 'all') {
                    $query = sprintf("SELECT nick, firstName, lastName as lastName, ID FROM %s WHERE
                        (nick LIKE '%%%s%%' OR
                        firstName LIKE '%%%s%%' OR
                        lastName LIKE '%%%s%%' OR
                        CONCAT(firstName, ' ', lastName) LIKE '%%%s%%' OR
                        EMail LIKE '%%%s%%'
                        ) ORDER BY ID", $usertable, $str, $str, $str, $str, $str);
                } else if ($scope == "tickets") {
                    $query = sprintf("SELECT DISTINCT u.nick as nick, u.firstName as firstName, u.lastName as lastName,
                        u.ID as ID FROM %s as u, %s as t WHERE t.eventID=%s AND t.user=u.ID AND
                        (u.nick LIKE '%%%s%%' OR
                        u.firstName LIKE '%%%s%%' OR
                        u.lastName LIKE '%%%s%%' OR
                        CONCAT(u.firstName, ' ', u.lastName) LIKE '%%%s%%' OR
                        EMail LIKE '%%%s%%'
                        ) ORDER BY u.ID", $usertable, $ticketstable, $sessioninfo->eventID, $str, $str, $str, $str, $str);
                }

                $result = db_query($query);
                $num    = db_num($result);

                $content .= "<table class=\"table ticket-table\"><thead><tr><th>" . _("Name") . "</th><th>" . _("Tickets") . "</th></tr></thead><tbody>";
                if ($num > 0) {
                    $i = 0;
                    while ($row = db_fetch($result)) {
                        $cssClass = ($i++ % 2 == 0 ? 'odd' : 'even');

                        $tickets = $ticketManager->getTicketsOfUser($row->ID, null, array(''));
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
                                $content .= "<a href=\"index.php?module=arrival&amp;action=ticketdetail&amp;ticket=" . $value->getMd5ID() . "\">" . $value->getTicketType()->getName() . "</a>";
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
