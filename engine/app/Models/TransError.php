<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransError extends Model
{
    use HasFactory;

    public static function saveTransError($content){
        $data = new TransError();
        $data->error_content = $content;
        $data->save();
    }
}
