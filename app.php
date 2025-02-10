<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Model\User;
use App\VO\Uid;
use App\Controller;

$controller = new Controller();

// Récupération des arguments
if ($argc < 2) {
	echo "Usage:\n";
	echo "  php app.php add <json>\n";
	echo "  php app.php update <id> <json>\n";
	echo "  php app.php delete <id>\n";
	exit(1);
}

$command = $argv[1];

switch ($command) {
	case 'add':
		if ($argc < 3) {
			echo "Usage: php app.php add <json>\n";
			exit(1);
		}
		$decoded = json_decode($argv[2], true);
		if (!isset($decoded['id'])) {
			echo "Erreur: La propriété 'id' est requise." . PHP_EOL;
			exit(1);
		}
		if (!isset($decoded['login'])) {
			echo "Erreur: La propriété 'login' est requise." . PHP_EOL;
			exit(1);
		}
		if (!isset($decoded['password'])) {
			echo "Erreur: La propriété 'password' est requise." . PHP_EOL;
			exit(1);
		}
		if (!isset($decoded['email'])) {
			echo "Erreur: La propriété 'email' est requise." . PHP_EOL;
			exit(1);
		}

		$id = new Uid($decoded['id']);
		$user = new User($id, $decoded['login'], $decoded['password'], $decoded['email'], new DateTimeImmutable());

		try {
			$createdUser = $controller->getUserManager()->create($user);
			echo $createdUser ?: "Erreur: Échec de l'ajout" . PHP_EOL;
		}
		catch (Exception $e) {
			echo "Erreur: " . $e->getMessage() . PHP_EOL;
		}
		break;

	case 'update':
		if ($argc < 4) {
			echo "Usage: php app.php update <id> <json>\n";
			exit(1);
		}
		$id = new Uid($argv[2]);
		$decoded = json_decode($argv[3], true);

		$newLogin = $decoded['login'] ?? null;
		$newPassword = $decoded['password'] ?? null;
		$newEmail = $decoded['email'] ?? null;

		try {
			$updatedUser = $controller->getUserManager()->update($id, $newLogin, $newPassword, $newEmail);
			echo $updatedUser ?: "Erreur: Échec de la mise à jour\n";
		}
		catch (Exception $e) {
			echo "Erreur: " . $e->getMessage() . PHP_EOL;
		}
		break;

	case 'delete':
		if ($argc < 3) {
			echo "Usage: php app.php delete <id>\n";
			exit(1);
		}
		$id = new Uid($argv[2]);
		try {
			$deleted = $controller->getUserManager()->delete($id);
			echo $deleted ? 'OK' : "Erreur: Échec de la suppression\n";
		}
		catch (Exception $e) {
			echo "Erreur: " . $e->getMessage() . PHP_EOL;
		}
		break;

	case 'list':
		$users = $controller->getUserManager()->getDbAdapter()->query('user');
		echo "Liste des utilisateurs:\n";

		if (empty($users)) {
			echo "Aucun utilisateur trouvé.\n";
		} else {
			foreach ($users as $user) {
				echo "id: " . $user['id'] . "\n";
				echo "Login: " . $user['login'] . "\n";
				echo "Email: " . $user['email'] . "\n";
				echo "Date de création: " . $user['created_at'] . "\n\n";
			}
		}
		break;

	default:
		echo "Erreur: Commande inconnue: $command\n";
		exit(1);
}
