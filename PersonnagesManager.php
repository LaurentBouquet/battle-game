<?php

class PersonnagesManager
{
    private $_db; // Instance de PDO.

    public function __construct($db)
    {
        $this->setDb($db);
    }

    /**
     * Préparation de la requête d'insertion.
     * Assignation des valeurs pour le nom, la force, les dégâts, l'expérience et le niveau du personnage.
     * Exécution de la requête.
     */
    public function add(Personnage $perso)
    {
        $request = $this->_db->prepare('INSERT INTO personnages SET nom = :nom,
            `force` = :force, degats = :degats, niveau = :niveau, experience = :experience;');

        $request->bindValue(':nom', $perso->getNom(), PDO::PARAM_STR);
        $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
        $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
        $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);

        $request->execute();

        if ($request->errorCode() > 0) {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }

    }

    public function delete(Personnage $perso)
    {
        //$this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->getId().';');
        $this->_db->prepare('DELETE FROM personnages WHERE id = :id;');
        $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);
        $request->execute();

        if ($request->errorCode() > 0) {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }
    }

    public function getOne($id)
    {
        $id = (int) $id;

        //$request = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience FROM personnages WHERE id = '.$id.';');
        $request = $this->_db->prepare('SELECT id, nom, `force`, degats, niveau, experience FROM personnages WHERE id = :id;');
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();

        if ($request->errorCode() > 0) {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }

        $ligne = $request->fetch(PDO::FETCH_ASSOC);

        return new Personnage($ligne);
    }

    /**
     * Retourne la liste de tous les personnages.
     */
    public function getList()
    {
        $persos = array();

        $request = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience
                                        FROM personnages ORDER BY nom;');

        while ($ligne = $request->fetch(PDO::FETCH_ASSOC)) {
            $persos[] = new Personnage($ligne);
        }

        return $persos;
    }

    public function update(Personnage $perso)
    {
        $request = $this->_db->prepare('UPDATE personnages SET `force` = :force,
        degats = :degats, niveau = :niveau, experience = :experience WHERE id = :id;');

        $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
        $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
        $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);
        $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $request->execute();

        if ($request->errorCode() > 0) {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }
    }


    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}
