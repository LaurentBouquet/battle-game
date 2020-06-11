<?php

class PersonnagesManager
{
    private $_db; // Instance de PDO

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function add(Personnage $perso)
    {
        $request = $this->_db->prepare('INSERT INTO personnages SET nom = :nom, `force` = :force, degats = :degats, niveau = :niveau, experience = :experience;');

        $request->bindValue(':nom', $perso->getNom(), PDO::PARAM_STR);
        $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
        $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
        $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);

        $request->execute();
          
        $perso->hydrate(array(
          'id' => $this->_db->lastInsertId(),
          'degats' => 0,
        ));
    }

    public function count()
    {
      return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }
    
    public function delete(Personnage $perso)
    {
      $q = $this->_db->prepare('DELETE FROM personnages WHERE id = :id');
      $q->bindValue(':id', $perso->getId());
      $q->execute();
    }

    public function exists($info)
    {
      if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
      {
        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE id = :id');
        $q->execute(array(':id' => $info));
        
        return (bool) $q->fetchColumn();
      }
      
      // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.
      
      $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
      $q->execute(array(':nom' => $info));
      
      return (bool) $q->fetchColumn();
    }

    public function getOne($info)
    {
      if (is_int($info))
      {
        $q = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience FROM personnages WHERE id = '.$info.';');
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        
        return new Personnage($donnees);
      }
      else
      {
        $q = $this->_db->prepare('SELECT id, nom, `force`, degats, niveau, experience FROM personnages WHERE nom = :nom;');
        $q->execute(array(':nom' => $info));
      
        return new Personnage($q->fetch(PDO::FETCH_ASSOC));
      }
    }

    public function getList($nom = "")
    {
        $persos = array();

        if ($nom != "") {
            $q = $this->_db->prepare('SELECT id, nom, `force`, degats, niveau, experience FROM personnages WHERE nom <> :nom ORDER BY nom;');
            $q->execute(array(':nom' => $nom));
        } else {
            $q = $this->_db->query('SELECT id, nom, `force`, degats, niveau, experience FROM personnages ORDER BY nom;');
            $q->execute();
        }

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
            $persos[] = new Personnage($donnees);
        }

        return $persos;
    }

    public function update(Personnage $perso)
    {
        $request = $this->_db->prepare('UPDATE personnages SET `force` = :force, degats = :degats, niveau = :niveau, experience = :experience WHERE id = :id;');

        $request->bindValue(':force', $perso->getForce(), PDO::PARAM_INT);
        $request->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $request->bindValue(':niveau', $perso->getNiveau(), PDO::PARAM_INT);
        $request->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);
        $request->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $request->execute();
    }

    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}
