<?php
        session_start();

        // Check if user is logged in and is a superadmin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
            header('Location: index.php');
            exit();
        }

        $dsn = 'mysql:host=localhost;dbname=renduphpcrud;charset=utf8';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Reset all claims
            $stmt = $pdo->prepare("UPDATE message SET is_claim = 0");
            $stmt->execute();

            $_SESSION['message'] = "Tous les claims ont été réinitialisés avec succès.";
            header('Location: index.php');
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la réinitialisation: " . $e->getMessage();
            header('Location: index.php');
            exit();
        }
        ?>