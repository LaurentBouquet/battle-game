<?php

abstract class Personnage
{
    protected $_id;
    protected $_nom = 'inconnu';
    protected $_force = 20;
    protected $_experience = 0;
    protected $_degats = 0;
    protected $_type = 'inconnu';
    protected $_atout = 0;
    protected $_reveil = 0;

    // Constante renvoyée par la méthode `frapper` si on se frappe soit-même.
    const CEST_MOI = 1;
    // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
    const PERSONNAGE_TUE = 2;
    // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
    const PERSONNAGE_FRAPPE = 3;
    // Constante renvoyée par la méthode `lancerUnSort`
    // (voir classe Magicien) si on a bien ensorcelé un personnage.
    const PERSONNAGE_ENSORCELE = 4;
    // Constante renvoyée par la méthode `lancerUnSort`
    // (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.
    const PAS_DE_MAGIE = 5;
    // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi.
    const PERSO_ENDORMI = 6;

    public function __construct(array $ligne)
    {
        $this->hydrate($ligne);
        $this->_type = strtolower(get_class($this));
        //print('<br/> Le personnage "' . $this->getNom() . '" est créé !'); // Message s'affichant une fois que tout objet est créé.
        //self::parler();

    }

    public function __toString()
    {
        return $this->getNom() . ' a ' . $this->getVie() . ' points de vie'
        . ' et a une force de ' . $this->getForce() . '.';
    }

    public function hydrate(array $ligne)
    {
        foreach ($ligne as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set' . ucfirst($key);

            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $this->$method($value);
            }
        }
    }

    public function estEndormi()
    {
        return $this->_reveil > time();
    }

    // Notez que le mot-clé static peut aussi être placé avant la visibilité de la méthode.
    public static function parler()
    {
        print('<br/> Je suis un personnage');
    }

    public function setNom($nom)
    {
        if (!is_string($nom)) {
            trigger_error('Le nom du personnage doit être un texte.');
        } else {
            $this->_nom = $nom;
        }
        return $this;
    }

    public function nomEstValide()
    {
        return !empty($this->getNom());
    }

    public function setForce($force)
    {
        if (!is_int($force)) {
            trigger_error('La force du personnage doit être une valeur entière.');
        } else {
            $this->_force = $force;
        }
    }

    public function getForce()
    {
        return $this->_force;
    }

    public function setVie($vie)
    {
        if (!is_int($vie)) {
            trigger_error('Les points de vie du personnage doivent être une valeur entière.');
        } else {
            $this->_vie = $vie;
        }
    }

    public function getVie()
    {
        return $this->_vie;
    }

    public function setExperience($experience)
    {
        $experience = (int) $experience;

        if ($experience >= 1 && $experience <= 100) {
            $this->_experience = $experience;
        }
    }

    public function gagnerExperience()
    {
        //$this->_experience = $this->_experience + 1;
        $this->_experience += 1;
        print('<br/>' . $this->getNom() . ' a ' . $this->_experience . ' points d\'expérience.');
    }

    public function frapper(Personnage $adversaire)
    {
        if ($adversaire->getId() == $this->_id) {
            return self::CEST_MOI;
        }

        if ($this->estEndormi()) {
            return self::PERSO_ENDORMI;
        }

        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode :
        // self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE.
        return $adversaire->recevoirDegats();
    }

    public function recevoirDegats()
    {
        $this->_degats += 5;

        // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
        if ($this->_degats >= 100) {
            return self::PERSONNAGE_TUE;
        }

        // Sinon, on se contente de mettre à jour les dégâts du personnage.
        return self::PERSONNAGE_FRAPPE;
    }

    public function getHeureReveil()
    {
        $secondes = $this->_reveil;
        $secondes -= time();

        $heures = floor($secondes / 3600);
        $secondes -= $heures * 3600;
        $minutes = floor($secondes / 60);
        $secondes -= $minutes * 60;

        $heures .= $heures <= 1 ? ' heure' : ' heures';
        $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
        $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';

        return $heures . ', ' . $minutes . ' et ' . $secondes;
    }

    public function getAtout()
    {
        return $this->_atout;
    }

    public function getDegats()
    {
        return $this->_degats;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getNom()
    {
        return $this->_nom;
    }

    public function getReveil()
    {
        return $this->_reveil;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setAtout($atout)
    {
        if (!is_int($atout)) {
            trigger_error('L\'atout du personnage doit être une valeur entière.');
        } else {
            if ($atout >= 0 && $atout <= 4) {
                $this->_atout = $atout;
            } else {
                trigger_error('L\'atout du personnage doit être une valeur entière entre 0 et 4.');
            }
        }
    }

    public function setDegats($degats)
    {
        if (!is_int($degats)) {
            trigger_error('Les dégâts du personnage doivent être une valeur entière.');
        } else {
            if ($degats >= 0 && $degats <= 100) {
                $this->_degats = $degats;
            } else {
                trigger_error('Les dégâts du personnage doivent être une valeur entière entre 0 et 100.');
            }
        }
    }

    public function setId($id)
    {
        if (!is_int($force)) {
            trigger_error('L\'id du personnage doit être une valeur entière.');
        } else {
            $this->_id = $id;
        }
    }

    public function setReveil($time)
    {
        if (!is_int($time)) {
            trigger_error('L\'heure de réveil du personnage doit un nombre de secondes.');
        } else {
            $this->_reveil = (int) $time;
        }
    }

}
