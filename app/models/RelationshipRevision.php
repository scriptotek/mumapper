<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class RelationshipRevision extends Activity implements CommentableInterface {

	/**
     * Validation rules
     * 
     * @var Array
     */
	protected static $rules = array(
		'relationship_id' => 'required|integer|exists:relationships,id',
		'created_by' => 'required|integer|exists:users,id',
		'state' => 'required|alpha',
	);

	// Don't forget to fill this array
	protected $fillable = [];

	public function parent()
	{
		return $this->hasOne('RelationshipRevision', 'id', 'parent_revision');
	}

	public function relationship()
	{
		return $this->belongsTo('Relationship');
	}

	public function reviewedBy()
	{
		return $this->belongsTo('User', 'reviewed_by');
	}

	public function comments()
	{
		return $this->morphMany('Comment', 'commentable');
	}

	public function representation($prefix = false, $link = true)
	{
		return ($prefix ? 'revisjon ' . $this->id . ' av ' : '') . 
			 $this->relationship->representation($link);
	}

	public function stateLabel()
	{
		return Relationship::$stateLabels[$this->state];
	}

	public function asEvent($backlink = false)
	{
		list($pre, $post) = $this->reviewed_by
			? ['godkjente', null]
		 	: ($this->parent 
				? ($this->parent->stateLabel() != $this->stateLabel() 
					? ['endret status for', 'fra <strong>' . $this->parent->stateLabel() . '</strong> til <strong>' . $this->stateLabel() . '</strong>']
					: ['kommenterte', null]
				  )
				: ['opprettet', null]
			  );

		$subj = $backlink ? $this->representation() : 'relasjonen';
		$s = $this->formatEvent($pre, $subj, $post);

		// Relationship revisions can have comments. Let's show them as well
		foreach ($this->comments as $c) {
			$s .= '<div class="comment">' . $c->content . '<div>';
		}

		return $s;
	}

}