<?php
// Get MPesa callback data
$callbackJSONData = file_get_contents('php://input');
$callbackData = json_decode($callbackJSONData, true);

// Log the raw callback data (for debugging)
file_put_contents('mpesa_callback.log', date("Y-m-d H:i:s") . " - " . json_encode($callbackData) . "\n", FILE_APPEND);

// Check if payment was successful
if (isset($callbackData['Body']['stkCallback']['ResultCode']) && $callbackData['Body']['stkCallback']['ResultCode'] == 0) {
    
    // Extract transaction details
    $metadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'];
    $phoneNumber = $metadata[4]['Value']; // Customer phone number
    $amountPaid = $metadata[0]['Value']; // Amount paid
    $transactionID = $metadata[1]['Value']; // Transaction ID

    // Assume the contestant's ID is passed as metadata during payment request
    $contestantID = $_GET['contestantID'] ?? 'UNKNOWN';

    // ✅ Step 1: Update Votes in Google Sheets
    $googleSheetsURL = "https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec"; // Replace with your Google Apps Script Web App URL

    $postData = json_encode([
        "action" => "updateVotes",
        "contestantID" => $contestantID,
        "votesToAdd" => 1 // Each payment = 1 vote (adjust if needed)
    ]);

    $ch = curl_init($googleSheetsURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    curl_close($ch);

    // ✅ Step 2: Log the Transaction
    file_put_contents('payments.log', date("Y-m-d H:i:s") . " - Payment received from $phoneNumber, Amount: $amountPaid, Transaction ID: $transactionID, Contestant: $contestantID\n", FILE_APPEND);

    // ✅ Step 3: Send Success Response
    echo json_encode(["status" => "success", "message" => "Votes updated successfully"]);
} else {
    // Handle failed transactions
    file_put_contents('failed_payments.log', date("Y-m-d H:i:s") . " - Failed Transaction: " . json_encode($callbackData) . "\n", FILE_APPEND);
    
    echo json_encode(["status" => "error", "message" => "Payment Failed"]);
}
?>
