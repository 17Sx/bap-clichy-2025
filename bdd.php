<?php
    try {
        $connexion = new PDO('mysql:host=localhost;dbname=iim_a2_securite', 'root');
    } catch (Exception $e) {
        die($e->getMessage());
    }
?>