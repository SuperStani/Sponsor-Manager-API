<?php


namespace SponsorAPI\Core\ORM;


class DB
{
    private ?\PDO $instance;

    private string $host;

    private int $port;

    private string $username;

    private string $password;

    private ?string $selectedDB;

    public function __construct(string $host, int $port, string $username, string $password, ?string $database = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->selectedDB = $database;
        $this->instance = null;
    }

    private function initialize(): ?\PDO
    {
        if ($this->instance === null) {
            try {
                $this->instance = new \PDO("mysql:host=$this->host;port=$this->port;dbname=$this->selectedDB", $this->username, $this->password);
            } catch (\PDOException $e) {
                return null;
            }
        }
        return $this->instance;
    }

    public function query($stmtQuery, ...$args): ?\PDOStatement
    {
        $conn = $this->initialize();
        if ($conn !== null) {
            $stmt = $conn->prepare($stmtQuery);
            foreach ($args as $key => &$value) {
                $key = $key + 1;
                if (is_numeric($value)) {
                    $stmt->bindParam($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($key, $value);
                }
            }
            if ($stmt->execute()) {
                return $stmt;
            }
        }
        return null;
    }

    public function getLastInsertId(): ?string
    {
        $conn = $this->initialize();
        if($conn !== null) {
            return $conn->lastInsertId();
        }
        return null;
    }
}