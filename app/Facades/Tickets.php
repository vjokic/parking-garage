<?php
/**
 * Created by PhpStorm.
 * User: vjokic
 * Date: 2017-03-28
 * Time: 10:51 AM
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class Tickets extends Facade
{
    protected static function getFacadeAccessor(){
        return 'tickets';
    }

}