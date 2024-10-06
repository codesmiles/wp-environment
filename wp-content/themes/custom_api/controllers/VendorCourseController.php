<?php

require_once(__DIR__ . '/../helpers/ResponseHelper.php');
require_once(__DIR__ . '/../helpers/EmailTemplatesHelper.php');

class VendorCoursesController {

    public function purchase_vendor_course($request) {
        $data = $request->get_params();
        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);
        $email = sanitize_email($data['email']);
        $course_name = sanitize_text_field($data['course_name']);
        $amount = sanitize_text_field($data['amount']);
        $message = sanitize_text_field($data['message']);
        
        // Validate input
        if (!$first_name || !$last_name || !$email || !$course_name || !$amount || !$message) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Missing fields"]]);
        }
        
        // Send vendor course purchase information to sales team
        if (!send_vendor_course_email([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'course_name' => $course_name,
            'amount' => $amount,
            'message' => $message
        ])) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Failed to send email"]]);
        }
        
        return sendResponse(["error" => false, "message" => "vendor_course_request_sent", "data" => ["Vendor course request sent successfully"]]);
    }
}