<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $primaryKey = 'CATEGORY_ID';
    protected $table = "categories";

    public $timestamps = false;
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CATEGORY_NAME', 'DESCRIPTION', 'PT_CATEGORY_ID', 'STATUS', 'CATEGORY_ORDER', 'CREATED_BY', 'CREATED_DATE', 'UPDATED_BY', 'UPDATED_DATE'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

}
