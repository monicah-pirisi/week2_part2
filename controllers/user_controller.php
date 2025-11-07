<?php

require_once __DIR__ . '/../classes/customer_class.php';

/**
 * Register a new user
 * @param string $name
 * @param string $email
 * @param string $password
 * @param string $phone_number
 * @param string $country
 * @param string $city
 * @param int $role
 * @return int|false User ID on success, false on failure
 */
function register_user_ctr($name, $email, $password, $phone_number, $country, $city, $role)
{
    $customer = new Customer();
    $user_id = $customer->createCustomer($name, $email, $password, $phone_number, $country, $city, $role);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

/**
 * Get user by email address
 * @param string $email
 * @return array|false User data on success, false on failure
 */
function get_user_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

/**
 * Login customer with email and password
 * @param string $email
 * @param string $password
 * @return array Response array with status and user data
 */
function login_customer_ctr($email, $password)
{
    try {
        $customer = new Customer();
        $result = $customer->loginCustomer($email, $password);
        return $result;
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'message' => 'An error occurred during login'
        );
    }
}