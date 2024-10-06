<?php

require_once(dirname(__FILE__) . '/EmailTemplatesHelper.php');

/**
 * Generates a random 6-digit verification code.
 */
function generate_verification_token() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}


/**
 * Save verification code to the database for the given email.
 */
function save_verification_code($email, $code) {
    global $wpdb;
    $table = $wpdb->prefix . 'user_verifications';

    // I added this to insert and update verification code for the email
    $wpdb->replace($table, [
        'email' => $email,
        'verification_code' => $code,
        'created_at' => current_time('mysql')
    ]);
}

/**
 * Verify the code provided by the user to be the same the one stored in the database.
 */
function verify_code($email, $input_code) {
    global $wpdb;
    $table = $wpdb->prefix . 'user_verifications';

    // Fetch the stored code for the email
    $stored_code = $wpdb->get_var($wpdb->prepare("SELECT verification_code FROM $table WHERE email = %s", $email));

    if (!$stored_code) {
        return false; 
    }

    // If the stored code matches the input code, delete the code from the table and return true
    if ($stored_code === $input_code) {
        $wpdb->delete($table, ['email' => $email]); 
        return true;
    }

    return false; 
}


/**
 * Sends a password reset email to the user.
 */
function send_password_reset_email($user_email, $reset_token) {
    $reset_link = site_url("/reset-password?token=$reset_token&email=$user_email"); 
    $subject = 'Password Reset Request';
    $message = get_password_reset_email_template($reset_link);  
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    return wp_mail($user_email, $subject, $message, $headers); 
}


/**
 * Sends a verification email to the user with a 6-digit token.
 */
function send_verification_email($user_email, $token) {
    // Save the verification code to the database
    save_verification_code($user_email, $token);
    
    $subject = 'Your Email Verification Code';
    $message = get_verification_email_template($token);
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    return wp_mail($user_email, $subject, $message, $headers);
}


/**
 * Sends an email to the sales team about the lab rental request.
 */
function send_lab_rental_email($data) {
    // Sales team email
    $sales_team_email = 'abakpad82@gmail.com'; 
    

    $subject = 'New Lab Rental Request';

   
    $message = get_lab_rental_email_template([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email']
    ], [
        'course_name' => $data['course_name'],
        'duration' => $data['duration'],
        'message' => $data['message']
    ]);

   
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    // Send email to the sales team
    return wp_mail($sales_team_email, $subject, $message, $headers);
}