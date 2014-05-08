<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class Vocabulary extends \Eloquent {

	public function concepts()
    {
        return $this->hasMany('Concept');
    }
}