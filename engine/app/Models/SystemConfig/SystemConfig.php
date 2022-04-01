<?php

namespace App\Models\SystemConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    use HasFactory;

    protected $primaryKey = 'configId';

    protected $table = 'sysConfig';

    protected $guarded = ['configId'];

    public static function invoiceGeneration(){
        return SystemConfig::first();
    }

}
