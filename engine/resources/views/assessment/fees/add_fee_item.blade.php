@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">



        {!! Form::open(['url'=>'fees/save-fee-item','method'=>'post','class'=>'form','files'=>true])!!}

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
                                {!! Form::select('division_id',[''=>'Select division']+$divisions,array(),['class'=>'form-control','onchange'=>'get_fee_accounts()','id'=>'division_id']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Account') !!}
                                {!! Form::select('fee_account_id',[''=>'Select account'],array(),['class'=>'form-control','id'=>'fee_account_id','onchange'=>'get_fees()']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Fee') !!}
                                {!! Form::select('fee_id',[''=>'Select fee'],array(),['class'=>'form-control','id'=>'fee_id']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Item name') !!}
                                {!! Form::text('item_name',null,['class'=>'form-control','placeholder'=>'Item name']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">


                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Item amount') !!}
                                {!! Form::text('item_amount',null,['class'=>'form-control','placeholder'=>'Item amount']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Penalty amount') !!}
                                {!! Form::text('penalty_amount',null,['class'=>'form-control','placeholder'=>'Penalty amount','id'=>'penalty_amount']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Days') !!}
                                {!! Form::text('days',null,['class'=>'form-control','placeholder'=>'Days before penalty']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Copy charge') !!}
                                {!! Form::text('copy_charge',null,['class'=>'form-control','placeholder'=>'Copy charge']) !!}
                            </div>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Stamp duty amount') !!}
                                {!! Form::text('stamp_duty_amount',null,['class'=>'form-control','placeholder'=>'Stamp duty amount']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Currency') !!}
                                {!! Form::select('currency',[''=>'Select currency']+$currencies,array(),['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">

                    </div>

                </div>
            </div>
        </div>






        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::submit('Add item',['class'=>'btn btn-success']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {!!Form::close()!!}

    </div>

    <script>

        function get_fees(){
            var fee_account_id = document.getElementById('fee_account_id').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('fee_id').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("get-fees") }}?fee_account_id='+fee_account_id,true);
            myObject.send();
        }

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
