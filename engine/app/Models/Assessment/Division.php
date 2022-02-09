<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Division extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function updateDivision($divisionId,$division_code,$division_name,$description){
        $division = Division::find($divisionId);
        $division->division_code = $division_code;
        $division->division_name = $division_name;
        $division->description = $description;
        $division->save();

        return $division;
    }

    public static function saveDivision($division_code,$division_name,$description){
        $division = new Division();
        $division->division_code = $division_code;
        $division->division_name = $division_name;
        $division->description = $description;
        $division->user_id = Auth::user()->id;
        $division->save();

        return $division;
    }

    public static $add_rules = array(
        'division_code'=>'required',
        'division_name'=>'required',
        'description'=>'required'
    );
}
