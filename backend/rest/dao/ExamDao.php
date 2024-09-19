<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      $host = "localhost";
      $username = "root1";
      $password = "";
      $dbname = "september-final";
      $port = 3306;

      $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);

      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function login($email) {
    try {
      $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      return $user;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return null;
    }
  }

  public function film_performance_report() {
    try {
      $stmt = $this->conn->prepare("
      SELECT c.category_id AS id, c.name, COUNT(f.film_id) AS total
      FROM category c
      LEFT JOIN film_category fc ON c.category_id = fc.category_id
      LEFT JOIN film f ON fc.film_id = f.film_id
      GROUP BY c.category_id, c.name
      ");
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
  }
  }

  public function delete_film($film_id) {
    try {
      $stmt = $this->conn->prepare("DELETE FROM film WHERE film_id = :film_id");
      $stmt->bindParam(':film_id', $film_id);
      $stmt->execute();
      return "Film deleted successfully";
  } catch (PDOException $e) {
      return "Error: " . $e->getMessage();
  }
  }

  public function edit_film($film_id, $data) {
    try {
      $stmt = $this->conn->prepare("
          UPDATE film 
          SET title = :title, description = :description, release_year = :release_year 
          WHERE film_id = :film_id
      ");
      $stmt->bindParam(':title', $data['title']);
      $stmt->bindParam(':description', $data['description']);
      $stmt->bindParam(':release_year', $data['release_year']);
      $stmt->bindParam(':film_id', $film_id);
      $stmt->execute();

      // Fetch the updated film
      $stmt = $this->conn->prepare("SELECT * FROM film WHERE film_id = :film_id");
      $stmt->bindParam(':film_id', $film_id);
      $stmt->execute();
      $updatedFilm = $stmt->fetch(PDO::FETCH_ASSOC);

      return $updatedFilm;
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return null;
  }
  }

  public function get_customers_report() {
    try {
      $offset = ($page - 1) * $pageSize;

      $stmt = $this->conn->prepare("
          SELECT 
              CONCAT(c.first_name, ' ', c.last_name) AS customer_full_name,
              SUM(p.amount) AS total_amount,
              CONCAT('<div>', c.first_name, ' ', c.last_name, '</div>') AS details
          FROM customer c
          JOIN rental r ON c.customer_id = r.customer_id
          JOIN payment p ON r.rental_id = p.rental_id
          GROUP BY c.customer_id
          LIMIT :limit OFFSET :offset
      ");
      $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
      $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
  }
  }

  public function get_customer_rental_details($customer_id) {
    try {
      $stmt = $this->conn->prepare("
          SELECT 
              r.rental_date,
              f.title AS film_title,
              p.amount AS payment_amount
          FROM rental r
          JOIN inventory i ON r.inventory_id = i.inventory_id
          JOIN film f ON i.film_id = f.film_id
          JOIN payment p ON r.rental_id = p.rental_id
          WHERE r.customer_id = :customer_id
      ");
      $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
  }
  }

}
