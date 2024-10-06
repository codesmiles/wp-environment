<?php

/*
|--------------------------------------------------------------------------
| AuthenticationMiddleware.php
|--------------------------------------------------------------------------
| Middleware to check for user authentication for protected routes.
*/

// Include the JWT library
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class AuthenticationMiddleware
{
    // Secret key for JWT signing and verification
    private static $secret_key = 'jshsjsuuwissisnsiwiqoi092929828'; 
    // Check if the request is authenticated
    public static function handle($request)
    {
        // Check if a valid authentication token exists in the request headers
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        if (!$auth_header) {
            return new WP_REST_Response(
                [
                    'error' => true,
                    'message' => 'Unauthorized: Missing authentication token.',
                    'data' => []
                ],
                401 // HTTP status code for Unauthorized
            );
        }

        // Extract token from the "Bearer" string
        $token_parts = explode(' ', $auth_header);
        if (count($token_parts) != 2 || $token_parts[0] != 'Bearer') {
            return new WP_REST_Response(
                [
                    'error' => true,
                    'message' => 'Unauthorized: Invalid authentication format.',
                    'data' => []
                ],
                401
            );
        }

        $token = $token_parts[1];

        // Verify the token using JWT
        if (!self::verify_token($token)) {
            return new WP_REST_Response(
                [
                    'error' => true,
                    'message' => 'Unauthorized: Invalid or expired token.',
                    'data' => []
                ],
                401
            );
        }

        // If the token is valid, continue the request
        return true;
    }

    // Token verification logic using JWT
    private static function verify_token($token)
    {
        try {
            // Decode the token using the secret key
            $decoded = JWT::decode($token, self::$secret_key, ['HS256']);
            // You can access user information from $decoded
            return true;
        } catch (ExpiredException $e) {
            return false; // Token has expired
        } catch (Exception $e) {
            return false; // Token is invalid
        }
    }
}