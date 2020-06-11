<?php

class Guerrier extends Personnage
{
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
        if ($this->_degats >= 0 && $this->_degats <= 25) {
            $this->_atout = 4;
        } elseif ($this->_degats > 25 && $this->_degats <= 50) {
            $this->_atout = 3;
        } elseif ($this->_degats > 50 && $this->_degats <= 75) {
            $this->_atout = 2;
        } elseif ($this->_degats > 50 && $this->_degats <= 90) {
            $this->_atout = 1;
        } else {
            $this->_atout = 0;
        }

        $this->_degats += 5 - $this->_atout;

        // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
        if ($this->_degats >= 100) {
            return self::PERSONNAGE_TUE;
        }

        // Sinon, on se contente de mettre à jour les dégâts du personnage.
        return self::PERSONNAGE_FRAPPE;
    }
}
