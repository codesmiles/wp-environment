<?php

require_once(__DIR__ . '/../helpers/Helper.php');
require_once(__DIR__ . '/../helpers/ResponseHelper.php');
require_once(__DIR__ . '/../helpers/EmailTemplatesHelper.php');

class AuthController {
    
    // User Registration
    public function register($request) {
        $data = $request->get_params();
        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);
        $email = sanitize_email($data['email']);
        $phone = sanitize_text_field($data['phone']);
        $password = sanitize_text_field($data['password']);
        $confirm_password = sanitize_text_field($data['confirm_password']);
        $agree_to_terms = isset($data['agree_to_our_terms_and_condition']) && $data['agree_to_our_terms_and_condition'] === 'on';
        $subscribe_to_newsletter = isset($data['subscribe_to_our_newsletter']) && $data['subscribe_to_our_newsletter'] === 'on';

        // Validate input
        if (!$first_name || !$last_name || !$email || !$phone || !$password || !$confirm_password) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Missing fields"]]);
        }
        
        if ($password !== $confirm_password) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Passwords do not match"]]);
        }
        
        if (!$agree_to_terms) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["You must agree to our terms and conditions"]]);
        }
        
        // Check if user exists
        if (email_exists($email)) {
            return sendResponse(["error" => true, "message" => "failed_request_error", "data" => ["Email already exists"]]);
        }
        
        $user_id = wp_insert_user([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_login' => $email,
            'user_pass' => $password,
            'user_email' => $email,
            'role' => 'subscriber'
        ]);
        
        if (is_wp_error($user_id)) {
            return sendResponse(["error" => true, "message" => "failed_request_error", "data" => [$user_id->get_error_message()]]);
        }
        
        
        update_user_meta($user_id, 'subscribe_to_newsletter', $subscribe_to_newsletter ? 'yes' : 'no');
        
        // Generate and then send email verification
        $verification_code = generate_verification_token();
        save_verification_code($email, $verification_code);

        if (!send_verification_email($email, $verification_code)) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Unable to send verification email"]]);
        }
        
        return sendResponse(["error" => false, "message" => "request_successful", "data" => ["Registration successful. Check your email for verification."]]);
    }

    // Email Verification
    public function verify_email($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        $verification_code = sanitize_text_field($data['verification_code']);
        
        if (!verify_code($email, $verification_code)) {
            return sendResponse(["error" => true, "message" => "invalid_code", "data" => ["Invalid verification code"]]);
        }
        
        return sendResponse(["error" => false, "message" => "email_verified", "data" => ["Email successfully verified"]]);
    }

    // User Login
    public function login($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        $password = sanitize_text_field($data['password']);
        
        $user = wp_authenticate($email, $password);
        if (is_wp_error($user)) {
            return sendResponse(["error" => true, "message" => "login_failed", "data" => ["Invalid email or password"]]);
        }
        
        return sendResponse(["error" => false, "message" => "login_successful", "data" => ["Login successful"]]);
    }

    // Password Reset Request
    public function reset_password_request($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        
        if (!email_exists($email)) {
            return sendResponse(["error" => true, "message" => "user_not_found", "data" => ["No user found with that email"]]);
        }
        
        // Send password reset email
        if (!send_password_reset_email($email)) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Unable to send reset email"]]);
        }
        
        return sendResponse(["error" => false, "message" => "reset_email_sent", "data" => ["Check your email for reset instructions"]]);
    }

    // Resend Verification Code
    public function resend_verification_code($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        
        if (!email_exists($email)) {
            return sendResponse(["error" => true, "message" => "user_not_found", "data" => ["Email not found"]]);
        }

        $verification_code = generate_verification_token();
        save_verification_code($email, $verification_code);

        if (!send_verification_email($email, $verification_code)) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Unable to resend verification email"]]);
        }

        return sendResponse(["error" => false, "message" => "verification_resent", "data" => ["Verification code resent to your email"]]);
    }

    // Reset Password
    public function reset_password($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        $reset_token = sanitize_text_field($data['reset_token']);
        $new_password = sanitize_text_field($data['new_password']);
        $confirm_password = sanitize_text_field($data['confirm_password']);

        if (!email_exists($email)) {
            return sendResponse(["error" => true, "message" => "user_not_found", "data" => ["No user found with that email"]]);
        }

        if ($new_password !== $confirm_password) {
            return sendResponse(["error" => true, "message" => "validation_error", "data" => ["Passwords do not match"]]);
        }
        
        $stored_token = get_user_meta(email_exists($email), 'reset_password_token', true);
        $expiry_time = get_user_meta(email_exists($email), 'reset_password_token_expiry', true);

        if ($stored_token !== $reset_token) {
            return sendResponse(["error" => true, "message" => "invalid_token", "data" => ["Invalid or expired reset token"]]);
        }

        wp_set_password($new_password, email_exists($email));
        
        delete_user_meta(email_exists($email), 'reset_password_token');
        delete_user_meta(email_exists($email), 'reset_password_token_expiry');

        return sendResponse(["error" => false, "message" => "password_reset_successful", "data" => ["Password has been reset successfully"]]);
    }
    
    
    // Forgot Password
    public function forgot_password($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
    
        // Check if the email exists in the database
        if (!email_exists($email)) {
            return sendResponse(["error" => true, "message" => "user_not_found", "data" => ["No user found with that email"]]);
        }
       
    
        // Generate a reset token
        $reset_token = bin2hex(random_bytes(16));
        $expiry_time = time() + 3600; 
        
        update_user_meta(email_exists($email), 'reset_password_token', $reset_token);
        update_user_meta(email_exists($email), 'reset_password_token_expiry', $expiry_time);
        
        // Send password reset email with both email and token
        if (!send_password_reset_email($email, $reset_token)) {
            return sendResponse(["error" => true, "message" => "email_failed", "data" => ["Unable to send reset email"]]);
        }
    
        return sendResponse(["error" => false, "message" => "reset_email_sent", "data" => ["Check your email for reset instructions"]]);
    }

    

    // Delete User
    public function delete_user($request) {
        $data = $request->get_params();
        $email = sanitize_email($data['email']);
        
        if (!email_exists($email)) {
            return sendResponse(["error" => true, "message" => "user_not_found", "data" => ["No user found with that email"]]);
        }

        $user_id = email_exists($email);
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        
        if (wp_delete_user($user_id)) {
            return sendResponse(["error" => false, "message" => "user_deleted", "data" => ["User has been deleted successfully"]]);
        } else {
            return sendResponse(["error" => true, "message" => "deletion_failed", "data" => ["Failed to delete user"]]);
        }
    }

}