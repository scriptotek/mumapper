<?php

class Comment extends Activity {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public function commentable()
	{
		return $this->morphTo();
	}

	/**
	 * Mutuator for the content field
	 */
	public function setContentAttribute($value)
	{
		$this->attributes['content'] = strip_tags($value);
	}


	public function asEvent($backlink = false)
	{
		if ($this->commentable) {
			$subj = $backlink ? $this->commentable->representation() : null;
		} else {
			$subj = '<em>noe som ikke eksisterer</em>';
		}
		$s = $this->formatEvent('kommenterte', $subj);
		$s .= '<div class="comment">' . $this->content . '</div>';
		return $s;
	}


}