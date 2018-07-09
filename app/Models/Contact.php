<?php

namespace Framework\Models;

use Illuminate\Database\Eloquent\Model;
use Framework\Events\ModelCreated;

class Contact extends Model
{
    use IngoingTrait;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ModelCreated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'message'];
}
