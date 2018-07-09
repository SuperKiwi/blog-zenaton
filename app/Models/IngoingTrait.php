<?php

namespace Framework\Models;

use Framework\Models\Ingoing;

trait IngoingTrait
{
    /**
     * Morph One relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
	public function ingoing()
	{
		return $this->morphOne(Ingoing::class, 'ingoing');
	}
}
