<?php

/*
|--------------------------------------------------------------------------
| OnlineCourse.php
|--------------------------------------------------------------------------
| Model for handling online course payments and registration.
*/

class OnlineCourse {

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $course_name;
    public $amount;
    public $created_at;

    public function __construct($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_online_course_data();
        }
    }

    // Load online course data from the database
    private function load_online_course_data() {
        global $wpdb;
        $table = $wpdb->prefix . "online_courses";
        $course = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $this->id));

        if ($course) {
            $this->first_name = $course->first_name;
            $this->last_name = $course->last_name;
            $this->email = $course->email;
            $this->course_name = $course->course_name;
            $this->amount = $course->amount;
            $this->created_at = $course->created_at;
        }
    }

    // Save online course data to the database
    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . "online_courses";

        if ($this->id) {
            // Update existing course
            $wpdb->update(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'course_name' => $this->course_name,
                    'amount' => $this->amount,
                    'created_at' => $this->created_at,
                ],
                ['id' => $this->id]
            );
        } else {
            // Insert new course
            $wpdb->insert(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'course_name' => $this->course_name,
                    'amount' => $this->amount,
                    'created_at' => current_time('mysql'),
                ]
            );
            $this->id = $wpdb->insert_id;
        }
    }
}