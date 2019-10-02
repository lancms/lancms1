<?php

$eventID = $sessioninfo->eventID;
$userID = -1;
$user = null;

if (isset($sessioninfo) && $sessioninfo->userID > 1) {
    $userID = $sessioninfo->userID;
    $user = UserManager::getInstance()->getUserByID($userID);
}

$ticketManager = TicketManager::getInstance();

if(!config("enable_ticketorder", $eventID)) {
    $content .= "<p>Ticketorder is not enabled.</p>";
    return;
}

$ticket = isset($_GET['ticket']) && is_numeric($_GET['ticket']) ? $_GET['ticket'] : 0;

$content .= "
        <h1 class=\"page-title\">" . _("Order ticket") . "</h1>
        <div class=\"tickets\">";

switch ($action) {

    //==================================================================================
    // Handle order
    case "receipt":

        if ($userID < 1) {
            header("Location: ?module=ticketorder");
            die();
        }

        $orderInfo = $_SESSION["orderInfo" . $sessioninfo->userID];

        if (is_array($orderInfo)) {
            $content .= "
            <h3>Kvittering</h3>
            <p>Din billett er blitt satt på din konto. Her er noen detaljer om bestillingen:</p>";

            $content .= "<table class=\"table\">
            <tr>
                <td>Status</td>
                <td>" . $orderInfo["status"] . "</td>
            </tr>
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
        } else {
            $content .= "<h3>En feil oppstod!</h3><p>En feil skjedde, vi fant ingen ordre å vise resultat på.</p>";
        }



        unset($_SESSION["orderInfo" . $sessioninfo->userID]);

        break;

    case "handleStripeSuccess":
        if ($userID < 1) {
            header("Location: ?module=ticketorder");
            die();
        }
        $orderInfo = array("status" => "failed", "dateTime" => time(), "userID" => $sessioninfo->userID);

        $ttID = $requestGet->get('ttID');

        $amount = $_SESSION["orderInfo" . $sessioninfo->userID]['amount'] ?? 1;
        $ticketType = $ticketManager->getTicketTypeByID($ttID);
        $canAmount = (isset($maxTicketsPrUser) && is_numeric($maxTicketsPrUser) ? intval($maxTicketsPrUser) : 1);
        $availableTickets = $ticketType->getNumAvailable();
        $ticketIDs = array();

        if (($ticketType instanceof TicketType) === false) {
            header("Location: index.php?module=ticketorder&msg=9");
            die();
        }

        $orderInfo["ticketType"] = $ticketType->getTicketTypeID();
        $orderInfo["ticketTypeType"] = $ticketType->getType();
        $orderInfo["start_time"] = time();
        $price = floor($ticketType->getPrice() * $amount);

        $orderInfo["price"] = $price;
        $orderInfo["amount"] = $amount;

        $checkoutSessionId = $requestGet->get('session_id');
        $tickets = $ticketManager->getTicketsByOrderReference($checkoutSessionId);
        if (count($tickets) > 0) {
            $orderInfo["status"] = 'paid';
        }
        $orderInfo["ticketMD5"] = array_map(function(Ticket $ticket): string {
            return $ticket->getMd5ID();
        }, $tickets);

        $_SESSION["orderInfo" . $sessioninfo->userID] = array_merge(
            $_SESSION["orderInfo" . $sessioninfo->userID] ?? [], $orderInfo);

        header("Location: index.php?module=ticketorder&action=receipt");

        die();

    //==================================================================================
    // Handle order
    case "handleOrderTicket":

        if ($userID < 1) {
            header("Location: ?module=ticketorder");
            die();
        }
        $orderInfo = array("status" => "failed", "dateTime" => time(), "userID" => $sessioninfo->userID);

        $amount = $_SESSION["orderInfo" . $sessioninfo->userID]['amount'] ?? 1;
        $ticketType = $ticketManager->getTicketTypeByID($_SESSION["orderInfo" . $sessioninfo->userID]["ttID"]);
        $canAmount = (isset($maxTicketsPrUser) && is_numeric($maxTicketsPrUser) ? intval($maxTicketsPrUser) : 1);
        $availableTickets = $ticketType->getNumAvailable();
        $ticketIDs = array();

        if ($ticketType instanceof TicketType === false) {
            header("Location: index.php?module=ticketorder&msg=9");
            die();
        }

        if ($amount > $canAmount) {
            header("Location: index.php?module=ticketorder&msg=7");
            die();
        }

        if ($amount > $availableTickets) {
            header('Location: index.php?module=ticketorder&msg=10');
            die();
        }

        // Add the ticket now, and set as paid later!
        $newTicketMd5 = $user->validateAddTicketType($ticketType, $amount);
        if ($newTicketMd5 === false) {
            header("Location: index.php?module=ticketorder&msg=8");
            die();
        }

        $orderInfo["ticketType"] = $ticketType->getTicketTypeID();
        $orderInfo["ticketTypeType"] = $ticketType->getType();
        $orderInfo["ticketMD5"] = $newTicketMd5;
        $orderInfo["start_time"] = time();
        $price = floor($ticketType->getPrice() * $amount);

        $orderInfo["price"] = $price;
        $orderInfo["amount"] = $amount;

        $logPurchase = true;

        switch ($ticketType->getType()) {
            case "onsite-visitor":
                // Handle onsite visitor, just return.
                $orderInfo["onSiteVisitor"] = true;
                $orderInfo["status"] = "notused";
                $tickets = $ticketManager->getTicketsByMD5($orderInfo["ticketMD5"]);
                if (is_array($tickets) && count($tickets) > 0) {
                    foreach ($tickets as $ticket) {
                        $ticketIDs[] = $ticket->getTicketID();
                    }
                }
                unset($tickets);
                break;

            case "preorder":
                $paymentMethod = $requestPost->get("pay_method", "door");
                $orderInfo["paymentMethod"] = $paymentMethod;

                if ($paymentMethod == "stripe") {
                    $logPurchase = false; // logging done in webhook!
                    $checkoutSessionId = $requestGet->get('session_id');
                    $tickets = $ticketManager->getTicketsByOrderReference($checkoutSessionId);
                    if (count($tickets) > 0) {
                        $orderInfo["status"] = 'paid';
                    }
                    $orderInfo[] = '';
                } else if ($ticketOrderAllowPreorderPayOnArrival) {
                    // Just go!
                    $orderInfo["status"] = "notused";
                    $tickets = $ticketManager->getTicketsByMD5($orderInfo["ticketMD5"]);
                    if (is_array($tickets) && count($tickets) > 0) {
                        foreach ($tickets as $ticket) {
                            $ticketIDs[] = $ticket->getTicketID();
                        }
                    }
                    unset($tickets);
                } else {
                    header('Location: index.php?module=ticketorder&msg=11');
                    die();
                }

                break;
        }

        if ($logPurchase) {
            // Log this
            TicketManager::getInstance()->logTicketPurchase(
                $userID,
                $eventID,
                (count($ticketIDs) > 0 ? implode(", ", $ticketIDs) : ""),
                time(),
                (isset($orderInfo["stripeTime"]) ? $orderInfo["stripeTime"] : 0),
                (isset($orderInfo["stripeRef"]) ? $orderInfo["stripeRef"] : ""),
                $orderInfo["status"],
                $ticketType,
                $price,
                $amount
            );

            $glLog = array(
                "userID" => $userID,
                "eventID" => $eventID,
                "ticketIDs" => (count($ticketIDs) > 0 ? implode(", ", $ticketIDs) : ""),
                "time" => time(),
                "stripeTime" => (isset($orderInfo["stripeTime"]) ? $orderInfo["stripeTime"] : 0),
                "stripeRef" => (isset($orderInfo["stripeRef"]) ? $orderInfo["stripeRef"] : ""),
                "status" => $orderInfo["status"],
                "ticketType" => $ticketType,
                "price" => $price,
                "amount" => $amount
            );

            log_add("ticketorder", "handleticketpurchase", serialize($glLog));
        }

        $_SESSION["orderInfo" . $sessioninfo->userID] = array_merge(
            $_SESSION["orderInfo" . $sessioninfo->userID], $orderInfo);

        header("Location: index.php?module=ticketorder&action=receipt");

        die();

    //==================================================================================
    // Order a ticket
    case "orderTicket":

        if ($userID < 1) {
            header("Location: ?module=ticketorder");
            die();
        }
        $ttID = $requestGet->has("ttID") ? $requestGet->getInt("ttID", -1) : -1;
        if ($ttID > 0) {
            $ticketType = $ticketManager->getTicketTypeByID($ttID);
            $amount = $requestGet->has("amount") ? $requestGet->getInt("amount", 1) : 1;
            $canAmount = (isset($maxTicketsPrUser) && is_numeric($maxTicketsPrUser) ? intval($maxTicketsPrUser) : 1);

            if ($amount > $canAmount) {
                header("Location: index.php?module=ticketorder&msg=7");
                die();
            }

            $_SESSION["orderInfo" . $sessioninfo->userID] = array("ttID" => $ttID, "amount" => $amount, "newTickets" => $newTicketMd5);
            $priceNormal = floor($ticketType->getPrice() * $amount);
            $price = $priceNormal . "00";

            if (file_exists(__DIR__ . "/types/" . $ticketType->getType() . ".php")) {
                include __DIR__ . "/types/" . $ticketType->getType() . ".php";
            } else {
                $content .= "Filen : " . $ticketType->getType() . ".php ble ikke funnet";
            }

        } else {
            header("Location: index.php?module=ticketorder&msg=2");
            die();
        }

        break;

    //==================================================================================
    // List tickets
    default:
        $ticketTypes = $ticketManager->getTicketTypes();
        $ticketTypes = array_filter($ticketTypes, "_filterTypes");

        // Message to user
        if (isset($_GET['msg']) && is_numeric($_GET['msg'])) {
            $message = "";
            $messageType = "success";

            switch ($_GET['msg']) {
                case 1:
                    $message = _("Ticket ordered") . "!";
                    break;

                case 2:
                    $message = _("Ticket not found");
                    $messageType = "danger";
                    break;

                case 3:
                case 4:
                    $message = _("You have ordered the maximum tickets allowed");
                    $messageType = "danger";
                    break;

                case 10:
                    $message = _('There are no more tickets available.');
                    $messageType = 'danger';
                    break;

                default:
                    break;
            }

            $content .= "<div class=\"alert alert-$messageType\">$message</div>";
        }

        if (count($ticketTypes) > 0) {
            $content .= "<p>" . _("Choose a ticket to order.") . "</p>
            <div class=\"ticket-list\">";
            foreach ($ticketTypes as $key => $ticketType) {

                $content .= "<div class=\"ticket\">
                <div class=\"title\"><h3>" . $ticketType->getName() . "</h3></div>
                <div class=\"description\">" . $ticketType->getDescription() . "</div>";

                $content .= "<div class=\"order\">";
                if ($user instanceof User) {
                    $canOrderAmount = $ticketType->getAmountUserCanOrder($user);
                    $available = $ticketType->getNumAvailable();

                    if ($available > 0 && $canOrderAmount > 0) {
                        $content .= "<form action=\"index.php?\" method=\"get\">";
                        // If $maxTicketsPrUser is set print a select element up to the max amount set.
                        // Otherwise amount 1 is default.
                        $content .= "<input type=\"hidden\" name=\"module\" value=\"ticketorder\" />";
                        $content .= "<input type=\"hidden\" name=\"action\" value=\"orderTicket\" />";
                        $content .= "<input type=\"hidden\" name=\"ttID\" value=\"" . $ticketType->getTicketTypeID() . "\" />";
                        $content .= "<label for=\"amount-" . $key . "\">Amount:</label>
                            &nbsp;<select id=\"amount-" . $key . "\" name=\"amount\">";
                        for ($i=1; $i <= $canOrderAmount; $i++) {
                            $content .= "<option value=\"$i\">$i</option>";
                        }
                        $content .= "</select>";

                        $content .= "&nbsp;<input type=\"submit\" class=\"btn\" name=\"order-ticket\" value=\"" . _("Order ticket") . "\" />
                        </form>";
                    } else if ($available < 1) {
                        $content .= "<span class=\"small italic\">" . _('There are no more tickets available') . "</span>";
                    } else if ($canOrderAmount < 1) {
                        $content .= "<span class=\"small italic\">" . _("You have ordered the maximum allowed") . "</span>";
                    }
                } else {
                    $content .= "<span class=\"small italic\">" . _("You be logged in to order tickets") . "</span>";
                }
                $content .= "</div></div>";
            }
            $content .= "</div>";
        } else {
            $content .= "<div class=\"empty-text\">" . _("No tickets found") . "</div>";
        }

        break;

}

$content .= "</div>";

function _filterTypes(&$var) {
    if ($var->isEnabled() == false)
        return false;

    return true;
}
