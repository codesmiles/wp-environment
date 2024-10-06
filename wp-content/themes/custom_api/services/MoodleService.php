<?php

/*
|--------------------------------------------------------------------------
| MoodleService.php
|--------------------------------------------------------------------------
| Handles communication with the Moodle API.
*/

class MoodleService
{
    private $moodle_url;
    private $token;

    public function __construct()
    {
        $this->moodle_url = 'https://cloudixtraining.com/moodle/webservice/rest/server.php'; 
        $this->token = 'moodle_token'; 
    }

    /**
     * Create a new user in Moodle.
     *
     * @param array $userData User data
     * @return array Response from Moodle API.
     */
    public function createUser($userData)
    {
        $data = [
            'wstoken' => $this->token,
            'wsfunction' => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users' => [$userData]
        ];

        return $this->sendRequest($data);
    }

    /**
     * Enroll a user in a course.
     *
     * @param int $userId User ID in Moodle.
     * @param int $courseId Course ID in Moodle.
     * @return array Response from Moodle API.
     */
    public function enrollUserInCourse($userId, $courseId)
    {
        $data = [
            'wstoken' => $this->token,
            'wsfunction' => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments' => [
                [
                    'roleid' => 5, // Student role ID
                    'userid' => $userId,
                    'courseid' => $courseId
                ]
            ]
        ];

        return $this->sendRequest($data);
    }

    /**
     * Send an HTTP request to the specified Moodle API endpoint.
     *
     * @param array $data Data to be sent to Moodle.
     * @return array Response from the Moodle API.
     */
    private function sendRequest($data)
    {
        $curl = curl_init($this->moodle_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}