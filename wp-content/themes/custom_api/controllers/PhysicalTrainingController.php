<?php

require_once(__DIR__ . '/../helpers/ResponseHelper.php');
require_once(__DIR__ . '/../helpers/EmailTemplatesHelper.php');

class PhysicalTrainingController {

    public function register_training($request) {
        $data = $request->get_params();
        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);
        $email = sanitize_email($data['email']);
        $training_name = sanitize_text_field($data['training_name']);
        $amount = sanitize_text_field($data['amount']);
        $message = sanitize_text_field($data['message']);
        
        // Validate input
        if (!$first_name || !$last_name || !$email || !$training_name || !$amount || !$message) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Missing fields"]]);
        }
        
        // Send physical training registration information to sales team
        if (!send_training_registration_email([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'training_name' => $training_name,
            'amount' => $amount,
            'message' => $message
        ])) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Failed to send email"]]);
        }
        
        return sendResponse(["error" => false, "message" => "training_request_sent", "data" => ["Training request sent successfully"]]);
    }
}