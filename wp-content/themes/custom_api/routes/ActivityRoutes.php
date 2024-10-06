<?php

/*
|--------------------------------------------------------------------------
| ActivityRoutes.php
|--------------------------------------------------------------------------
| Manages routes for user activities such as lab rental, course purchases, and trainings.
*/

/*
|--------------------------------------------------------------------------
| imports
|--------------------------------------------------------------------------
*/
require_once(dirname(__FILE__) . '/../controllers/LabRentalController.php');
require_once(dirname(__FILE__) . '/../controllers/VendorCourseController.php');
require_once(dirname(__FILE__) . '/../controllers/PhysicalTrainingController.php');
require_once(dirname(__FILE__) . '/../controllers/OnlineCourseController.php');

$activity_route_prefix = "v1/api/activity";

// instantiate the controller
$lab_controller = new LabRentalController();
$vendor_controller = new VendorCoursesController();
$physical_training_controller = new PhysicalTrainingController();
$online_course_controller = new OnlineCourseController();


/*
|--------------------------------------------------------------------------
| Lab Rental endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use($activity_route_prefix, $lab_controller) {
    register_rest_route($activity_route_prefix, '/lab-rental', [
        'methods' => 'POST',
        'callback' => [$lab_controller, 'rent_lab'], 
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Vendor Course Purchase endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($activity_route_prefix, $vendor_controller) {
    register_rest_route($activity_route_prefix, '/vendor-course', [
        'methods' => 'POST',
        'callback' => [$vendor_controller, 'purchase_vendor_course'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Physical Training Purchase endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($activity_route_prefix,$physical_training_controller) {
    register_rest_route($activity_route_prefix, '/physical-training', [
        'methods' => 'POST',
        'callback' => [$physical_training_controller, 'register_training'],
        'permission_callback' => '__return_true',
    ]);
});

/*
|--------------------------------------------------------------------------
| Online Course Purchase endpoint
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () use ($activity_route_prefix,$online_course_controller) {
    register_rest_route($activity_route_prefix, '/online-course', [
        'methods' => 'POST',
        'callback' => [$online_course_controller, 'purchase_online_course'],
        'permission_callback' => '__return_true',
    ]);
});