<?php
    
    function connect() {
        // $host = "db_qrcode_test.mysql.dbaas.com.br";
        // $user = "db_qrcode_test";
        // $pass = "P@ulO#675514!$";
        // $db = "db_qrcode_test";


        $host = "localhost";
        $user = "root";
        $pass = "root";
        $db = "db_perguntas_qr";
        // db_schindler, P@ulO#675514!$
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
    
