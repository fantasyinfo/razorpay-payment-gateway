<?php
session_start();
$host = 'localhost';
$dbusername = 'root';
$dbpass = 'mysql';
$dbname = 'razorpay_payment_gateway';


$conn = mysqli_connect($host, $dbusername, $dbpass, $dbname);



// form save to db
if (isset($_POST['submit'])) {
    $user_name = mysqli_real_escape_string($conn, $_POST['name']);
    $user_email = mysqli_real_escape_string($conn, $_POST['email']);
    $amt = mysqli_real_escape_string($conn, $_POST['amt']);
    $user_id = 2; // getting via session
    $order_amt = $amt * 100;
    $u_order_id = 'u_order_id' . md5(rand(0, 8));
    $razorpay_payment_id = "";
    $razorpay_order_id = "";
    $razorpay_signature = "";




    $order_status = 'pending';
    $query = "insert into orders (user_id, user_name, user_email, u_order_id, razorpay_payment_id, razorpay_order_id, razorpay_signature, order_amt, order_status) values ('$user_id','$user_name','$user_email','$u_order_id','$razorpay_payment_id','$razorpay_order_id','$razorpay_signature','$order_amt','$order_status')";

    $run = mysqli_query($conn, $query);
    if ($run) {
        $_SESSION['user_name'] =  $user_name;
        $_SESSION['user_email'] =  $user_email;
        $_SESSION['u_order_id'] =  $u_order_id;
        $_SESSION['order_amt'] =  $order_amt;

        header('Location: pay.php');
    } else {
        echo 'Something Went Wrong Data Not Inserted';
    }
}