@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">



        {!! Form::open(['route'=>'update-fee','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Fee account details</div>
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
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Division') !!}
                                {!! Form::select('division_id',[''=>'Select division']+$divisions,$division->id,['class'=>'form-control','onchange'=>'get_fee_accounts()','id'=>'division_id']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Account') !!}
                                {!! Form::select('fee_account_id',[''=>'Select account']+$feeAccounts,$fee->fee_account_id,['class'=>'form-control','id'=>'fee_account_id','onchange'=>'get_code()']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Fee code') !!}
                                {!! Form::text('fee_code',$fee->fee_code,['class'=>'form-control','placeholder'=>'Fee code']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Fee name') !!}
                                {!! Form::text('fee_name',$fee->fee_name,['class'=>'form-control','placeholder'=>'Fee name']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Account code') !!}
                                {!! Form::text('account_code',$fee->account_code,['class'=>'form-control','placeholder'=>'Account code','id'=>'account_code','readonly']) !!}
                            </div>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Type') !!}
                                {!! Form::select('type',[''=>'Select type','new'=>'New','change'=>'Change'],$fee->type,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','GFS code') !!}
                                {!! Form::text('gfs_code',$fee->gfs_code,['class'=>'form-control','placeholder'=>'GFS code']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Has form') !!}
                                {!! Form::select('has_form',[''=>'Select option','no'=>'No','yes'=>'Yes'],$fee->has_form,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Amount') !!}
                                {!! Form::text('amount',$fee->amount,['class'=>'form-control','placeholder'=>'Amount']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Currency') !!}
                                {!! Form::select('currency',[''=>'Select currency']+$currencies,$fee->currency,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Is active') !!}
                                {!! Form::select('active',[''=>'Select status','yes'=>'Yes','no'=>'No'],$fee->active,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Define fee amount?') !!}
                                {!! Form::select('defineFeeAmount',[''=>'Select status','1'=>'Yes','0'=>'No'],$fee->defineFeeAmount,['class'=>'form-control','required'=>'required']) !!}
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
                            {!! Form::hidden('feeId',$fee->id) !!}
                            {!! Form::submit('Update fee',['class'=>'btn btn-success']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {!!Form::close()!!}

    </div>

    <script>

        function get_code(){
            var fee_account_id = document.getElementById('fee_account_id').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                var response = JSON.parse(data);
                if (myObject.readyState == 4) {
                    if(response.success == 1){
                        document.getElementById('account_code').value = response.account_code;
                    }else{
                        alert('Failed');
                    }

                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("get-code") }}?fee_account_id='+fee_account_id,true);
            myObject.send();
        }

        function get_fee_accounts(){
            var division_id = document.getElementById('division_id').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('fee_account_id').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("get-fee-accounts") }}?division_id='+division_id,true);
            myObject.send();
        }
    </script>



@stop
