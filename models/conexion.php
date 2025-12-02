<?php
    session_start();
    class Conexion{
        public function get_conexion(){
            $user = "root";
            $pass = "";
            $host = "localhost:8080";
            $dbname = "alquiler_bicicletas";

            $conexion = new PDO("mysql: host=$host; dbname=$dbname",$user,$pass);
            return $conexion;

        }
    }

?>