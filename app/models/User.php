<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
//use Jenssegers\Mongodb\Model as MongoModel;

class User extends \Eloquent implements UserInterface, RemindableInterface {

	public function accounts()
    {
        return $this->hasMany('UserAccount');
    }

	/**
	 * Attributes that can be filled by mass assignment
	 *
	 * @var string[]
	 */
	protected $fillable = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
     * Accessor for the data field
     */
	public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Mutator for the data field
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value) {
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName() {
		return 'remember_token';
	}

}