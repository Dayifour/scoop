<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('location: index.php');
    exit;
}

// Connexion à la base de données
$bd = new PDO('mysql:host=localhost;dbname=scoopbd', 'root', '');
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'utilisateur
$stmt = $bd->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION["id"], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    header('location: index.php');
    exit;
}

$isAdmin = ($user['role'] === 'Admin');

// Gestion des actions admin
if ($isAdmin && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    try {
        switch($action) {
            case 'delete_user':
                if (isset($_GET['id']) && $_GET['id'] != $_SESSION["id"]) {
                    $userId = (int)$_GET['id'];
                    
                    // Journalisation
                    $stmt = $bd->prepare("INSERT INTO admin_logs (admin_id, action_type, target_id, description) 
                                        VALUES (:admin_id, 'delete_user', :target_id, :description)");
                    $stmt->bindValue(':admin_id', $_SESSION["id"], PDO::PARAM_INT);
                    $stmt->bindValue(':target_id', $userId, PDO::PARAM_INT);
                    $description = "Suppression de l'utilisateur ID $userId";
                    $stmt->bindValue(':description', $description);
                    $stmt->execute();
                    
                    // Suppression
                    $stmt = $bd->prepare("DELETE FROM users WHERE id = :id");
                    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    header("Location: vente.php?success=user_deleted");
                    exit;
                }
                break;
                
            case 'update_role':
                if (isset($_POST['user_id']) && isset($_POST['new_role']) && $_POST['user_id'] != $_SESSION["id"]) {
                    $userId = (int)$_POST['user_id'];
                    $newRole = in_array($_POST['new_role'], ['Admin', 'Client']) ? $_POST['new_role'] : 'Client';
                    
                    // Journalisation
                    $stmt = $bd->prepare("INSERT INTO admin_logs (admin_id, action_type, target_id, description) 
                                        VALUES (:admin_id, 'update_role', :target_id, :description)");
                    $stmt->bindValue(':admin_id', $_SESSION["id"], PDO::PARAM_INT);
                    $stmt->bindValue(':target_id', $userId, PDO::PARAM_INT);
                    $description = "Changement de rôle pour l'utilisateur ID $userId en $newRole";
                    $stmt->bindValue(':description', $description);
                    $stmt->execute();
                    
                    // Mise à jour
                    $stmt = $bd->prepare("UPDATE users SET role = :role WHERE id = :id");
                    $stmt->bindValue(':role', $newRole);
                    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    header("Location: vente.php?success=role_updated");
                    exit;
                }
                break;
        }
    } catch(PDOException $e) {
        die("Erreur lors de l'action admin: " . $e->getMessage());
    }
}

// Récupération des données
if ($isAdmin) {
    $stmt = $bd->prepare("SELECT p.*, u.nom as vendeur_nom, u.prenom as vendeur_prenom 
                         FROM produit p JOIN users u ON p.id_vendeur = u.id 
                         ORDER BY p.date_creation DESC");
} else {
    $stmt = $bd->prepare("SELECT * FROM produit WHERE id_vendeur = :id_vendeur ORDER BY date_creation DESC");
    $stmt->bindParam(':id_vendeur', $_SESSION["id"], PDO::PARAM_INT);
}
$stmt->execute();
$produits = $stmt->fetchAll();

// Récupération des utilisateurs et historique si admin
$users = [];
$historique = [];
if ($isAdmin) {
    $stmt = $bd->prepare("SELECT * FROM users WHERE id != :current_id ORDER BY id DESC");
    $stmt->bindValue(':current_id', $_SESSION["id"], PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    $stmt = $bd->prepare("SELECT l.*, u.prenom, u.nom 
                         FROM admin_logs l 
                         JOIN users u ON l.admin_id = u.id 
                         ORDER BY l.action_date DESC LIMIT 50");
    $stmt->execute();
    $historique = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? 'Administration' : 'Mes activités' ?> - Scoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B00;
            --primary-light: #FF8C42;
            --secondary: #2C3E50;
            --light: #F8F9FA;
            --dark: #212529;
            --white: #FFFFFF;
            --gray: #6C757D;
            --border: #E9ECEF;
            --success: #28A745;
            --danger: #DC3545;
            --info: #17A2B8;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--secondary);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 80px;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: var(--white);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            height: 80px;
            display: flex;
            align-items: center;
        }

        .header-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 2rem;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            height: 80px;
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            padding: 0.5rem 0;
            transition: var(--transition);
        }

        .nav-links a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary);
            bottom: 0;
            left: 0;
            transition: var(--transition);
        }

        .nav-links a:hover:after,
        .nav-links a.active:after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .admin-container {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .admin-header h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-badge {
            background-color: var(--primary);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.15);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .admin-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Styles pour les produits */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-details {
            padding: 1.5rem;
        }

        .product-details h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }

        .price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-view {
            background-color: var(--white);
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-view:hover {
            background-color: rgba(255, 107, 0, 0.1);
        }

        .btn-edit {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-edit:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: var(--white);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .btn-delete:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--gray);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        /* Styles pour le tableau des utilisateurs */
        .table-container {
            overflow-x: auto;
            margin-bottom: 2rem;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .users-table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover {
            background-color: rgba(255, 107, 0, 0.03);
        }

        .role-form {
            display: inline-block;
        }

        .role-select {
            padding: 0.4rem 0.8rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            background-color: var(--white);
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }

        .role-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 107, 0, 0.2);
        }

        /* Styles pour l'historique */
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .history-item {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 1.25rem;
            transition: var(--transition);
        }

        .history-item:hover {
            box-shadow: var(--shadow-md);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .action-type {
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .action-type.delete_user {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .action-type.update_role {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--info);
        }

        .action-date {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .action-description {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .action-admin {
            font-size: 0.85rem;
            color: var(--gray);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .admin-container {
                padding: 1.5rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }

            .product-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                padding: 0 1rem;
            }

            .logo {
                height: 70px;
            }

            .nav-links {
                gap: 1rem;
            }

            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }
        }
        .main-footer {
            background-color: var(--secondary);
            color: var(--white);
            padding: 2rem;
            text-align: center;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-contact {
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: var(--white);
            font-size: 1.25rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--primary);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php">
                <img src="./image/Logo5.png" alt="Scoop" class="logo">
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="product.php">Publier</a></li>
                    <li><a href="vente.php" class="active">Activités</a></li>
                    <li><a href="index.php?a">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-container">
        <div class="admin-header">
            <h1><?= $isAdmin ? 'Tableau de bord Admin' : 'Mes activités' ?></h1>
            <div class="user-info">
                <span>Bonjour, <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                <?php if ($isAdmin): ?>
                    <span class="admin-badge">ADMINISTRATEUR</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                switch($_GET['success']) {
                    case 'user_deleted': echo "L'utilisateur a été supprimé avec succès"; break;
                    case 'role_updated': echo "Le rôle de l'utilisateur a été mis à jour"; break;
                    default: echo "Action effectuée avec succès";
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-tabs">
            <button class="tab-btn active" data-tab="publications"><?= $isAdmin ? 'Publications' : 'Mes publications' ?></button>
            <?php if ($isAdmin): ?>
                <button class="tab-btn" data-tab="utilisateurs">Utilisateurs</button>
                <button class="tab-btn" data-tab="historique">Historique</button>
            <?php endif; ?>
        </div>

        <!-- Publications Section -->
        <section id="publications" class="tab-content active">
            <?php if (!empty($produits)): ?>
                <div class="products-grid">
                    <?php foreach ($produits as $prod): ?>
                        <article class="product-card">
                            <img src="uploads/<?= htmlspecialchars($prod['photo']) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" class="product-image">
                            <div class="product-details">
                                <h3><?= htmlspecialchars($prod['nom']) ?></h3>
                                <p class="price"><?= number_format($prod['prix'], 0, ',', ' ') ?> FCFA</p>
                                
                                <?php if ($isAdmin): ?>
                                    <div class="product-meta">
                                        <span>Vendeur: <?= htmlspecialchars($prod['vendeur_prenom']) ?> <?= htmlspecialchars($prod['vendeur_nom']) ?></span>
                                        <span>Publié le: <?= date('d/m/Y à H:i', strtotime($prod['date_creation'])) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?= $prod['id'] ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    <a href="edit_product.php?id=<?= $prod['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="delete_product.php?id=<?= $prod['id'] ?>" class="btn btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit définitivement ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p><?= $isAdmin ? 'Aucun produit trouvé' : 'Vous n\'avez aucun produit' ?></p>
                    <a href="product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Users Section (Admin only) -->
        <?php if ($isAdmin): ?>
            <section id="utilisateurs" class="tab-content">
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['nom']) ?></td>
                                    <td><?= htmlspecialchars($u['prenom']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <form method="post" action="vente.php?action=update_role" class="role-form">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <select name="new_role" class="role-select" onchange="this.form.submit()">
                                                <option value="Client" <?= $u['role'] == 'Client' ? 'selected' : '' ?>>Client</option>
                                                <option value="Admin" <?= $u['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="actions">
                                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-edit btn-small">
                                            <i class="fas fa-user-edit"></i> Modifier
                                        </a>
                                        <a href="vente.php?action=delete_user&id=<?= $u['id'] ?>" 
                                           class="btn btn-delete btn-small"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?')">
                                            <i class="fas fa-user-times"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- History Section (Admin only) -->
            <section id="historique" class="tab-content">
                <?php if (!empty($historique)): ?>
                    <div class="history-list">
                        <?php foreach ($historique as $log): ?>
                            <div class="history-item">
                                <div class="history-header">
                                    <span class="action-type <?= $log['action_type'] ?>">
                                        <?php 
                                        switch($log['action_type']) {
                                            case 'delete_user': echo 'Suppression utilisateur'; break;
                                            case 'update_role': echo 'Modification rôle'; break;
                                            default: echo $log['action_type'];
                                        }
                                        ?>
                                    </span>
                                    <span class="action-date"><?= date('d/m/Y à H:i', strtotime($log['action_date'])) ?></span>
                                </div>
                                <div class="action-description">
                                    <?= htmlspecialchars($log['description']) ?>
                                </div>
                                <div class="action-admin">
                                    Action effectuée par: <?= htmlspecialchars($log['prenom'] . ' ' . $log['nom']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>Aucune action enregistrée dans l'historique</p>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include("./components/footer.php") ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Retirer la classe active de tous les boutons et contenus
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Ajouter la classe active au bouton cliqué
                    button.classList.add('active');
                    
                    // Afficher le contenu correspondant
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Confirmation pour les actions sensibles
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    if (!confirm('Cette action est irréversible. Confirmer la suppression ?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>