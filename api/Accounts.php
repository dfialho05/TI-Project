<?php

class Account {
    private $username;
    private $password;
    private $permission = "user"; //user or root or viewer

    public function __construct(string $username, string $password) { //constructor
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string { //getter for username
        return $this->username;
    }

    public function getPermission(): string { //getter for permission
        return $this->permission;
    }

    

    public function setPermission(string $permission): void { //setter for permission
        $this->permission = $permission;
    }
}

?>