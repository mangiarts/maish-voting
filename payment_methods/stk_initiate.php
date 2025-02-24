<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$contestantCode = $data['contestantCode'];
$contestantName = $data['contestantName'];
$votes = $data['votes'];
$amount = $data['amount'];
$phone = $data['phone'];

date_default_timezone_set('Africa/Nairobi');

$consumerKey = 'Hy3UmgOWkJrOKj94bvt0ximLDnILn075V0NjAsApatOGhhLa'; // Fill with your app Consumer Key
$consumerSecret = 'R7yJ0VVV4zdz2a82oo2SEDPsape4nYNPXcdeNxrLwD9zWWfjBYgdOznz7YCWSGOh'; // Fill with your app Secret

$BusinessShortCode = '174379';
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

$PartyA = $phone;
$AccountReference = '2255';
$TransactionDesc = 'Vote Payment';

$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

$headers = ['Content-Type:application/json; charset=utf8'];

$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$CallBackURL = 'https://yourdomain.com/payment_methods/callback_url.php';

$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;
curl_close($curl);

$stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $initiate_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);

$curl_post_data = array(
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);

echo $curl_response;
?>