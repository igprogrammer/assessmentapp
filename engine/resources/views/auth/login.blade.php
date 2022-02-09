@extends('...layouts.login')
@section('content')
<div id="login" class="container auth answers"">

@if(Session::has('success-message'))
    <br>

    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {!! Session::get('success-message') !!}
    </div>
@endif

@if(Session::has('error-message'))
    <br>

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

@if(Session::has('wrong-creds'))
    <p>
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{!! Session::get('wrong-creds') !!}</p>
        </div>
    </p>
@endif



{!! Form::open(array('url'=>'authenticate')) !!}
{!! csrf_field() !!}
    <div style="padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;background-color: white;margin-left:3px;margin-right: 3px;margin-bottom: 0px;">
        <div class="form-group" style="margin-top: 5px;margin-left: 5px;margin-right: 5px">
            {!! Form::text('email',null, ['class' => 'form-control topic','placeholder'=>'Username']) !!}
        </div>
        <div class="form-group" style="margin-bottom: 5px;margin-left: 5px;margin-right: 5px">
            {!! Form::password('password', array('placeholder'=>'Password', 'class'=>'form-control' ) ) !!}
        </div>
        <div class="form-group" style="margin-bottom: 5px;margin-left: 5px;margin-right: 5px">
            {!! Form::submit('Login',['class'=>'btn btn-warning fonts width'])!!}
        </div>
    </div>
    </div>
{!! Form::close() !!}

@endsection