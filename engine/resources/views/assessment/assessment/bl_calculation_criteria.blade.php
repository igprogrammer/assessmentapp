<div class="col-md-4">
    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
        {!! Form::label('title','Majority ownership') !!}
        {!! Form::select('calculationType',[''=>'Select type','1'=>'Local','2'=>'Foreign'],array(),['class'=>'form-control','id'=>'calculationType']) !!}
    </div>
</div>
<div class="col-md-4">
    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
        {!! Form::label('title','Licence type') !!}
        {!! Form::select('licenceType',[''=>'Select type','1'=>'Principal','2'=>'Branch'],array(),['class'=>'form-control','id'=>'licenceType']) !!}
    </div>
</div>
