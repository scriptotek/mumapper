<?php

// use Jenssegers\Mongodb\Model as Eloquent;

class Activity extends BaseModel {

	public function createdBy()
	{
		return $this->belongsTo('User', 'created_by');
	}

	protected function formatEvent($pre, $subject = null, $post = null)
    {
    	$s = '<span class="user">' . $this->createdBy->name . '</span> ';
		$s .= $pre;
		$s .= $subject ? ' ' . $subject : '';
		$s .= $post ? ' ' . $post : '';
		$s .= ' <span style="color:#666;">for ' . $this->created_at->diffForHumans() . '</span>';
		return $s;
    }

}