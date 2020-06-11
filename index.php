<?php

function chargerClasse($nomClasse)
{
    require $nomClasse . '.php';
}

spl_autoload_register('chargerClasse');

session_start();

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

// Si la session perso existe, on restaure l'objet.
if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}

print('<br/>PHP Battle Game<br/>');

$dsn = 'mysql:dbname=battlegame;host=127.0.0.1';
$user = 'root';
$password = 'root';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    if ($db) {
        print('<br/>Lecture dans la base de données :');

        $manager = new PersonnagesManager($db);

        // Si on a voulu créer un personnage.
        if (isset($_POST['creer']) && isset($_POST['nom'])) {

            switch ($_POST['type']) {
                case 'magicien':
                    $perso = new Magicien(array('nom' => $_POST['nom']));
                    break;

                case 'guerrier':
                    $perso = new Guerrier(array('nom' => $_POST['nom']));
                    break;

                default:
                    $message = 'Le type du personnage est invalide.';
                    break;
            }

            // Si le type du personnage est valide, on a créé un personnage.
            if (isset($perso)) {
                if (!$perso->nomEstValide()) {
                    $message = 'Le nom choisi est invalide.';
                    unset($perso);
                } elseif ($manager->exists($perso->getNom())) {
                    $message = 'Le nom du personnage est déjà pris.';
                    unset($perso);
                } else {
                    $message = 'Ajout du personnage ' . $perso->getNom();
                    $manager->add($perso);
                }
            }
        }
        // Si on a voulu utiliser un personnage.
        elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
            if ($manager->exists($_POST['nom'])) // Si celui-ci existe.
            {
                $perso = $manager->get($_POST['nom']);
            } else {
                // S'il n'existe pas, on affichera ce message.
                $message = 'Ce personnage n\'existe pas !';
            }
        }

        // Si on a cliqué sur un personnage pour le frapper.
        elseif (isset($_GET['frapper'])) {
            if (!isset($perso)) {
                $message = 'Merci de créer un personnage ou de vous identifier.';
            } else {
                if (!$manager->exists((int) $_GET['frapper'])) {
                    $message = 'Le personnage que vous voulez frapper n\'existe pas !';
                } else {
                    $persoAFrapper = $manager->get((int) $_GET['frapper']);
                    // On stocke dans $retour les éventuelles erreurs
                    // ou messages que renvoie la méthode frapper.
                    $retour = $perso->frapper($persoAFrapper);

                    switch ($retour) {
                        case Personnage::CEST_MOI:
                            $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                            break;
                        case Personnage::PERSONNAGE_FRAPPE:
                            $message = 'Le personnage a bien été frappé !';

                            $manager->update($perso);
                            $manager->update($persoAFrapper);

                            break;

                        case Personnage::PERSONNAGE_TUE:
                            $message = 'Vous avez tué ce personnage !';

                            $manager->update($perso);
                            $manager->delete($persoAFrapper);

                            break;

                        case Personnage::PERSO_ENDORMI:
                            $message = 'Vous êtes endormi, vous ne pouvez pas frapper de personnage !';
                            break;
                    }
                }
            }
        } elseif (isset($_GET['ensorceler'])) {
            if (!isset($perso)) {
                $message = 'Merci de créer un personnage ou de vous identifier.';
            } else {
                // Il faut bien vérifier que le personnage est un magicien.
                if ($perso->getType() != 'magicien') {
                    $message = 'Seuls les magiciens peuvent ensorceler des personnages !';
                } else {
                    if (!$manager->exists((int) $_GET['ensorceler'])) {
                        $message = 'Le personnage que vous voulez frapper n\'existe pas !';
                    } else {
                        $persoAEnsorceler = $manager->get((int) $_GET['ensorceler']);
                        $retour = $perso->lancerUnSort($persoAEnsorceler);

                        switch ($retour) {
                            case Personnage::CEST_MOI:
                                $message = 'Mais... pourquoi voulez-vous vous ensorceler ???';
                                break;

                            case Personnage::PERSONNAGE_ENSORCELE:
                                $message = 'Le personnage a bien été ensorcelé !';

                                $manager->update($perso);
                                $manager->update($persoAEnsorceler);

                                break;
                            case Personnage::PAS_DE_MAGIE:
                                $message = 'Vous n\'avez pas de magie !';
                                break;

                            case Personnage::PERSO_ENDORMI:
                                $message = 'Vous êtes endormi, vous ne pouvez pas lancer de sort !';
                                break;
                        }
                    }
                }
            }
        }

    }
} catch (PDOException $e) {
    print('<br/>Erreur de connexion : ' . $e->getMessage());
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>TP : Mini jeu de combat - Version 2</title>

    <meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
  </head>
  <body>

<?php
if (isset($message)) // On a un message à afficher ?
{
    echo '<p>', $message, '</p>'; // Si oui, on l'affiche
}


if ($manager->count() > 0) {
    // Afficher la liste des personnages
    $personnages = $manager->getList();

    foreach ($personnages as $personnage) {
        print('<br/>' . $personnage->getNom() . ' a ' . $personnage->getForce() . ' de force, '
            . $personnage->getDegats() . ' de dégâts, ' . $personnage->getExperience() . ' d\'expérience et est au niveau '
            . $personnage->getNiveau());
    }

}
echo '<br/><br/>Nombre de personnages : '.$manager->count();



if (isset($perso)) // Si on utilise un personnage (nouveau ou pas).
{
    ?>        
        <p><a href="?deconnexion=1">Déconnexion</a></p>

        <fieldset>
          <legend>Mes informations</legend>
          <p>
            Type : <?php echo ucfirst($perso->getType()); ?><br />
            Nom : <?php echo htmlspecialchars($perso->getNom()); ?><br />
            Dégâts : <?php echo $perso->getDegats(); ?><br />
            <?php
// On affiche l'atout du personnage suivant son type.
    switch ($perso->getType()) {
        case 'magicien':
            echo 'Magie : ';
            break;

        case 'guerrier':
            echo 'Protection : ';
            break;
    }

    echo $perso->getAtout();
    ?>
            </p>
            </fieldset>

            <fieldset>
              <legend>Qui attaquer ?</legend>
              <p>
              <?php
    // On récupère tous les personnages par ordre alphabétique,
    // dont le nom est différent de celui de notre personnage (on va pas se frapper nous-même).

    $retourPersos = $manager->getList($perso->getNom());

    if (empty($retourPersos)) {
        echo 'Personne à frapper !';
    } else {
        if ($perso->estEndormi()) {
            echo 'Un magicien vous a endormi ! Vous allez vous réveiller dans ', $perso->getHeureReveil(), '.';
        } else {
            foreach ($retourPersos as $unPerso) {
                echo '<a href="?frapper=', $unPerso->getId(), '">', htmlspecialchars($unPerso->getNom()), '</a> (dégâts : ', $unPerso->getDegats(), ' | type : ', $unPerso->getType(), ')';

                // On ajoute un lien pour lancer un sort si le personnage est un magicien.
                if ($perso->getType() == 'magicien') {
                    echo ' | <a href="?ensorceler=', $unPerso->getId(), '">Lancer un sort</a>';
                }

                echo '<br />';
            }
        }
    }
    ?>

</p>
</fieldset>
<?php
} else {
    ?>
<form action="" method="post">
<p>
  Nom : <input type="text" name="nom" maxlength="50" /> <input type="submit" value="Utiliser ce personnage" name="utiliser" /><br />
  Type :
  <select name="type">
    <option value="magicien">Magicien</option>
    <option value="guerrier">Guerrier</option>
  </select>
  <input type="submit" value="Créer ce personnage" name="creer" />
</p>
</form>
<?php
}
?>
</body>
</html>
<?php
// Si on a créé un personnage, 
// on le stocke dans une variable session 
// afin d'économiser une requête SQL.
if (isset($perso)) 
{
  $_SESSION['perso'] = $perso;
}
?>
