<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kunnr extends Model
{
    //
    protected $table = 'sap_kunnr';
	protected $guarded = [];
    public $timestamps = false;
}
