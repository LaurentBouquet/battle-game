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
        $request = $this->_db->prepare('INSERT INTO personnages_v2
            SET nom = :nom, `type` = :type, `atout` = :atout, `reveil` = :reveil, `force` = :force,
                `degats` = :degats, `niveau` = :niveau, `experience` = :experience ;');

        $request->bindValue(':nom', $perso->getNom());
        $request->bindValue(':type', $perso->getType());
        $request->bindValue(':atout', $perso->getAtout());
        $request->bindValue(':reveil', $perso->getReveil());
        $request->bindValue(':force', $perso->getForce());
        $request->bindValue(':degats', $perso->getDegats());
        $request->bindValue(':niveau', $perso->getNiveau());
        $request->bindValue(':experience', $perso->getExperience());

        $request->execute();

        if ($request->errorCode() != '00000') {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }

        $perso->hydrate(array(
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
            'atout' => 0,
        ));
    }

    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        //$this->_db->exec('DELETE FROM personnages_v2 WHERE id = '.$perso->getId().';');
        $request = $this->_db->prepare('DELETE FROM personnages_v2 WHERE id = :id;');
        $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);
        $request->execute();

        if ($request->errorCode() != '00000') {
            echo "<br/>Une erreur SQL est intervenue : ";
            print_r($request->errorInfo()[2]);
        }
    }

    public function exists($info)
    {
        if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
        {
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages_v2 WHERE id = ' . $info)->fetchColumn();
        }

        // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.

        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages_v2 WHERE nom = :nom');
        $q->execute(array(':nom' => $info));

        return (bool) $q->fetchColumn();
    }

    /*public function getOne($id)
    {
    $id = (int) $id;

    //$request = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience FROM personnages_v2 WHERE id = '.$id.';');
    $request = $this->_db->prepare('SELECT id, nom, `force`, degats, niveau, experience FROM personnages_v2 WHERE id = :id;');
    $request->bindValue(':id', $id, PDO::PARAM_INT);
    $request->execute();

    if ($request->errorCode() != '00000') {
    echo "<br/>Une erreur SQL est intervenue : ";
    print_r($request->errorInfo()[2]);
    }

    $ligne = $request->fetch(PDO::FETCH_ASSOC);

    return new Personnage($ligne);
    }
     */

    public function get($info)
    {
        if (is_int($info)) {
            $q = $this->_db->query('SELECT id, nom, degats, reveil, type, atout FROM personnages_v2 WHERE id = ' . $info);
            $perso = $q->fetch(PDO::FETCH_ASSOC);
        } else {
            $q = $this->_db->prepare('SELECT id, nom, degats, reveil, type, atout FROM personnages_v2 WHERE nom = :nom');
            $q->execute(array(':nom' => $info));

            $perso = $q->fetch(PDO::FETCH_ASSOC);
        }

        switch ($perso['type']) {
            case 'guerrier':return new Guerrier($perso);
            case 'magicien':return new Magicien($perso);
            default:return null;
        }
    }

    /*
    public function getList()
    {
    $persos = array();

    $request = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience
    FROM personnages_v2 ORDER BY nom;');

    while ($ligne = $request->fetch(PDO::FETCH_ASSOC)) {
    $persos[] = new Personnage($ligne);
    }

    return $persos;
    }
     */
    /*
    public function getList($nom)
    {
    $persos = array();

    $q = $this->_db->prepare('SELECT id, nom, degats, reveil, type,
    atout FROM personnages_v2 WHERE nom <> :nom ORDER BY nom');
    $q->execute(array(':nom' => $nom));

    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
    {
    switch ($donnees['type'])
    {
    case 'guerrier': $persos[] = new Guerrier($donnees); break;
    case 'magicien': $persos[] = new Magicien($donnees); break;
    }
    }

    return $persos;
    }
     */
    public function getList($nom = null)
    {
        $persos = array();

        if (isset($nom)) {
            $q = $this->_db->prepare('SELECT id, nom, degats, reveil, type,
            atout FROM personnages_v2 WHERE nom <> :nom ORDER BY nom');
            $q->execute(array(':nom' => $nom));
        } else {
            $q = $this->_db->query('SELECT id, nom, degats, reveil, type,
            atout FROM personnages_v2 ORDER BY nom');
        }   

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
            switch ($donnees['type']) {
                case 'guerrier':$persos[] = new Guerrier($donnees);
                    break;
                case 'magicien':$persos[] = new Magicien($donnees);
                    break;
            }
        }

        return $persos;
    }

    /*
    public function update(Personnage $perso)
    {
    $request = $this->_db->prepare('UPDATE personnages_v2 SET `force` = :force,
    degats = :degats, niveau = :niveau, experience = :experience WHERE id = :id;');

    $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
    $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
    $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
    $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);
    $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

    $request->execute();

    if ($request->errorCode() != '00000') {
    echo "<br/>Une erreur SQL est intervenue : ";
    print_r($request->errorInfo()[2]);
    }
    }
     */
    public function update(Personnage $perso)
    {
        $q = $this->_db->prepare('UPDATE personnages_v2
        SET degats = :degats, reveil = :reveil,
            atout = :atout WHERE id = :id');

        $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $q->bindValue(':reveil', $perso->getReveil(), PDO::PARAM_INT);
        $q->bindValue(':atout', $perso->getAtout(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $q->execute();
    }

    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}
