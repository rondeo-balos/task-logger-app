<?php namespace rondeobalos\model;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model {
    protected $table = 'notes';

    protected $fillable = ['index','content'];
}