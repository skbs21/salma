<?php
// Fonction pour centraliser l'envoi des réponses JSON
function sendResponse($message, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(["message" => $message]);
    exit;
}

// Vérification de la méthode HTTP
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Récupérer les données JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les champs nécessaires sont présents dans la requête
    if (isset($data['username']) && isset($data['email'])) {

        // Assainir les données
        $username = trim($data['username']);
        $email = trim($data['email']);

        // Validation des données
        if (empty($username) || empty($email)) {
            sendResponse("Nom d'utilisateur et email sont requis", 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse("Email invalide", 400);
        }

        // Connexion à la base de données
        $conn = mysqli_connect("localhost", "root", "", "mydatabase");
        if (!$conn) {
            error_log("Erreur de connexion à la base de données : " . mysqli_connect_error());
            sendResponse("Erreur interne du serveur", 500);
        }

        // Configurer l'encodage pour UTF-8
        mysqli_set_charset($conn, "utf8mb4");

        // Utilisation des requêtes préparées pour sécuriser l'insertion
        $stmt = $conn->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
        if ($stmt === false) {
            error_log("Erreur lors de la préparation de la requête SQL : " . $conn->error);
            sendResponse("Erreur lors de la préparation de la requête", 500);
        }

        // Lier les paramètres
        $stmt->bind_param("ss", $username, $email);

        // Exécution de la requête et gestion des erreurs
        if ($stmt->execute()) {
            sendResponse("Données insérées avec succès");
        } else {
            error_log("Erreur lors de l'insertion des données : " . $stmt->error);
            sendResponse("Erreur lors de l'insertion des données", 500);
        }

        // Fermeture des ressources
        $stmt->close();
        mysqli_close($conn);

    } else {
        // Si les champs sont manquants
        sendResponse("Données manquantes", 400);
    }

} else {
    // Si la méthode n'est pas POST
    sendResponse("Méthode HTTP non autorisée", 405);
}
