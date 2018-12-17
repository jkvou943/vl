<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class AutoPrice extends Model
{
    protected $guarded = [];
	protected $connection = 'order';
    protected $table = 'auto_price';
    public $timestamps = false;
}
