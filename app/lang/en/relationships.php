<?php

return array(

	'title' => 'Relationship',
	'source_vocabulary_concept' => 'Concept in source vocabulary',
	'target_vocabulary_concept' => 'Concept in target vocabulary',
	'external_resources' => 'External resources',
	'comment' => 'Comment',
	'other_relationships' => 'Other relationships',
	'as_source' => ':state to :target',
	'as_target' => ':state from :source',

	'states' => array(
		'suggested' => 'suggested',
		'exact' => 'exact equivalence (EQ)',
		'close' => 'inexact equivalence (EQ~)',
		'broad' => 'broader (BM)',
		'narrow' => 'narrower (NM)',
		'related' => 'related (RM)',
		'rejected' => 'rejected',
	),

);
