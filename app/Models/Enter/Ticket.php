<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 11:13
 */

namespace App\Models\Enter;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'mysql_enter';

    protected $table = 'ticket';
}