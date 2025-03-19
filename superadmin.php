<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    header('Location: index.php');
    exit();
}

require_once 'bdd.php';

$message = '';
$error = '';
$users = [];

function getStatusBadge($isAdmin, $isSuperAdmin) {
    if ($isSuperAdmin) {
        return '<span class="badge badge-danger">Super Admin</span>';
    } elseif ($isAdmin) {
        return '<span class="badge badge-warning">Admin</span>';
    } else {
        return '<span class="badge badge-secondary">Utilisateur</span>';
    }
}

try {
    $stmt = $connexion->prepare("SELECT id, pseudo, email, admin, is_superadmin, creea FROM user ORDER BY creea DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $isAdmin = isset($_POST['admin']) ? 1 : 0;
    $isSuperAdmin = isset($_POST['is_superadmin']) ? 1 : 0;
    
    if (empty($pseudo) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email utilisateur invalide";
    } else {
        try {
            $check = $connexion->prepare("SELECT id FROM user WHERE email = :email OR pseudo = :pseudo");
            $check->bindParam(':email', $email);
            $check->bindParam(':pseudo', $pseudo);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                $error = "Cet utilisateur existe déjà (email ou pseudo déjà utilisé)";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $insert = $connexion->prepare("INSERT INTO user (pseudo, email, password, admin, is_superadmin) VALUES (:pseudo, :email, :password, :admin, :is_superadmin)");
                $insert->bindParam(':pseudo', $pseudo);
                $insert->bindParam(':email', $email);
                $insert->bindParam(':password', $hashedPassword);
                $insert->bindParam(':admin', $isAdmin);
                $insert->bindParam(':is_superadmin', $isSuperAdmin);
                $insert->execute();
                
                $message = "Utilisateur créé avec succès!";
                
                $stmt = $connexion->prepare("SELECT id, pseudo, email, admin, is_superadmin, creea FROM user ORDER BY creea DESC");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la création de l'utilisateur: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $userId = $_POST['user_id'];
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $isAdmin = isset($_POST['admin']) ? 1 : 0;
    $isSuperAdmin = isset($_POST['is_superadmin']) ? 1 : 0;
    $password = $_POST['password'];
    
    if (empty($pseudo) || empty($email)) {
        $error = "Les champs pseudo et email sont obligatoires";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide";
    } else {
        try {
            $check = $connexion->prepare("SELECT id FROM user WHERE (email = :email OR pseudo = :pseudo) AND id != :id");
            $check->bindParam(':email', $email);
            $check->bindParam(':pseudo', $pseudo);
            $check->bindParam(':id', $userId);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                $error = "Cet email ou pseudo est déjà utilisé";
            } else {
                if (empty($password)) {
                    $update = $connexion->prepare("UPDATE user SET pseudo = :pseudo, email = :email, admin = :admin, is_superadmin = :is_superadmin WHERE id = :id");
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $update = $connexion->prepare("UPDATE user SET pseudo = :pseudo, email = :email, password = :password, admin = :admin, is_superadmin = :is_superadmin WHERE id = :id");
                    $update->bindParam(':password', $hashedPassword);
                }
                
                $update->bindParam(':pseudo', $pseudo);
                $update->bindParam(':email', $email);
                $update->bindParam(':admin', $isAdmin);
                $update->bindParam(':is_superadmin', $isSuperAdmin);
                $update->bindParam(':id', $userId);
                $update->execute();
                
                $message = "Utilisateur mis à jour avec succès!";
                
                $stmt = $connexion->prepare("SELECT id, pseudo, email, admin, is_superadmin, creea FROM user ORDER BY creea DESC");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $userId = $_POST['user_id'];
    
    if ($userId == $_SESSION['user_id']) {
        $error = "Vous ne pouvez pas supprimer votre propre compte";
    } else {
        try {
            $delete = $connexion->prepare("DELETE FROM user WHERE id = :id");
            $delete->bindParam(':id', $userId);
            $delete->execute();
            
            $message = "Utilisateur supprimé avec succès!";
            
            $stmt = $connexion->prepare("SELECT id, pseudo, email, admin, is_superadmin, creea FROM user ORDER BY creea DESC");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Erreur lors de la suppression de l'utilisateur: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clichy | Gestion des comptes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Gestion des comptes utilisateurs</h1>
        
        <div class="back-to-site">
            <a href="index.php" class="btn btn-outline-secondary">← Retour au site</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="mt-4 mb-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                <i class="fas fa-plus"></i> Ajouter un utilisateur
            </button>
        </div>
        
        <div class="user-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['pseudo']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo getStatusBadge($user['admin'], $user['is_superadmin']); ?></td>
                            <td><?php echo isset($user['creea']) ? $user['creea'] : 'N/A'; ?></td>
                            <td class="actions">
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editUserModal<?php echo $user['id']; ?>">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteUserModal<?php echo $user['id']; ?>">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel<?php echo $user['id']; ?>">Modifier l'utilisateur</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label for="pseudo<?php echo $user['id']; ?>">Pseudo</label>
                                                <input type="text" class="form-control" id="pseudo<?php echo $user['id']; ?>" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="email<?php echo $user['id']; ?>">Email</label>
                                                <input type="email" class="form-control" id="email<?php echo $user['id']; ?>" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="password<?php echo $user['id']; ?>">Mot de passe (laisser vide pour ne pas modifier)</label>
                                                <input type="password" class="form-control" id="password<?php echo $user['id']; ?>" name="password">
                                            </div>
                                            
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="admin<?php echo $user['id']; ?>" name="admin" <?php echo $user['admin'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="admin<?php echo $user['id']; ?>">Administrateur</label>
                                            </div>
                                            
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_superadmin<?php echo $user['id']; ?>" name="is_superadmin" <?php echo $user['is_superadmin'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_superadmin<?php echo $user['id']; ?>">Super Administrateur</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal fade" id="deleteUserModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteUserModalLabel<?php echo $user['id']; ?>">Confirmer la suppression</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer l'utilisateur <strong><?php echo htmlspecialchars($user['pseudo']); ?></strong> ?
                                        <br>
                                        Cette action est irréversible.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Ajouter un utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="form-group">
                                <label for="pseudo_new">Pseudo</label>
                                <input type="text" class="form-control" id="pseudo_new" name="pseudo" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email_new">Email</label>
                                <input type="email" class="form-control" id="email_new" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_new">Mot de passe</label>
                                <input type="password" class="form-control" id="password_new" name="password" required>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="admin_new" name="admin">
                                <label class="form-check-label" for="admin_new">Administrateur</label>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_superadmin_new" name="is_superadmin">
                                <label class="form-check-label" for="is_superadmin_new">Super Administrateur</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
