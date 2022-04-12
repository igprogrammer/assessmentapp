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




        {!! Form::open(['url'=>'customers/save-customer','method'=>'post','class'=>'form'])!!}
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Customer name') !!}
                            {!! Form::text('customerName',null,['class'=>'form-control','placeholder'=>'Customer name','required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Customer number') !!}
                            {!! Form::text('companyNumber',null,['class'=>'form-control','placeholder'=>'Customer number','required'=>'required']) !!}
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
                            {!! Form::label('title','Date') !!}
                            {!! Form::text('regDate',null,['class'=>'form-control datepicker','required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Entity type') !!}
                            {!! Form::select('entityType',[''=>'Select type','CMP'=>'Companies','BN'=>'Business Name','TM'=>'Trade and service mark','PT'=>'Patent'],array(),['class'=>'form-control','required'=>'required']) !!}
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
