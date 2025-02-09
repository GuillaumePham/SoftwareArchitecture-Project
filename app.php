<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Model\User;
use App\VO\Uid;
use App\Controller;

$controller = new Controller();

// Récupération des arguments
if ($argc < 2) {
    echo "Usage:\n";
    echo "  php app.php add <id> <login> <password> <email>\n";
    echo "  php app.php update <id> <login> <password> <email>\n";
    echo "  php app.php delete <id>\n";
    exit(1);
}

$command = $argv[1];

switch ($command) {
    case 'add':
        if ($argc < 6) {
            echo "Usage: php app.php add <id> <login> <password> <email>\n";
            exit(1);
        }
        $id = new Uid($argv[2]);
        $user = new User($id, $argv[3], $argv[4], $argv[5], new DateTimeImmutable());

        $createdUser = $controller->getUserManager()->create($user);
        echo $createdUser ? "Utilisateur ajouté: $createdUser" : "Échec de l'ajout\n";
        break;

    case 'update':
        if ($argc < 6) {
            echo "Usage: php app.php update <id> <login> <password> <email>\n";
            exit(1);
        }
        $id = new Uid($argv[2]);
        $user = new User($id, $argv[3], $argv[4], $argv[5], new DateTimeImmutable());

        $updatedUser = $controller->getUserManager()->update($user);
        echo $updatedUser ? "Utilisateur mis à jour: $updatedUser" : "Échec de la mise à jour\n";
        break;

    case 'delete':
        if ($argc < 3) {
            echo "Usage: php app.php delete <id>\n";
            exit(1);
        }
        $id = new Uid($argv[2]);
        $controller->getUserManager()->delete($id);
        echo "Utilisateur supprimé.\n";
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
        echo "Commande inconnue: $command\n";
        exit(1);
}
