<?php namespace rondeobalos\model;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model {
    protected $table = 'tags';

    protected $fillable = ['tag','color'];
}