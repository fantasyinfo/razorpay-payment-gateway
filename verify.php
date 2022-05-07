<?php

$host = 'localhost';
$dbusername = 'root';
$dbpass = 'mysql';
$dbname = 'razorpay_payment_gateway';


$conn = mysqli_connect($host, $dbusername, $dbpass, $dbname);

require('config.php');

session_start();

require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false) {
    $api = new Api($keyId, $keySecret);

    try {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    } catch (SignatureVerificationError $e) {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}


$razorpay_payment_id = $_POST['razorpay_payment_id'];
$razorpay_order_id = $_SESSION['razorpay_order_id'];
$razorpay_signature = $_POST['razorpay_signature'];
$u_order_id = $_SESSION['u_order_id'];

if ($success) {
    $query = "update orders set razorpay_payment_id = '$razorpay_payment_id',razorpay_order_id = '$razorpay_order_id' , razorpay_signature = '$razorpay_signature', order_status = 'Done' where u_order_id = '$u_order_id' ";

    $run = mysqli_query($conn, $query);
    if ($run) {
        echo 'Your Payment is Done';
        echo $razorpay_payment_id;
        echo $razorpay_order_id;
        echo $razorpay_signature;
        //header('Location: pay.php');
    } else {
        echo 'Something Went Wrong Data Not Updated Your Payment is Failed';
    }
} else {
    echo 'Something Went Wrong Data Not Updated Your Payment is Failed';
}



// if ($success === true) {
//     $html = "<p>Your payment was successful</p>
//              <p>Payment ID: {$_POST['razorpay_payment_id']}</p>
//              <p>Order ID: {$_SESSION['razorpay_order_id']}</p>
//              <p>Signature ID: {$_POST['razorpay_signature']}</p>


//              ";
// } else {
//     $html = "<p>Your payment failed</p>
//              <p>{$error}</p>";
// }

echo "<pre>";
//echo $html;

print_r($_POST);
print_r($_SESSION);