<?php

/*
|--------------------------------------------------------------------------
| EmailTemplatesHelper.php
|--------------------------------------------------------------------------
| Helper for constructing email templates for different purposes.
*/

/**
 * Returns an HTML email template for email verification.
 */
function get_verification_email_template($token) {
    return '
    <html>
    <body>
        <h1>Email Verification</h1>
        <p>Your verification code is: <strong>' . $token . '</strong></p>
        <p>Please enter this code on the website to verify your account.</p>
    </body>
    </html>';
}

/**
 * Returns an HTML email template for lab rental requests.
 */
function get_lab_rental_email_template($user_data, $lab_data) {
    return '
    <html>
    <body>
        <h1>Lab Rental Request</h1>
        <p>A user has requested to rent a lab. Details below:</p>
        <p><strong>Name:</strong> ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . '</p>
        <p><strong>Email:</strong> ' . $user_data['email'] . '</p>
        <p><strong>Course Name:</strong> ' . $lab_data['course_name'] . '</p>
        <p><strong>Rental Duration:</strong> ' . $lab_data['duration'] . '</p>
        <p><strong>Message:</strong> ' . $lab_data['message'] . '</p>
    </body>
    </html>';
}

/**
 * Returns an HTML email template for certified vendor course purchase.
 */
function get_vendor_course_email_template($user_data, $course_data) {
    return '
    <html>
    <body>
        <h1>Certified Vendor Course Purchase Request</h1>
        <p>A user has shown interest in purchasing a certified vendor course. Details below:</p>
        <p><strong>Name:</strong> ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . '</p>
        <p><strong>Email:</strong> ' . $user_data['email'] . '</p>
        <p><strong>Course Name:</strong> ' . $course_data['course_name'] . '</p>
        <p><strong>Amount:</strong> ' . $course_data['amount'] . '</p>
        <p><strong>Message:</strong> ' . $course_data['message'] . '</p>
    </body>
    </html>';
}

/**
 * Returns an HTML email template for physical training registration.
 */
function get_physical_training_email_template($user_data, $training_data) {
    return '
    <html>
    <body>
        <h1>Physical Training Registration Request</h1>
        <p>A user has registered for a physical training. Details below:</p>
        <p><strong>Name:</strong> ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . '</p>
        <p><strong>Email:</strong> ' . $user_data['email'] . '</p>
        <p><strong>Training Name:</strong> ' . $training_data['training_name'] . '</p>
        <p><strong>Amount:</strong> ' . $training_data['amount'] . '</p>
        <p><strong>Message:</strong> ' . $training_data['message'] . '</p>
    </body>
    </html>';
}

/**
 * Returns an HTML email template for online course purchase.
 */
function get_online_course_email_template($user_data, $course_data) {
    return '
    <html>
    <body>
        <h1>Online Course Purchase</h1>
        <p>A user has made a payment for an online course. Details below:</p>
        <p><strong>Name:</strong> ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . '</p>
        <p><strong>Email:</strong> ' . $user_data['email'] . '</p>
        <p><strong>Course Name:</strong> ' . $course_data['course_name'] . '</p>
        <p><strong>Amount:</strong> ' . $course_data['amount'] . '</p>
    </body>
    </html>';
}

/**
 * Returns an HTML email template for sending Moodle login credentials after course enrollment.
 */
function get_moodle_enrollment_email_template($user_data, $course_data, $moodle_credentials) {
    return '
    <html>
    <body>
        <h1>Welcome to Your Online Course</h1>
        <p>Dear ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . ',</p>
        <p>You have been successfully enrolled in the course: <strong>' . $course_data['course_name'] . '</strong>.</p>
        <p>Here are your Moodle login credentials:</p>
        <ul>
            <li><strong>Username:</strong> ' . $moodle_credentials['username'] . '</li>
            <li><strong>Password:</strong> ' . $moodle_credentials['password'] . '</li>
            <li><strong>Login URL:</strong> <a href="' . $moodle_credentials['login_url'] . '">Moodle Login</a></li>
        </ul>
        <p>The course will start on: <strong>' . $course_data['start_date'] . '</strong>.</p>
        <p>We wish you the best in your studies!</p>
    </body>
    </html>';
}


/**
 * Returns an HTML email template for password reset.
 */
function get_password_reset_email_template($reset_link) {
    return '
    <html>
    <body>
        <h1>Password Reset Request</h1>
        <p>Dear ' . $user_data['first_name'] . ' ' . $user_data['last_name'] . ',</p>
        <p>We received a request to reset your password. You can reset your password by clicking the link below:</p>
        <p><a href="' . $reset_link . '">Reset Your Password</a></p>
        <p>If you didn’t request a password reset, you can safely ignore this email.</p>
        <p>For security purposes, this link will expire in 24 hours.</p>
        <p>Thank you!</p>
    </body>
    </html>';
}