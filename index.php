<?php

function chargerClasse($nomClasse)
{
    require $nomClasse . '.php';
}

spl_autoload_register('chargerClasse');

print('<br/>PHP Battle Game<br/>');

$dsn = 'mysql:dbname=battlegame;host=127.0.0.1';
$user = 'root';
$password = 'root';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    if ($db) {
        print('<br/>Lecture dans la base de données :');

        $personnagesManager = new PersonnagesManager($db);

        // Ajouter un personnage
        $perso = new Personnage(array(
            'nom' => 'Totor',
            'force' => 50,
            'degats' => 0,
            'niveau' => 1,
            'experience' => 0
          ));


        $personnagesManager->add($perso);

        // Afficher la liste des personnages
        $personnages = $personnagesManager->getList();
        
        foreach ($personnages as $perso) {
            print('<br/>' . $perso->getNom() . ' a ' . $perso->getForce() . ' de force, '
                . $perso->getDegats() . ' de dégâts, ' . $perso->getExperience() . ' d\'expérience et est au niveau '
                . $perso->getNiveau());
        }
    }
} catch (PDOException $e) {
    print('<br/>Erreur de connexion : ' . $e->getMessage());
}
