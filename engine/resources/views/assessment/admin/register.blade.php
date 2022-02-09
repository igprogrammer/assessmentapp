@extends('...layouts.admin')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">



        @if(Session::has('success-message'))

            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! Session::get('success-message') !!}
            </div>
        @endif

        @if(Session::has('error-message'))

            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! Session::get('error-message') !!}
                <p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </p>
            </div>
        @endif




    {!! Form::open(['url'=>'register','method'=>'post','class'=>'form'])!!}
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Employee Name') !!}
                        {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Name']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Username') !!}
                        {!! Form::text('username',null,['class'=>'form-control','placeholder'=>'Username']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Password') !!}
                        {!! Form::password('password', array('placeholder'=>'Password', 'class'=>'form-control' ) ) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Employee Type') !!}
                        {!! Form::select('role',array(''=>'Select','0'=>'Admin','1'=>'Assessor'),array(),['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Reports to') !!}
                        {!! Form::select('reportsTo',array(''=>'Select','DRCL'=>'DRCL'),array(),['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::submit('Register',['class'=>'btn btn-success']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!!Form::close()!!}

    </div>

@stop