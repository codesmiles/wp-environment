<?php

require_once(__DIR__ . '/../helpers/ResponseHelper.php');
require_once(__DIR__ . '/../helpers/EmailTemplatesHelper.php');
require_once(__DIR__ . '/../helpers/Helper.php'); 

class LabRentalController {

    public function rent_lab($request) {
        $data = $request->get_params();
        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);
        $email = sanitize_email($data['email']);
        $course_name = sanitize_text_field($data['course_name']);
        $duration = sanitize_text_field($data['duration']);
        $message = sanitize_text_field($data['message']);
        
        // Validate input
        if (!$first_name || !$last_name || !$email || !$course_name || !$duration || !$message) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Missing fields"]]);
        }
        
        // Send rental information to sales team
        if (!send_lab_rental_email([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'course_name' => $course_name,
            'duration' => $duration,
            'message' => $message
        ])) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Failed to send email"]]);
        }
        
        return sendResponse(["error" => false, "message" => "rental_request_sent", "data" => ["Rental request sent successfully"]]);
    }
}