<?php
    
    function connect() {


        $host = "localhost";
        $user = "root";
        $pass = "root";
        $db = "db_oficina";
        try {
            $con = new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
            
        } catch(PDOException $e) {
            echo "Erro encontrado: " . $e->getMessage();
            exit;
        } 
    }

    $db = connect();
    
