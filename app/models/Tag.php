<?php

class Tag extends BaseModel {

	/**
	 * Validation rules
	 * 
	 * @var Array
	 */
	protected static $rules = array(
		'label' => 'required',
	);

	public function relationships()
	{
		return $this->belongsToMany('Relationship');
	}

}