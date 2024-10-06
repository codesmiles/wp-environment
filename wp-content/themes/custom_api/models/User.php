<?php

/*
|--------------------------------------------------------------------------
| User.php
|--------------------------------------------------------------------------
| Model for managing users in the system.
*/

class User {

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $email_verified;
    public $verification_code;
    public $terms_accepted;
    public $newsletter_subscribed;
    public $created_at;

    public function __construct($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_user_data();
        }
    }

    // Load user data from the database
    private function load_user_data() {
        global $wpdb;
        $table = $wpdb->prefix . "users";
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $this->id));

        if ($user) {
            $this->first_name = $user->first_name;
            $this->last_name = $user->last_name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->password = $user->password;
            $this->email_verified = $user->email_verified;
            $this->verification_code = $user->verification_code;
            $this->terms_accepted = $user->terms_accepted; // Load new property
            $this->newsletter_subscribed = $user->newsletter_subscribed; // Load new property
            $this->created_at = $user->created_at;
        }
    }

    // Save user data to the database
    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . "users";

        if ($this->id) {
            // Update existing user
            $wpdb->update(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => $this->password,
                    'email_verified' => $this->email_verified,
                    'verification_code' => $this->verification_code,
                    'terms_accepted' => $this->terms_accepted, // Save new property
                    'newsletter_subscribed' => $this->newsletter_subscribed, // Save new property
                    'created_at' => $this->created_at,
                ],
                ['id' => $this->id]
            );
        } else {
            // Insert new user
            $wpdb->insert(
                $table,
                [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => $this->password,
                    'email_verified' => $this->email_verified,
                    'verification_code' => $this->verification_code,
                    'terms_accepted' => $this->terms_accepted,
                    'newsletter_subscribed' => $this->newsletter_subscribed,
                    'created_at' => current_time('mysql'),
                ]
            );
            $this->id = $wpdb->insert_id;
        }
    }

    // Find a user by email
    public static function find_by_email($email) {
        global $wpdb;
        $table = $wpdb->prefix . "users";
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email));

        return $user ? new User($user->id) : null;
    }

    // Verify email using a verification code
    public static function verify_email($email, $code) {
        $user = self::find_by_email($email);
        if ($user && $user->verification_code == $code) {
            $user->email_verified = 1;
            $user->verification_code = null;
            $user->save();
            return true;
        }
        return false;
    }
}