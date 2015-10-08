// Payment js

function handlePaymentSubmit()
{
    var paymentForm = $("#payment-form");
    var selectedRadio = paymentForm.find("input[type=radio]:checked");
    if (selectedRadio.length > 0) {
        $("#payment-form").hide();
        switch (selectedRadio.val()) {
            case "stripe":
                $(".ticket.pay-stripe").show();
                break;

            case "door":
                $(".ticket.pay-door").show();
                break;

            default:
                break;
        }
    }

    return false;
}

function cancelPayment()
{
    $("#payment-form").show();
    $(".ticket.pay-stripe").hide();
    $(".ticket.pay-door").hide();
}
