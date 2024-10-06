<?php

/*
|--------------------------------------------------------------------------
| ResponseHelper.php
|--------------------------------------------------------------------------
*/

$validation_message = "validation_error";
$failed_message = "failed_request_error";
$success_message = "request_successful";
$payload_mismatch_message = "payload_mismatch_error";

/**
 * For sending API responses.
 */
function sendResponse(array $payload): array {
    return [
        'error' => $payload['error'] ?? true,
        'message' => $payload['message'] ?? $failed_message,
        'data' => $payload['data'] ?? [],
    ];
}