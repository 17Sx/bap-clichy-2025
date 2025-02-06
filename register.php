<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="pseudo">Username:</label>
        <input type="text" id="pseudo" name="pseudo" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Register">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($pseudo) || empty($email) || empty($password)) {
            echo "All fields are required.";
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn = new PDO("mysql:host=localhost;dbname=RenduPhpCRUD", 'root', '');

        $sql = "INSERT INTO user (pseudo, email, password) VALUES (:pseudo, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
    ?>
</body>
</html>