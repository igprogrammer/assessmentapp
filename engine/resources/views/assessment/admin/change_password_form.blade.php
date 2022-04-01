@extends('layouts.master')

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




    {!! Form::open(['url'=>'change-password','method'=>'post','class'=>'form'])!!}


    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
        {!! Form::label('title','Your username/email please') !!}
        {!! Form::text('email',$employee->email,['class'=>'form-control','placeholder'=>'username/email','readonly']) !!}
    </div>
    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
        {!! Form::label('title','New password') !!}
        {!! Form::password('password',['class'=>'form-control','placeholder'=>'New password']) !!}
    </div>
    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
        {!! Form::label('title','Confirm password') !!}
        {!! Form::password('password_confirmation', array('class'=>'form-control','placeholder'=>'Confirm password')) !!}
    </div>



    <div class="form-group">
        {{ Form::hidden('id',$employee->id) }}
        {!! Form::submit('Update',['class'=>'btn btn-success']) !!}
    </div>

    {!!Form::close()!!}

    </div>

@stop
