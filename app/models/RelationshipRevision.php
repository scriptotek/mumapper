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
		return Lang::get('relationships.states')[$this->state];
	}

	public function asEvent($backlink = false)
	{
		list($pre, $post, $icon) = $this->reviewed_at
			? ['godkjente', null, 'ok-circle']
		 	: ($this->parent 
				? ($this->parent->stateLabel() != $this->stateLabel() 
					? ['endret status for', 'fra <strong>' . $this->parent->stateLabel() . '</strong> til <strong>' . $this->stateLabel() . '</strong>', 'pencil']
					: ['kommenterte', null, 'comment']
				  )
				: ['opprettet', null, 'plus-sign']
			  );

		$subj = $backlink ? $this->representation() : 'relasjonen';
		$s = $this->formatEvent($pre, $subj, $post, $icon);

		// Relationship revisions can have comments. Let's show them as well
		// foreach ($this->comments as $c) {
		// 	$s .= '<div class="comment">' . $c->content . '<div>';
		// }

		return $s;
	}

}