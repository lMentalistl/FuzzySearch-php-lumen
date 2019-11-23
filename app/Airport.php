<?php


namespace App;


use Illuminate\Database\Eloquent\Model;


class Airport extends Model
{
    protected $fillable = [
        'cityName', 'area', 'country', 'lat','lng','timezone',
    ];
}
