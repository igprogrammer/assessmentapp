@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">



        {!! Form::open(['url'=>'divisions/save-division','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Division details</div>
                <div class="panel-body">
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

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Division code') !!}
                                {!! Form::text('division_code',null,['class'=>'form-control','placeholder'=>'Division code']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Division name') !!}
                                {!! Form::text('division_name',null,['class'=>'form-control','placeholder'=>'Division name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Description') !!}
                                {!! Form::text('description',null,['class'=>'form-control','placeholder'=>'Description']) !!}
                            </div>
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
                            {!! Form::submit('Add division',['class'=>'btn btn-success']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {!!Form::close()!!}

    </div>



@stop
