<?php

require_once(dirname(__FILE__) . '/../controllers/AuthController.php');

$auth_route_prefix = "v1/api/auth";

// Create an instance of AuthController
$auth_controller = new AuthController();

/*
|--------------------------------------------------------------------------
| Register endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/register', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'register'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Login endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/login', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'login'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Resend verification code endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/resend-verification', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'resend_verification_code'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Verify email endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/verify-email', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'verify_email'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Forgot password endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/forgot-password', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'forgot_password'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Reset password endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/reset-password', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'reset_password'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Delete user endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($auth_route_prefix, $auth_controller) {
    register_rest_route($auth_route_prefix, '/delete-user', [
        'methods' => 'POST',
        'callback' => [$auth_controller, 'delete_user'],
        'permission_callback' => '__return_true',
    ]);
});