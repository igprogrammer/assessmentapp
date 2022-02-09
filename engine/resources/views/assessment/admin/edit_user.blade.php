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

                @if($errors->has())

                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            <p>{!! Session::get('error-message') !!}
            <ul>
                {!! $errors->first('Employee_Name', '<li>:message</li>') !!}
                {!! $errors->first('email', '<li>:message</li>') !!}
                {!! $errors->first('Employee_Type', '<li>:message</li>') !!}
            </ul>
            </p>
    </div>
    </p>

    @endif
    @endif




    {!! Form::open(['url'=>'updateEmployee','method'=>'post','class'=>'form'])!!}
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Employee Name') !!}
                            {!! Form::text('Employee_Name',$employee->Employee_Name,['class'=>'form-control','placeholder'=>'Name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Email') !!}
                            {!! Form::text('email',$employee->email,['class'=>'form-control','placeholder'=>'Username']) !!}
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
                            {!! Form::label('title','Employee Type') !!}
                            {!! Form::select('Employee_Type',array(''=>'','receptionist'=>'Receptionist','actionOfficer'=>'Action officer','drcl'=>'DRCL','registryOfficer'=>'Registry officer','deputyRegistry'=>'Deputy registry','admin'=>'Administrator'),$employee->Employee_Type,['class'=>'form-control','placeholder'=>'File number']) !!}
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
                        {!! Form::hidden('id',$employee->id) !!}
                        {!! Form::submit('Update',['class'=>'btn btn-success']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!!Form::close()!!}

    </div>

@stop