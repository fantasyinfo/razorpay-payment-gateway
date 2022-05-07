<?php


$host = 'localhost';
$dbusername = 'root';
$dbpass = 'mysql';
$dbname = 'razorpay_payment_gateway';


$conn = mysqli_connect($host, $dbusername, $dbpass, $dbname);

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//



$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$u_order_id = $_SESSION['u_order_id'];
$order_amt = $_SESSION['order_amt'];
$amount = $order_amt * 100;







$orderData = [
    'receipt'         => 3456,
    'amount'          => $order_amt * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR') {
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$checkout = 'automatic';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true)) {
    $checkout = $_GET['checkout'];
}

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "Edu247",
    "description"       => "Best Job Posting Site",
    "image"             => "https://edu247.in/wp-content/uploads/2022/01/ll.jpg",
    "prefill"           => [
        "name"              => $user_name,
        "email"             => $user_email,
        "contact"           => "9999999999",
    ],
    "notes"             => [
        "address"           => "Hello World",
        "merchant_order_id" => "12312321",
    ],
    "theme"             => [
        "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR') {
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");