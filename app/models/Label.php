<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class Label extends BaseModel {

	/**
     * Validation rules
     * 
     * @var Array
     */
	protected static $rules = array(
		'concept_id' => 'required|integer',
		'class'      => 'required|in:prefLabel,altLabel',
		'lang'       => 'required|alpha',
		'value'      => 'required|unique_with:labels,concept_id,lang',
	);

	// Don't forget to fill this array
	//protected $fillable = [];

	public function concept()
	{
		return $this->belongsTo('Concept');
	}

}