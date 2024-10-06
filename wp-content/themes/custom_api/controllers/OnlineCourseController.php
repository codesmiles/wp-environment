<?php

require_once(__DIR__ . '/../helpers/ResponseHelper.php');
require_once(__DIR__ . '/../helpers/EmailTemplatesHelper.php');
require_once(__DIR__ . '/../helpers/MoodleAPIHelper.php');
require_once(__DIR__ . '/../services/PaymentService.php');

class OnlineCourseController {

    public function purchase_online_course($request) {
        $data = $request->get_params();
        $user_id = get_current_user_id();
        
        if (!$user_id) {
            return sendResponse(["error" => true, "message" => "authentication_required", "data" => ["User not authenticated"]]);
        }
        
        $course_name = sanitize_text_field($data['course_name']);
        $amount = sanitize_text_field($data['amount']);
        
        // Fetch user email (assuming you store user emails in WordPress)
        $user_info = get_userdata($user_id);
        $email = $user_info->user_email;

        // Process payment via Paystack
        $payment_service = new PaymentService();
        $payment_result = $payment_service->initiatePayment($email, $amount, $course_name);

        if (empty($payment_result['status']) || !$payment_result['status']) {
            return sendResponse(["error" => true, "message" => "payment_failed", "data" => ["Payment failed or could not be processed"]]);
        }

        // Send payment information to the sales team via email
        if (!send_online_course_payment_email([
            'user_id' => $user_id,
            'course_name' => $course_name,
            'amount' => $amount
        ])) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Failed to send email"]]);
        }

        // Enroll user in Moodle course
        $enrollment_result = enroll_user_in_moodle($user_id, $course_name);
        if (!$enrollment_result['success']) {
            return sendResponse(["error" => true, "message" => "moodle_enrollment_failed", "data" => ["Failed to enroll in Moodle"]]);
        }

        // Send course login credentials and details
        if (!send_moodle_course_credentials($user_id, $course_name)) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Failed to send Moodle credentials"]]);
        }

        return sendResponse(["error" => false, "message" => "purchase_successful", "data" => ["Course purchase and Moodle enrollment successful"]]);
    }
}