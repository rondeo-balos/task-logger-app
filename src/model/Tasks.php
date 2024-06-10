<?php namespace rondeobalos\model;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model {
    protected $table = 'tasks';

    protected $fillable = ['title', 'description', 'tag', 'start', 'end'];
}