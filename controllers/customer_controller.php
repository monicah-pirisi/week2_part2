<?php
/**
 * Customer Controller
 * Handles all customer-related operations
 */

require_once __DIR__ . '/../classes/customer_class.php';

/**
 * Login customer
 * @param string $email Customer email
 * @param string $password Customer password
 * @return array Response with status and user data or error message
 */
function login_customer_ctr($email, $password)
{
    try {
        $customer = new Customer();
        $result = $customer->loginCustomer($email, $password);
        
        if (isset($result['status']) && $result['status'] === 'success') {
            return array(
                'status' => 'success',
                'user_id' => isset($result['user_id']) ? $result['user_id'] : null,
                'user_name' => isset($result['user_name']) ? $result['user_name'] : '',
                'user_email' => isset($result['user_email']) ? $result['user_email'] : $email,
                'user_role' => isset($result['user_role']) ? (int)$result['user_role'] : 0,
                'user_country' => isset($result['user_country']) ? $result['user_country'] : '',
                'user_city' => isset($result['user_city']) ? $result['user_city'] : '',
                'user_phone' => isset($result['user_phone']) ? $result['user_phone'] : ''
            );
        } else {
            return array(
                'status' => 'error',
                'message' => isset($result['message']) ? $result['message'] : 'Invalid email or password'
            );
        }
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'message' => 'An error occurred during login'
        );
    }
}

/**
 * Get customer by email
 * @param string $email Customer email
 * @return array|false Customer data or false if not found
 */
function get_customer_by_email_ctr($email)
{
    try {
        if (empty($email)) {
            return false;
        }
        
        $customer = new Customer();
        return $customer->getCustomerByEmail($email);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Register new customer
 * @param string $name Customer name
 * @param string $email Customer email
 * @param string $password Customer password
 * @param string $phone_number Customer phone number
 * @param string $country Customer country
 * @param string $city Customer city
 * @param int $role User role (0=customer, 1=admin, 2=restaurant owner)
 * @return int|false Customer ID on success, false on failure
 */
function register_customer_ctr($name, $email, $password, $phone_number, $country, $city, $role)
{
    try {
        // Validate inputs
        if (empty($name) || empty($email) || empty($password)) {
            return false;
        }
        
        $customer = new Customer();
        $customer_id = $customer->createCustomer($name, $email, $password, $phone_number, $country, $city, $role);
        
        if ($customer_id && is_numeric($customer_id) && $customer_id > 0) {
            return (int)$customer_id;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get customer by ID
 * @param int $customer_id Customer ID
 * @return array|false Customer data or false if not found
 */
function get_customer_by_id_ctr($customer_id)
{
    try {
        if (empty($customer_id) || !is_numeric($customer_id)) {
            return false;
        }
        
        $customer = new Customer();
        return $customer->getCustomerById($customer_id);
    } catch (Exception $e) {

        return false;
    }
}

/**
 * Update customer information
 * @param int $customer_id Customer ID
 * @param array $data Customer data to update
 * @return bool True on success, false on failure
 */
function update_customer_ctr($customer_id, $data)
{
    try {
        if (empty($customer_id) || !is_numeric($customer_id)) {
            return false;
        }
        
        $customer = new Customer();
        return $customer->updateCustomer($customer_id, $data);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Delete customer
 * @param int $customer_id Customer ID
 * @return bool True on success, false on failure
 */
function delete_customer_ctr($customer_id)
{
    try {
        if (empty($customer_id) || !is_numeric($customer_id)) {
            return false;
        }
        
        $customer = new Customer();
        return $customer->deleteCustomer($customer_id);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get all customers (Admin function)
 * @return array|false Array of customers or false on failure
 */
function get_all_customers_ctr()
{
    try {
        $customer = new Customer();
        return $customer->getAllCustomers();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Change customer password
 * @param int $customer_id Customer ID
 * @param string $old_password Old password for verification
 * @param string $new_password New password
 * @return bool True on success, false on failure
 */
function change_password_ctr($customer_id, $old_password, $new_password)
{
    try {
        if (empty($customer_id) || empty($old_password) || empty($new_password)) {
            return false;
        }
        
        $customer = new Customer();
        return $customer->changePassword($customer_id, $old_password, $new_password);
    } catch (Exception $e) {
        return false;
    }
}
?>