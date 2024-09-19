<?php
require_once __DIR__ . "/../dao/ExamDao.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ExamService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new ExamDao();
    }

    public function login($email, $password) {
        $jwt_secret = 'secret';
        $examDao = new ExamDao();
        $user = $examDao->login($email);
    
        if ($user && $user['password'] === $password) {
            
            unset($user['password']);
            $jwt_payload = [
                'user' => $user,
                'iat' => time(),
                'exp' => time() + (60*60*24),
    
    
            ];

            $jwt = JWT::encode($jwt_payload, $jwt_secret, "HS256");
    
            return [
                'data' => [
                    'message' => 'Login successful',
                    'token' => $jwt,
                    'user' => $user
                ],
                'status' => 200
            ];
        } else {
            return [
                'data' => [
                    'message' => 'Invalid email or password'
                ],
                'status' => 401
            ];
        }
    }

    public function film_performance_report() {
        $examDao = new ExamDao();
        return $examDao->film_performance_report();
    }

    public function delete_film($film_id) {
        $examDao = new ExamDao();
        return $examDao->delete_film($film_id);
    }

    public function edit_film($film_id, $data) {
        $examDao = new ExamDao();
        return $examDao->edit_film($film_id, $data);
    }

    public function get_customers_report() {
        $examDao = new ExamDao();
        return $customerDao->get_customers_report($page, $pageSize);
    }

    public function get_customer_rental_details($customer_id) {
        $examDao = new ExamDao();
        return $examDao->get_customer_rental_details($customer_id);
    }
}
