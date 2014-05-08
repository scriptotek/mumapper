<?php

//use Jenssegers\Mongodb\Model as MongoModel;

class UserAccount extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

    public function user()
    {
        return $this->belongsTo('User');
    }

	/**
     * Accessor for the extras field
     */
	public function getExtrasAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Mutator for the extras field
     */
    public function setExtrasAttribute($value)
    {
        $this->attributes['extras'] = json_encode($value);
    }

}