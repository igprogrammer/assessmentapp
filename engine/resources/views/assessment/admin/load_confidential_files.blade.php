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
            <p>

                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            <p>{!! Session::get('error-message') !!}
            <ul>
                {!! $errors->first('confidential_file', '<li>:message</li>') !!}
            </ul>
            </p>
    </div>
    </p>
    @endif




    {!! Form::open(['url'=>'load-confidential-files','method'=>'post','class'=>'form','files'=>true])!!}
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Import file') !!}
                            {!! Form::file('confidential_file',null,['class'=>'form-control','placeholder'=>'Import file']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Type') !!}
                            {!! Form::select('type', array(''=>'Select type','confidential'=>'Confidential', 'investigation'=>'Investigation'), array(),['class'=>'form-control','required'=>'required']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{--<div class="container-fluid">--}}
    {{--<div class="row-fluid">--}}
    {{--<div class="col-md-12">--}}
    {{--<div class="col-md-6">--}}
    {{--<div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">--}}
    {{--{!! Form::label('title','Employee Picture') !!}--}}
    {{--{!! Form::file('Employee_Picture', null, array('class'=>'form-control','id'=>'Employee_Picture')) !!}--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::submit('Load files',['class'=>'btn btn-success']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!!Form::close()!!}

    </div>

@stop