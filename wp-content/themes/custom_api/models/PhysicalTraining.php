<?php

/*
|--------------------------------------------------------------------------
| PhysicalTraining.php
|--------------------------------------------------------------------------
| Model for handling physical training registration.
*/

class PhysicalTraining {

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $training_name;
    public $amount;
    public $message;
    public $created_at;

    public function __construct($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_physical_training_data();
        }
    }

    // Load physical training data from the database
    private function load_physical_training_data() {
        global $wpdb;
        $table = $wpdb->prefix . "physical_trainings";
        $training = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $this->id));

        if ($training) {
            $this->first_name = $training->first_name;
            $this->last_name = $training->last_name;
            $this->email = $training->email;
            $this->training_name = $training->training_name;
            $this->amount = $training->amount;
            $this->message = $training->message;
            $this->created_at = $training->created_at;
        }
    }

    // Save physical training data to the database
    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . "physical_trainings";

        if ($this->id) {
            // Update existing training
            $wpdb->update(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'training_name' => $this->training_name,
                    'amount' => $this->amount,
                    'message' => $this->message,
                    'created_at' => $this->created_at,
                ],
                ['id' => $this->id]
            );
        } else {
            // Insert new training
            $wpdb->insert(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'training_name' => $this->training_name,
                    'amount' => $this->amount,
                    'message' => $this->message,
                    'created_at' => current_time('mysql'),
                ]
            );
            $this->id = $wpdb->insert_id;
        }
    }
}