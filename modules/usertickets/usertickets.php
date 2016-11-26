<?php

$eventID = $sessioninfo->eventID;
$userID = -1;
$user = null;

if (isset($sessioninfo) && $sessioninfo->userID > 1) {
    $userID = $sessioninfo->userID;
    $user = UserManager::getInstance()->getUserByID($userID);
} else {
    header ('Location: index.php');
    die();
}

if(!config("enable_usertickets", $eventID)) {
    $content .= "<p>Usertickets is not enabled.</p>";
    return;
}

$content .= "
        <h1 class=\"page-title\">" . _("My tickets") . "</h1>
        <div class=\"user-tickets\">";

switch ($action) {

    //==================================================================================
    // List user tickets
    case "change":
        $ticketID = isset($_GET['ticketID']) && is_numeric($_GET['ticketID']) ? intval($_GET['ticketID']) : -1;
        if ($ticketID < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = TicketManager::getInstance()->getTicket($ticketID);
            $changeType = (isset($_GET['type']) ? $_GET['type'] : 'user');
            
            /* HANDLERS */
            if (isset($_GET['set']) && is_numeric($_GET['set']) == true && intval($_GET['set']) > 0) {
                $ret = "index.php?module=usertickets&changed=" . $changeType;

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
                <form class="normal inline" action="index.php?module=usertickets&action=change&type=' . $changeType . '&ticketID=' . $ticketID . '" method="post">
                    <div class="form-group">
                        <input type="text" id="query-arrival" name="query" value="' . $userQueryValue . '" />
                        <input type="submit" name="doSearch" value="' . _("Search") . '" />
                    </div>
                </form>
            </div>';

            if (strlen($userQueryValue) > 0) {
                $result = UserManager::getInstance()->searchUsers($userQueryValue);

                if (count($result) > 0) {
                    $content .= "<table class=\"table ticket-table\"><thead><tr><th>" . _("Name") . "</th></tr></thead><tbody>";
                    foreach ($result as $key => $value) {
                        $name = $value->firstName . ' ' . $value->lastName . ' (' . $value->nick . ')';
                        $content .= "
                            <tr><td>
                                <a href=\"index.php?module=usertickets&action=change&type=" . $changeType . "&amp;set=" . $value->ID . "&amp;ticketID=$ticketID\">$name</a>
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
    // Delete ticket
    case "deletesingle":
        $ticketID = $requestGet->has("ticketID") ? $requestGet->getInt("ticketID") : -1;
        if ($ticketID < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $tickets = $user->getTickets();
            $deleteTicket = null;

            if (count($tickets) > 0) {
                foreach ($tickets as $key => $ticket) {
                    if (($ticket instanceof Ticket) == false) continue;

                    if ($ticket->getTicketID() == $ticketID) {
                        $deleteTicket = $ticket;
                    }
                }
                unset($tickets);
            }

            $msg = 0;
            if ($deleteTicket instanceof Ticket) {
                $msg = 1;
                
                if (!$deleteTicket->deleteTicket()) {
                    $msg = 2;
                }
            }

            header ('Location: index.php?module=usertickets&msg=' . $msg);
            die();

        }
        break;

    //==================================================================================
    // Delete multiple tickets
    case "delete":
        $ticketIDs = isset($_POST['ticketIDs']) && is_array($_POST['ticketIDs']) ? $_POST['ticketIDs'] : array();
        if (count($ticketIDs) < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $tickets = $user->getTickets();
            $ticketsDeleted = 0;

            if (count($tickets) > 0) {
                foreach ($tickets as $key => $ticket) {
                    if (($ticket instanceof Ticket) == false) continue;

                    if (in_array($ticket->getTicketID(), $ticketIDs)) {
                        $ticketsDeleted++;
                        $ticket->deleteTicket();
                    }
                }
                unset($tickets);
            }

            header ('Location: index.php?module=usertickets&msg=3&deleted=' . $ticketsDeleted);
            die();

        }
        break;

    //==================================================================================
    // Pay ticket now
    case "payTicket":
        $ticketID = isset($_GET['ticketID']) && is_numeric($_GET['ticketID']) ? intval($_GET['ticketID']) : -1;
        if ($ticketID < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = TicketManager::getInstance()->getTicket($ticketID);
            if ($ticket->isPaid()) {
                header("Location: index.php?module=usertickets");
                die();
            }

            include __DIR__ . "/payticket.php";
        }
        break;

    //==================================================================================
    // Receipt
    case "receipt":
        $orderInfo = $_SESSION["order-" . $userID];

        if (is_array($orderInfo)) {


            if (isset($orderInfo["error"])) {
                $content .= "<h3>En feil oppstod!</h3>";
                $content .= "<p>Vi oppdaget en feil under behandlingen av orderen din, dette er meldingen vi fikk:</p>";
                $content .= "<pre>" . $orderInfo["error"] . "</pre>";
            } else {
                $content .= "
                <h3>Kvittering</h3>
                <p>Din billett er blitt satt som betalt.</p>";

                $content .= "<table class=\"table\">
                <tr>
                    <td>Status</td>
                    <td>" . $orderInfo["status"] . "</td>
                </tr>";

                $content .= "
                <tr>
                    <td>Betalingsmåte</td>
                    <td>" . ($orderInfo["paymentMethod"] == "door" ? "Betal-i-døra" : $orderInfo["paymentMethod"]) . "</td>
                </tr>
                <tr>
                    <td>Dato</td>
                    <td>" . date("d.m.Y H:i:s", $orderInfo["start_time"]) . "</td>
                </tr>
                <tr>
                    <td>Billett-type</td>
                    <td>" . $orderInfo["ticketTypeType"] . "</td>
                </tr>
                <tr>
                    <td>Antall</td>
                    <td>" . $orderInfo["amount"] . "</td>
                </tr>
                <tr>
                    <td>Pris</td>
                    <td>" . number_format($orderInfo["price"]) . " kr</td>
                </tr>";

                if ($orderInfo["paymentMethod"] == "stripe") {
                    $content .= "<tr><td>Stripe-REF</td><td>" . $orderInfo["stripeRef"] . "</td></tr>";
                }

                $content .= "
                </table>";
                }
            
        } else {
            $content .= "<h3>En feil oppstod!</h3><p>En feil skjedde, vi fant ingen ordre å vise resultat på.</p>";
        }

        unset($_SESSION["order-" . $userID]);
        break;

    //==================================================================================
    // Handler Pay ticket now
    case "handlePayTicket":
        $ticketID = isset($_POST['ticketID']) && is_numeric($_POST['ticketID']) ? intval($_POST['ticketID']) : -1;
        if ($ticketID < 1) {
            $content .= "<p>Ugyldig parametere</p>";
        } else {
            $ticket = TicketManager::getInstance()->getTicket($ticketID);
            if ($ticket->isPaid()) {
                header("Location: index.php?module=usertickets");
                die();
            }

            $ticketType = $ticket->getTicketType();

            // We get from stripe:
            $orderInfo = array("status" => "failed", "dateTime" => time(), "userID" => $sessioninfo->userID);

            $orderInfo["ticketType"] = $ticketType->getTicketTypeID();
            $orderInfo["ticketTypeType"] = $ticketType->getType();
            $orderInfo["ticketMD5"] = $ticket->getMd5ID();
            $orderInfo["start_time"] = time();
            $price = floor($ticketType->getPrice());

            $orderInfo["price"] = $price;
            $orderInfo["amount"] = 1;

            $stripeToken = $_POST["stripeToken"];
            $stripeTokenType = $_POST["stripeTokenType"];
            $stripeEmail = $_POST["stripeEmail"];

            $stripePrice = $price . "00";

            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey($stripePaymentConfig["secretKey"]);

            // Create the charge on Stripe's servers - this will charge the user's card
            try {
                $charge = \Stripe\Charge::create(
                    array(
                        "amount" => $stripePrice, // amount in cents, again
                        "currency" => "nok",
                        "source" => $stripeToken,
                        "description" => "Charge: " . $ticketType->getName()
                    )
                );

                $orderInfo["stripeRef"] = $charge->id;
                $orderInfo["stripeTime"] = $charge->created;
                $orderInfo["status"] = "paid";

                if ($charge->paid == true) {
                    // Set ticket as paid.
                    $ticket->setPaid();
                    $orderInfo["setAsPaid"] = "true";
                } else {
                    // TODO: Handle no tickets to set as paid!
                }
            } catch(\Stripe\Error\Card $e) {
                $orderInfo["error"] = strval($e);
                // no-op
            } catch(\Stripe\Error\InvalidRequest $e) {
                $orderInfo["error"] = strval($e);
                // no-op
            }

            $_SESSION["order-" . $userID] = $orderInfo;

            // Log this
            TicketManager::getInstance()->logTicketPurchase(
                $userID,
                $eventID,
                $ticket->getTicketID(),
                time(),
                (isset($orderInfo["stripeTime"]) ? $orderInfo["stripeTime"] : 0),
                (isset($orderInfo["stripeRef"]) ? $orderInfo["stripeRef"] : ""),
                $orderInfo["status"],
                $ticketType,
                $price,
                1
            );

            header("Location: index.php?module=usertickets&action=receipt");
            die();
        }
        break;

    //==================================================================================
    // List user tickets
    default:
        $tickets = $user->getTickets();

        // Message to user
        if (isset($_GET['msg']) && is_numeric($_GET['msg'])) {
            $message = "";
            $messageType = "success";

            switch ($_GET['msg']) {
                case 1:
                    $message = _("Ticket has been deleted");
                    break;

                case 2:
                    $message = _("An error occurred during the deletion of the ticket");
                    $messageType = "danger";
                    break;

                case 3:
                    $message = _("Tickets has been deleted");
                    break;

                case 4:
                    $message = _("Ticket has been paid!");
                    break;

                default:
                    break;
            }

            $content .= "<div class=\"alert alert-$messageType\">$message</div>";
        }

        $explainCantSeat = false;

        $content .= "
        <p>" . _("This table lists all tickets you have ordered for this event.") . "</p>";

        if (count($tickets) > 0) {
            $content .= "<form action=\"index.php?module=usertickets&action=delete\" method=\"post\">
                <table style=\"width:100%\" class=\"table ticket-table\">
                    <thead>
                    <tr>
                        <th><input type=\"checkbox\" name=\"select_all\" id=\"select-all-tickets\" value=\"1\" /></th>
                        <th>" . _("Ticket id") . "</th>
                        <th>" . _("Name") . "</th>
                        <th>" . _("Owner") . " / " . _("User") . "</th>
                        <th>" . _("Seat") . "</th>
                        <th>" . _("Status") . "</th>
                        <th>" . _("Actions") . "</th>
                    </tr>
                    </thead>
                    <tbody>";

                $canDelete = 0;
                if (count($tickets) > 0) {
                    foreach ($tickets as $key => $ticket) {
                        if (($ticket instanceof Ticket) == false) continue;
                        
                        $isDeleted = ($ticket->getStatus() == Ticket::TICKET_STATUS_DELETED);

                        $ticketTypeName = $owner = $user = "<em>Ukjent</em>";

                        $status = _("Not paid");
                        $statusColumnCss = "notpaid";
                        if ($ticket->isPaid()) {
                            $status = _("Paid");
                            $statusColumnCss = "paid";
                        }
                        
                        switch ($ticket->getStatus()) {
                            case Ticket::TICKET_STATUS_USED:
                                $status = _("Used");
                                $statusColumnCss = "used";
                                break;
                                
                            case Ticket::TICKET_STATUS_DELETED:
                                $status = _("Deleted");
                                $statusColumnCss = "deleted";
                                break;
                        }

                        $seat = _("Not seated");
                        if ($ticket->getSeat() instanceof TicketSeat) {
                            $seat = _("Seated");
                        }

                        if ($ticket->getOwner() instanceof User)
                            $owner = $ticket->getOwner()->getFullName();                

                        if ($ticket->getUser() instanceof User)
                            $user = $ticket->getUser()->getFullName();

                        if ($ticket->getTicketType() instanceof TicketType)
                            $ticketTypeName = $ticket->getTicketType()->getName();

                        $canSeat = $ticket->canSeat();
                        if (!$canSeat && !$ticket->isPaid())
                            $explainCantSeat = true;

                        $content .= "<tr><td>";
                        if (( ! $isDeleted) && ($ticket->isPaid() === false && $ticket->getTicketType()->getType() == "preorder")) {
                            $canDelete++;
                            $content .= "<input type=\"checkbox\" class=\"checkbox-ticketSelect\" name=\"ticketIDs[]\" value=\"" . $ticket->getTicketID() . "\" />";
                        }

                        $content .= "</td>
                        <td>" . $ticket->getTicketID() . "</td>
                        <td>" . $ticketTypeName . "</td>
                        <td>
                            " . $owner . (!$isDeleted ? "&nbsp;<span class=\"small\">[<a href=\"?module=usertickets&amp;action=change&amp;type=owner&amp;ticketID=" . $ticket->getTicketID() . "\">" . _("Change owner") . "</a>]</span>" : "") . "<br />
                            " . $user . (!$isDeleted ? "&nbsp;<span class=\"small\">[<a href=\"?module=usertickets&amp;action=change&amp;type=user&amp;ticketID=" . $ticket->getTicketID() . "\">" . _("Change user") . "</a>]</span>" : "") . "
                        </td>
                        <td>" . $seat . "<br />
                            " . ($canSeat && !$isDeleted ? "<span class=\"small\">[<a href=\"?module=seating&ticketID=" . $ticket->getTicketID() . "\">" . lang("Place on map", "ticketorder") . "</a>]</span>" : "") . "
                        </td>
                        <td class=\"ticket-" . $statusColumnCss . "\">" . $status . "</td>
                        <td>";

                        if (( ! $isDeleted) && ($ticket->isPaid() === false && $ticket->getTicketType()->getType() == "preorder")) {
                            $content .= "<a href=\"index.php?module=usertickets&amp;action=payTicket&amp;ticketID=" . $ticket->getTicketID() . "\">" . _("Pay now") . "</a><br />";
                            $content .= "<a href=\"index.php?module=usertickets&amp;action=deletesingle&amp;ticketID=" . $ticket->getTicketID() . "\">" . _("Remove") . "</a>";
                        } else {
                            $content .= "&nbsp;";
                        }

                        $content .= "</td>
                        </tr>";
                    }
                }

                $content .= "
                </tbody>
                </table>";

                if ($canDelete > 0) {
                    $content .= "<div class=\"pull-right space-top-margin\">
                        <input type=\"submit\" name=\"deleted\" value=\"" . _("Delete selected") . "\" />
                    </div>";
                }
            $content .= "</form>";

            if ($explainCantSeat) {
                $content .= "<div class=\"clear\"></div><div style=\"margin-top:15px;\"><p>Du kan kun reservere en plass til en ubetalt billett, ved å betale en billett kan du plassere en umiddelbart.</p></div>";
            }

            $content .= "
            <script>
                $(document).ready(function(){
                    $(\"#select-all-tickets\").on('change', function(){
                        $(\".checkbox-ticketSelect\").each(function(i,v) {
                            $(v).prop(\"checked\", !$(v).prop(\"checked\"));
                        });
                    });
                });
            </script>";
        } else {
            $content .= "<div class=\"empty-text\">" . _("No tickets found") . "</div>";
        }
        break;

}

$content .= "</div>";
