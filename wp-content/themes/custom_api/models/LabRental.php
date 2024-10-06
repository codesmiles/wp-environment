<?php

/*
|--------------------------------------------------------------------------
| LabRental.php
|--------------------------------------------------------------------------
| Model for handling lab rental requests.
*/

class LabRental {

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $course_name;
    public $duration;
    public $message;
    public $created_at;

    public function __construct($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_lab_rental_data();
        }
    }

    // Load lab rental data from the database
    private function load_lab_rental_data() {
        global $wpdb;
        $table = $wpdb->prefix . "lab_rentals";
        $rental = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $this->id));

        if ($rental) {
            $this->first_name = $rental->first_name;
            $this->last_name = $rental->last_name;
            $this->email = $rental->email;
            $this->course_name = $rental->course_name;
            $this->duration = $rental->duration;
            $this->message = $rental->message;
            $this->created_at = $rental->created_at;
        }
    }

    // Save lab rental data to the database
    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . "lab_rentals";

        if ($this->id) {
            // Update existing rental
            $wpdb->update(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'course_name' => $this->course_name,
                    'duration' => $this->duration,
                    'message' => $this->message,
                    'created_at' => $this->created_at,
                ],
                ['id' => $this->id]
            );
        } else {
            // Insert new rental
            $wpdb->insert(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'course_name' => $this->course_name,
                    'duration' => $this->duration,
                    'message' => $this->message,
                    'created_at' => current_time('mysql'),
                ]
            );
            $this->id = $wpdb->insert_id;
        }
    }
}