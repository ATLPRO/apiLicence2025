<?php
require_once "../../model/users.php";

class userController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new users($pdo);
    }

    public function resetPassword($email, $newPassword) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return ["success" => false, "message" => "Utilisateur introuvable."];
        }
    
        $password = $newPassword;
        $success = $this->userModel->updatePassword($email, $password);
    
        if ($success) {
            return ["success" => true, "message" => "Mot de passe réinitialisé avec succès."];
        } else {
            return ["success" => false, "message" => "Échec de la réinitialisation du mot de passe."];
        }
    }
    
}
