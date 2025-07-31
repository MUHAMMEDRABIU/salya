<?php
function getMonnifyToken($apiKey, $secretKey)
{
    $credentials = base64_encode("$apiKey:$secretKey");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://sandbox.monnify.com/api/v1/auth/login");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['responseBody']['accessToken'] ?? null;
}
function createVirtualAccount($token, $contractCode, $customerEmail, $orderRef, $customerName)
{
    $payload = [
        "accountReference" => $orderRef,
        "accountName" => $customerName,
        "currencyCode" => "NGN",
        "contractCode" => $contractCode,
        "customerEmail" => $customerEmail,
        "customerName" => $customerName
    ];

    $ch = curl_init("https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
