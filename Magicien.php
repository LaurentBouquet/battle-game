<?php 

final class Magicien extends Personnage {

    private $_magie = 10;

    public function recevoirDegats(Personnage $adversaire) {
        $this->_degats = $this->_degats + 5;
    }



}