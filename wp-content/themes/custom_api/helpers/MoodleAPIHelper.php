<?php

/*
|--------------------------------------------------------------------------
| MoodleAPIHelper.php
|--------------------------------------------------------------------------
| Helper for integrating with Moodle REST API.
*/

$MOODLE_API_URL = 'https://cloudixtraining.com/webservice/rest/server.php';
$MOODLE_API_TOKEN = 'your_moodle_api_token';

/**
 * Enrolls a user in a Moodle course.
 * 
 * @param string $email - User email address.
 * @param string $first_name - First name of the user.
 * @param string $last_name - Last name of the user.
 * @param int $course_id - Moodle course ID.
 * @return bool - True if the user was enrolled successfully, false otherwise.
 */
function enroll_user_in_moodle($email, $first_name, $last_name, $course_id) {
    global $MOODLE_API_URL, $MOODLE_API_TOKEN;

    $user_data = [
        'wstoken' => $MOODLE_API_TOKEN,
        'wsfunction' => 'core_user_create_users',
        'moodlewsrestformat' => 'json',
        'users' => [
            [
                'username' => $email,
                'password' => wp_generate_password(12, false),
                'firstname' => $first_name,
                'lastname' => $last_name,
                'email' => $email,
                'auth' => 'manual'
            ]
        ]
    ];

    // Make a request to Moodle API
    $response = wp_remote_post($MOODLE_API_URL, ['body' => $user_data]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    // response contains 'success' field for success check
    return isset($result['success']) && $result['success'];
}

/**
 * Retrieves a Moodle course by ID.
 * 
 * @param int $course_id - The Moodle course ID.
 * @return array - Course details.
 */
function get_moodle_course_by_id($course_id) {
    global $MOODLE_API_URL, $MOODLE_API_TOKEN;

    $params = [
        'wstoken' => $MOODLE_API_TOKEN,
        'wsfunction' => 'core_course_get_courses',
        'moodlewsrestformat' => 'json',
        'criteria' => [['key' => 'id', 'value' => $course_id]]
    ];

    // Make request to Moodle API
    $response = wp_remote_post($MOODLE_API_URL, ['body' => $params]);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}