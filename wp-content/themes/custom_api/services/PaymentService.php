<?php

/*
|--------------------------------------------------------------------------
| PaymentService.php
|--------------------------------------------------------------------------
| Handles payment processing with Paystack.
*/

class PaymentService
{
    private $paystack_secret_key;

    public function __construct()
    {
        $this->paystack_secret_key = 'paystack_secret_key'; 
    }

    /**
     * Initiate a payment request with Paystack.
     *
     * @param string $email User's email address.
     * @param float $amount Amount to be charged (in kobo).
     * @param string $courseName Name of the course.
     * @return array Response from Paystack API.
     */
    public function initiatePayment($email, $amount, $courseName)
    {
        $url = 'https://api.paystack.co/transaction/initialize';
        $data = [
            'email' => $email,
            'amount' => $amount,
            'metadata' => [
                'course_name' => $courseName
            ]
        ];

        $response = $this->sendRequest($url, $data);
        return $response;
    }

    /**
     * Verify a payment after redirection from Paystack.
     *
     * @param string $reference Transaction reference from Paystack.
     * @return array Response from Paystack API.
     */
    public function verifyPayment($reference)
    {
        $url = "https://api.paystack.co/transaction/verify/{$reference}";
        $response = $this->sendRequest($url);
        return $response;
    }

    /**
     * Send an HTTP request to the specified URL.
     *
     * @param string $url The API endpoint.
     * @param array|null $data Data to be sent (optional).
     * @return array Response from the API.
     */
    private function sendRequest($url, $data = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->paystack_secret_key,
            'Content-Type: application/json'
        ]);

        if ($data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}