<?php

final class Guerrier extends Personnage
{
    private $_armure = 50;

    public function recevoirDegats(Personnage $adversaire)
    {        
        if ($this->_armure >= 5) {
            $this->_armure -= 5;
        } else {
            $this->_degats = ($this->_degats - $this->_armure) + 5;
            $this->_armure = 0;
        }
    }

}
