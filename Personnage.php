<?php
class Personnage
{
    private $_id;
    private $_nom = 'inconnu';
    private $_force = 20;
    private $_degats = 0;
    private $_niveau = 0;
    private $_experience = 0;

    const FORCE_PETITE = 20;
    const FORCE_MOYENNE = 50;
    const FORCE_GRANDE = 80;

    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
    const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
    const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.

    private static $_compteur = 0;

    /*public function __construct($nom, $force, $degats)
    {
    $this->setNom($nom); // Initialisation du nom du personnage
    $this->setForce($force); // Initialisation de la force.
    $this->setDegats($degats); // Initialisation des dégâts.
    $this->_experience = 1; // Initialisation de l'expérience à 1.
    self::$_compteur++;
    print('<br/> Le personnage "' . $nom . '" est créé !'); // Message s'affichant lorsque l'objet est créé.
    self::parler();
    }*/

    public function __construct(array $ligne)
    {
        $this->hydrate($ligne);
        self::$_compteur++;
        //print('<br/> Le personnage "' . $this->getNom() . '" est créé !'); // Message s'affichant lorsque l'objet est créé.
        //self::parler();
    }

    /*public function hydrate(array $ligne)
    {
    if (isset($ligne['id'])) {
    $this->_id = $ligne['id'];
    }

    if (isset($ligne['nom'])) {
    $this->_nom = $ligne['nom'];
    }

    if (isset($ligne['force'])) {
    $this->_force = $ligne['force'];
    }

    if (isset($ligne['degats'])) {
    $this->_degats = $ligne['degats'];
    }
    if (isset($ligne['niveau'])) {
    $this->_niveau = $ligne['niveau'];
    }

    if (isset($ligne['experience'])) {
    $this->_experience = $ligne['experience'];
    }
    }*/
    public function hydrate(array $ligne)
    {
        foreach ($ligne as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set' . ucfirst($key);

            //print('<br/>'.$method.'('.$value.')'. 'type = '.gettype($value));
            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $this->$method($value);
            }
        }
    }

    public function __toString()
    {
        return $this->_nom . ' : Force = '
        . $this->_force . ' / Dégats = '
        . $this->_degats . ' / Expérience = '
        . $this->_experience;
    }

    // Ceci est la méthode getNom() : elle se charge de renvoyer le contenu de l'attribut $_nom.
    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
      $id = (int) $id;
      
      if ($id > 0)
      {
        $this->_id = $id;
      }
    }

    // Ceci est la méthode getNom() : elle se charge de renvoyer le contenu de l'attribut $_nom.
    public function getNom()
    {
        return $this->_nom;
    }

    // Mutateur chargé de modifier l'attribut $_nom, pour vérifier les conditions de modification de l'attribut $_nom.
    public function setNom($nom)
    {
        if (!is_string($nom)) // S'il ne s'agit pas d'un texte.
        {
            trigger_error('Le nom d\'un personnage doit être un texte', E_USER_WARNING);
            return;
        }
        $this->_nom = $nom;
    }

    // Ceci est la méthode getForce() : elle se charge de renvoyer le contenu de l'attribut $_force.
    public function getForce()
    {
        return $this->_force;
    }

    // Mutateur chargé de vérifier les conditions de modification de l'attribut $_force.
    public function setForce($force)
    {
        // On vérifie qu'on nous donne bien soit une "FORCE_PETITE", soit une "FORCE_MOYENNE", soit une "FORCE_GRANDE".
        if (in_array($force, array(self::FORCE_PETITE, self::FORCE_MOYENNE, self::FORCE_GRANDE))) {
            $this->_force = $force;
        } else {
            trigger_error('La force d\'un personnage doit être ' . self::FORCE_PETITE . ', ' . self::FORCE_MOYENNE . ' ou ' . self::FORCE_GRANDE . '.', E_USER_WARNING);
            return;
        }
    }

    // Ceci est la méthode getDegats() : elle se charge de renvoyer le contenu de l'attribut $_degats.
    public function getDegats()
    {
        return $this->_degats;
    }

    // Mutateur chargé de modifier l'attribut $_niveau, pour vérifier les conditions de modification de l'attribut $_niveau.
    public function setNiveau($niveau)
    {
        if (!is_int($niveau)) // S'il ne s'agit pas d'un nombre entier.
        {
            trigger_error('Le niveau d\'un personnage doit être un nombre entier', E_USER_WARNING);
            return;
        }
        $this->_niveau = $niveau;
    }

    // Ceci est la méthode getniveau() : elle se charge de renvoyer le contenu de l'attribut $_niveau.
    public function getniveau()
    {
        return $this->_niveau;

    }

    // Mutateur chargé de modifier l'attribut $_degats, pour vérifier les conditions de modification de l'attribut $_degats.
    public function setDegats($degats)
    {
        if (!is_int($degats)) // S'il ne s'agit pas d'un nombre entier.
        {
            trigger_error('Les degats d\'un personnage doit être un nombre entier', E_USER_WARNING);
            return;
        }
        $this->_degats = $degats;
    }

    // Ceci est la méthode getExperience() : elle se charge de renvoyer le contenu de l'attribut $_experience.
    public function getExperience()
    {
        return $this->_experience;
    }

    // Mutateur chargé de modifier l'attribut $_experience.
    public function setExperience($experience)
    {
        $this->_experience += $experience;
    }

    public function gagnerExperience()
    {
        // On ajoute 1 à notre attribut $_experience.
        $this->_experience = $this->_experience + 1;
        print('<br/> ' . $this->getNom() . ' a ' . $this->_experience . ' points d\'expérience.');
    }

    /*public function frapper($persoAFrapper)
    {
    $persoAFrapper->_degats += $this->_force;
    print('<br/> ' . $persoAFrapper->getNom() . ' a été frappé par '
    . $this->getNom() . ' -> Dégats de ' . $persoAFrapper->getNom() . ' = ' . $persoAFrapper->_degats);
    }*/
    public function frapper(Personnage $perso)
    {
        if ($perso->getId() == $this->_id) {
            return self::CEST_MOI;
        }

        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats();
    }

    public function recevoirDegats()
    {
        $this->_degats += 5;

        // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
        if ($this->_degats >= 100) {
            return self::PERSONNAGE_TUE;
        }

        // Sinon, on se contente de dire que le personnage a bien été frappé.
        return self::PERSONNAGE_FRAPPE;
    }

    // Notez que le mot-clé static peut aussi être placé avant la visibilité de la méthode.
    public static function parler()
    {
        print('<br/>Il y a actuellement ' . self::$_compteur . ' personnage(s) dans le jeu.');
    }

    public function nomValide()
    {
      return !empty($this->_nom);
    }
    

}
