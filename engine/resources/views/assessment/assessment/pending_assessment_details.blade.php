@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        @if($payment_id != 0)

            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <a onclick="print_assessment('{{ encrypt($payment_id) }}','normal')" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Print normal bill</a>
                <a onclick="print_assessment('{{ encrypt($payment_id) }}','nmb')" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> NMB transfer</a>
                <a onclick="print_assessment('{{ encrypt($payment_id) }}','crdb')" class="btn btn-warning"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a>
                <a onclick="print_assessment('{{ encrypt($payment_id) }}','nbc')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> NBC transfer</a>


            </div>

        @endif



{{--        {!! Form::open(array('url'=>'assessments/save-assessment','method'=>'post','files'=>true)) !!}--}}
{{--        {!! csrf_field() !!}--}}

            <form id="generate-invoice" class="generate-invoice"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
                @csrf
        <div class="col-md-12">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">New assessment details</div>
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
                                {!! Form::label('title','Entity type') !!}
                                {!! Form::select('entityType',[''=>'Select type','CMP'=>'Company','BN'=>'Business Name','TM'=>'Trade and service mark','PT'=>'Patent'],$temp_payment->entityType,['class'=>'form-control','id'=>'entityType']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Entity number') !!}
                                {!! Form::text('company_number',$temp_payment->company_number,['class'=>'form-control','id'=>'company_number','placeholder'=>'Company number','onchange'=>'checkEntityType()']) !!}

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Company name') !!}
                                {!! Form::text('company_name',$temp_payment->company_name,['class'=>'form-control','id'=>'company_name','placeholder'=>'Company name','onchange'=>'checkEntityType()']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Inc/Reg date') !!}
                                {!! Form::text('regDate',$temp_payment->regDate,['class'=>'form-control datepicker','id'=>'regDate','placeholder'=>'Incorporation or Reg date']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Date') !!}
                                {!! Form::text('filing_date',date('Y-m-d', strtotime($temp_payment->filing_date)),['class'=>'form-control datepicker','id'=>'filing_date']) !!}
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <!-- Default panel contents -->
                            <div class="panel-heading">Item selection</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                            {!! Form::label('title','Division') !!}
                                            {!! Form::select('division_id',[''=>'Select division']+$divisions,$division->id,['class'=>'form-control','onchange'=>'get_fee_accounts()','id'=>'division_id']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                            {!! Form::label('title','Account') !!}
                                            {!! Form::select('fee_account_id',[''=>'Select account']+$fee_accounts,$feeAcc->id,['class'=>'form-control','id'=>'fee_account_id','onchange'=>'get_fees()']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                            {!! Form::label('title','Fee') !!}
                                            {!! Form::select('fee_id',[''=>'Select fee']+$fees,array(),['class'=>'form-control','id'=>'fee_id','onchange'=>'get_items()']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                            {!! Form::label('title','Item') !!}
                                            {!! Form::select('item_id',[''=>'Select item'],array(),['class'=>'form-control','id'=>'item_id','onchange'=>'display_fields()']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div  id="filing_year" style="display: none">
                                    <div class="col-md-12">
                                        <br>
                                        <div class="col-md-3">

                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                <label>Year</label>
                                                <?php
                                                $years = array();
                                                $current_date = date('Y-m-d');
                                                $current_year = date('Y');
                                                $start_year = '1960';

                                                for ($i=1;$start_year+$i <= $current_year;$i++){
                                                    $years[$start_year + $i] = $start_year + $i;
                                                }

                                                ?>

                                                {!! Form::select('year',[''=>'Select year']+$years,array(),['class'=>'form-control','id'=>'year']) !!}


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div  id="calculate_fee_button" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Majority ownership') !!}
                                                {!! Form::select('calculationType',[''=>'Select type','1'=>'Local','2'=>'Foreign'],$temp_payment->calculationType,['class'=>'form-control','id'=>'calculationType']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Licence type') !!}
                                                {!! Form::select('licenceType',[''=>'Select type','1'=>'Principal','2'=>'Branch'],$temp_payment->licenceType,['class'=>'form-control','id'=>'licenceType']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Phone number') !!}
                                                {!! Form::text('phone_number',$temp_payment->phone_number,['class'=>'form-control','id'=>'phone_number','placeholder'=>'Phone number']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Expire days') !!}
                                                {!! Form::text('expire_days',$temp_payment->expire_days,['class'=>'form-control','id'=>'expire_days','placeholder'=>'Expire days']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Number of files') !!}
                                                {!! Form::number('number_of_files',1,['class'=>'form-control','id'=>'number_of_files','required','min'=>'1']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                {!! Form::label('title','Item fee') !!}
                                                {!! Form::text('item_fee',null,['class'=>'form-control','id'=>'item_fee','placeholder'=>'Please specify item amount here']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                                <br><a class="btn btn-success" onclick="calculate_fee()">Calculate fee</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div  id="item_contents" style="display: none;">
                                    <div class="col-md-12"><b style="text-decoration: underline;">Item details</b>
                                        <hr>

                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>
                                                    Item name
                                                </th>
                                                <th>
                                                    Currency
                                                </th>
                                                <th>
                                                    Item amount
                                                </th>
                                                <th>
                                                    Penalty amount
                                                </th>
                                                <th>
                                                    Charge days
                                                </th>
                                                <th>
                                                    Action
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Item name" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="currency" id="currency" class="form-control" placeholder="Currency" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="item_amount" id="item_amount" class="form-control" placeholder="Item amount" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="penalty_amount" id="penalty_amount" class="form-control" placeholder="Penalty amount" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="charge_days" id="charge_days" class="form-control" placeholder="Charge days" readonly>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success" onclick="add_fee()"> Add fee</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                </div>


                            </div>

                        </div>
                    </div>

                    <div id="selected_items">

                        @include('assessment.assessment.get_selected_items')

                    </div>



                </div>
            </div>
        </div>






        <div id="generate_invoice" style="visibility: visible">
            <div class="container-fluid">
                <div class="row-fluid">
                    @if($temp_payment->status == 0)
                        <div class="col-md-6">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Attach assessment form') !!}
                                {!! Form::file('assessment_form_file',null,['class'=>'form-control','id'=>'assessment_form_file','required'=>'required']) !!}
                            </div>
                        </div>
                    @else
                        @include('assessment.assessment.attachment_list')
                    @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php $config = \App\Http\Controllers\Assessment\GeneralController::invoiceGeneration(); ?>
                                @if(!empty($config))
                                    @if($config->invoiceGeneration == 0)

                                        {!! Form::hidden('tempStatus',1) !!}
                                        {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}

                                    @elseif($config->invoiceGeneration == 1)

                                            @if(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 1)

                                                @if(in_array($temp_payment->status, array(2,3)))
                                                    {!! Form::hidden('tempStatus',1) !!}
                                                    {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                                @else
                                                    @if($temp_payment->user_id == \Illuminate\Support\Facades\Auth::user()->id)
                                                        {!! Form::hidden('tempStatus',1) !!}
                                                        {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                                    @else
                                                        <a href="{{ url('assessments/pending') }}" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> Back to list</a>
                                                    @endif
                                                @endif

                                            @else
                                                @if($temp_payment->status == 0)
                                                    {!! Form::hidden('tempStatus',2) !!}
                                                    {!! Form::submit('Forward to supervisor',['class'=>'btn btn-primary']) !!}
                                                @else
                                                    <a href="{{ url('assessments/pending') }}" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> Back to list</a>
                                                @endif
                                            @endif

                                    @else

                                        @if(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 1)
                                                @if($temp_payment->status == 2)
                                                    {!! Form::hidden('tempStatus',3) !!}
                                                    {!! Form::submit('Forward to accounts',['class'=>'btn btn-primary']) !!}
                                                @else

                                                    @if($temp_payment->user_id == \Illuminate\Support\Facades\Auth::user()->id)
                                                        {!! Form::hidden('tempStatus',3) !!}
                                                        {!! Form::submit('Forward to accounts',['class'=>'btn btn-primary']) !!}
                                                    @else
                                                        <a href="{{ url('assessments/pending') }}" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> Back to list</a>
                                                    @endif

                                                @endif
                                        @elseif(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 2)
                                                @if($temp_payment->status == 3)
                                                    {!! Form::hidden('tempStatus',1) !!}
                                                    {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                                @else
                                                    <a href="{{ url('assessments/pending') }}" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> Back to list</a>
                                                @endif
                                        @else

                                            @if($temp_payment->status == 0)
                                                    {!! Form::hidden('tempStatus',2) !!}
                                                    {!! Form::submit('Forward to supervisor',['class'=>'btn btn-primary']) !!}
                                            @else
                                                <a href="{{ url('assessments/pending') }}" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> Back to list</a>
                                            @endif

                                                {{--@if($temp_payment->status == 0)
                                                    {!! Form::hidden('tempStatus',2) !!}
                                                    {!! Form::submit('Forward invoice',['class'=>'btn btn-primary']) !!}
                                                @else
                                                    {!! Form::hidden('tempStatus',1) !!}
                                                    {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                                @endif--}}
                                        @endif

                                    @endif
                                @endif

                            </div>
                        </div>

                </div>
            </div>
        </div>

        {{--<div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a onclick="display_generate_invoice()" class="btn btn-primary">Done</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}


                <input type="hidden" name="temp_payment_id" value="{{ $temp_payment->id }}" id="temp_payment_id">

        </form>

    </div>

    <script>

        //function to add fee
        function add_fee(){
            var company_number = document.getElementById('company_number').value;
            var company_name = document.getElementById('company_name').value;
            var entityType = document.getElementById('entityType').value;
            var regDate = document.getElementById('regDate').value;
            var filing_date = document.getElementById('filing_date').value;
            var division_id = document.getElementById('division_id').value;
            var fee_account_id = document.getElementById('fee_account_id').value;
            var fee_id = document.getElementById('fee_id').value;
            var item_id = document.getElementById('item_id').value;
            var year = document.getElementById('year').value;
            var item_name = document.getElementById('item_name').value;
            var currency = document.getElementById('currency').value;
            var item_amount = document.getElementById('item_amount').value;
            var penalty_amount = document.getElementById('penalty_amount').value;
            var charge_days = document.getElementById('charge_days').value;
            var number_of_files = document.getElementById('number_of_files').value;

            var phone_number = document.getElementById('phone_number').value;
            var expire_days = document.getElementById('expire_days').value;
            var calculationType = document.getElementById('calculationType').value;
            var licenceType = document.getElementById('licenceType').value;

            if (document.getElementById('item_name').value == null || document.getElementById('item_name').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please select an item to add to this assessment...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }
            else if (document.getElementById('company_number').value == null || document.getElementById('company_number').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter company number...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }
            else if (document.getElementById('company_name').value == null || document.getElementById('company_name').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter company name...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }
            else if (document.getElementById('filing_date').value == null || document.getElementById('filing_date').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter start filing year...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }/*else if (document.getElementById('year').value == null || document.getElementById('year').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please select form or assessment begining year...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }*/
            else if (document.getElementById('phone_number').value == null || document.getElementById('phone_number').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter phone number...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }
            else if (document.getElementById('expire_days').value == null || document.getElementById('expire_days').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter expire days...",
                    title: "&nbsp;&nbsp;Action information",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }
            else{


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
                        if (response.success == 1){
                            var company_number = response.company_number;
                            var company_name = response.company_name;
                            var company_name = response.company_name;
                            var filing = response.filing_date;
                            var phone_number = response.phone_number;
                            var expire_days = response.expire_days;
                            var number_of_files = response.number_of_files;
                            var calculationType = response.calculationType;
                            var licenceType = response.licenceType;
                            var entityType = response.entityType;
                            var regDate = response.regDate;
                            bootbox.dialog({
                                closeButton: false,
                                message: "&nbsp;&nbsp;&nbsp;The fee item is successfully added to this assessment,click Okay to continue...",
                                title: "&nbsp;&nbsp;Success message",
                                buttons: {
                                    main: {
                                        label: "Okay",
                                        className: "btn-primary",
                                        callback: function() {

                                            document.getElementById('company_number').value = company_number;
                                            document.getElementById('company_name').value = company_name;
                                            document.getElementById('filing_date').value = filing_date;
                                            document.getElementById('phone_number').value = phone_number;
                                            document.getElementById('expire_days').value = expire_days;
                                            document.getElementById('number_of_files').value = number_of_files;
                                            document.getElementById('calculationType').value = calculationType;
                                            document.getElementById('licenceType').value = licenceType;
                                            document.getElementById('entityType').value = entityType;
                                            document.getElementById('regDate').value = regDate;
                                            //document.getElementById('temp_payment_id').value = filing_date;


                                            document.getElementById('filing_year').style.display = 'none';
                                            document.getElementById('item_contents').style.display = 'none';
                                            document.getElementById('calculate_fee_button').style.display = 'none';

                                            document.getElementById('item_name').value = '';
                                            document.getElementById('item_amount').value = '';
                                            document.getElementById('penalty_amount').value = '';
                                            //document.getElementById('copy_charge').value = '';
                                            document.getElementById('charge_days').value = '';
                                            document.getElementById('currency').value = '';
                                            document.getElementById('year').value = '';

                                            document.getElementById('fee_id').value = '';
                                            document.getElementById('item_id').value = '';

                                            if(window.XMLHttpRequest) {
                                                myObject = new XMLHttpRequest();
                                            }else if(window.ActiveXObject){
                                                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                                                myObject.overrideMimeType('text/xml');
                                            }

                                            myObject.onreadystatechange = function (){
                                                data = myObject.responseText;
                                                if (myObject.readyState == 4) {
                                                    document.getElementById('selected_items').innerHTML = data;
                                                }
                                            }; //specify name of function that will handle server response........
                                            myObject.open('GET','{{ URL::route("get-selected-items") }}?company_number='+company_number,true);
                                            myObject.send();

                                        }
                                    }
                                }
                            });
                        }else if (response.success == 4){
                            bootbox.dialog({
                                closeButton: false,
                                message: "&nbsp;&nbsp;&nbsp;Sorry,this item has been added to this assessment,please choose another item...",
                                title: "&nbsp;&nbsp;Info message",
                                buttons: {
                                    main: {
                                        label: "Okay",
                                        className: "btn-primary",
                                        callback: function() {
                                            var company_number = response.company_number;
                                            var company_name = response.company_name;
                                            var filing = response.filing_date;
                                            var phone_number = response.phone_number;
                                            var expire_days = response.expire_days;
                                            var number_of_files = response.number_of_files;
                                            var calculationType = response.calculationType;
                                            var licenceType = response.licenceType;
                                            var entityType = response.entityType;
                                            var regDate = response.regDate;


                                            document.getElementById('company_number').value = company_number;
                                            document.getElementById('company_name').value = company_name;
                                            document.getElementById('filing_date').value = filing_date;
                                            document.getElementById('phone_number').value = phone_number;
                                            document.getElementById('expire_days').value = expire_days;
                                            document.getElementById('number_of_files').value = number_of_files;
                                            document.getElementById('calculationType').value = calculationType;
                                            document.getElementById('licenceType').value = licenceType;
                                            document.getElementById('entityType').value = entityType;
                                            document.getElementById('regDate').value = regDate;
                                            //document.getElementById('temp_payment_id').value = filing_date;


                                            document.getElementById('filing_year').style.display = 'none';
                                            document.getElementById('item_contents').style.display = 'none';
                                            document.getElementById('calculate_fee_button').style.display = 'none';

                                            document.getElementById('item_name').value = '';
                                            document.getElementById('item_amount').value = '';
                                            document.getElementById('penalty_amount').value = '';
                                            //document.getElementById('copy_charge').value = '';
                                            document.getElementById('charge_days').value = '';
                                            document.getElementById('currency').value = '';
                                            document.getElementById('year').value = '';

                                            if(window.XMLHttpRequest) {
                                                myObject = new XMLHttpRequest();
                                            }else if(window.ActiveXObject){
                                                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                                                myObject.overrideMimeType('text/xml');
                                            }

                                            myObject.onreadystatechange = function (){
                                                data = myObject.responseText;
                                                if (myObject.readyState == 4) {
                                                    document.getElementById('selected_items').innerHTML = data;
                                                }
                                            }; //specify name of function that will handle server response........
                                            myObject.open('GET','{{ URL::route("get-selected-items") }}?company_number='+company_number,true);
                                            myObject.send();



                                        }
                                    }
                                }
                            });
                        }else if (response.success == 2){
                            bootbox.dialog({
                                closeButton: false,
                                message: "&nbsp;&nbsp;&nbsp;No fee record found...",
                                title: "&nbsp;&nbsp;Info message",
                                buttons: {
                                    main: {
                                        label: "Okay",
                                        className: "btn-primary",
                                        callback: function() {
                                            //do something else
                                            window.location.reload(true);
                                            return true;
                                        }
                                    }
                                }
                            });
                        }else{
                            bootbox.dialog({
                                closeButton: false,
                                message: "&nbsp;&nbsp;&nbsp;Failed to add item,try again...",
                                title: "&nbsp;&nbsp;Failure message",
                                buttons: {
                                    main: {
                                        label: "Okay",
                                        className: "btn-primary",
                                        callback: function() {
                                            //do something else
                                            window.location.reload(true);
                                            return true;
                                        }
                                    }
                                }
                            });
                        }
                    }
                }; //specify name of function that will handle server response........
                myObject.open('GET','{{ URL::route("add-assessment-fee") }}?regDate='+regDate+'&entityType='+entityType+'&licenceType='+licenceType+'&calculationType='+calculationType+'&number_of_files='+number_of_files+'&expire_days='+expire_days+'&phone_number='+phone_number+'&charge_days='+charge_days+'&company_number='+company_number+'&company_name='+company_name+'&filing_date='+filing_date+'&division_id='+division_id+'&fee_account_id='+fee_account_id+'&fee_id='+fee_id+'&item_id='+item_id+'&year='+year+'&item_name='+item_name+'&currency='+currency+'&item_amount='+item_amount+'&penalty_amount='+penalty_amount,true);
                myObject.send();
            }


        }


        function display_fields(){

            var item_id = document.getElementById('item_id').value;
            if (item_id != '' || item_id != null){

                myObject.onreadystatechange = function (){
                    data = myObject.responseText;
                    var response = JSON.parse(data);
                    if (myObject.readyState == 4) {
                        if(response.success == 1){
                            if (response.has_form == 'yes'){
                                document.getElementById('filing_year').style.display = 'block';
                                document.getElementById('item_contents').style.display = 'block';
                                document.getElementById('calculate_fee_button').style.display = 'block';

                                document.getElementById('item_name').value = '';
                                document.getElementById('item_amount').value = '';
                                document.getElementById('penalty_amount').value = '';
                                //document.getElementById('copy_charge').value = '';
                                document.getElementById('charge_days').value = '';
                                document.getElementById('currency').value = '';
                                document.getElementById('year').value = '';

                                if (response.defineFeeAmount == '1'){
                                    document.getElementById('item_fee').value = '';
                                }else{
                                    document.getElementById('item_fee').value = response.item_amount;
                                }


                            }else{
                                document.getElementById('filing_year').style.display = 'none';
                                document.getElementById('item_contents').style.display = 'block';
                                document.getElementById('calculate_fee_button').style.display = 'block';

                                document.getElementById('item_name').value = '';
                                document.getElementById('item_amount').value = '';
                                document.getElementById('penalty_amount').value = '';
                                //document.getElementById('copy_charge').value = '';
                                document.getElementById('charge_days').value = '';
                                document.getElementById('currency').value = '';
                                document.getElementById('year').value = '2022';

                                if (response.defineFeeAmount == '1'){
                                    document.getElementById('item_fee').value = '';
                                }else{
                                    document.getElementById('item_fee').value = response.item_amount;
                                }
                            }
                        }else{
                            document.getElementById('filing_year').style.display = 'none';
                            document.getElementById('item_contents').style.display = 'none';
                            document.getElementById('calculate_fee_button').style.display = 'none';
                        }
                    }
                }; //specify name of function that will handle server response........
                myObject.open('GET','{{ URL::route("display-fields") }}?item_id='+item_id,true);
                myObject.send();
            }else{
                document.getElementById('filing_year').style.display = 'none';
                document.getElementById('item_contents').style.display = 'none';
                document.getElementById('calculate_fee_button').style.display = 'none';
            }

        }



        //calculate fee
        function calculate_fee(){

            var item_id = document.getElementById('item_id').value;
            var filing_date = document.getElementById('filing_date').value;
            var division_id = document.getElementById('division_id').value;
            var fee_account_id = document.getElementById('fee_account_id').value;
            var fee_id = document.getElementById('fee_id').value;
            var year = document.getElementById('year').value;
            var number_of_files = document.getElementById('number_of_files').value;

            if($('#calculationType').val() == ''){
                bootbox.alert('Please select calculation type');
                return false;
            }else {
                var calculationType = $('#calculationType').val();
            }

            if($('#item_fee').val() == ''){
                bootbox.alert('Please define item fee amount');
                return false;
            }else {
                var item_fee = $('#item_fee').val();
            }

            if($('#licenceType').val() == ''){
                bootbox.alert('Please select licence type');
                return false;
            }else {
                var licenceType = $('#licenceType').val();
            }

            //var licenceType = document.getElementById('licenceType').value;

            if (item_id != ''){

                $('.loading').css('display','block');
                $('a[href]').on('click', function(event) { event.preventDefault(); });

                myObject.onreadystatechange = function (){
                    data = myObject.responseText;
                    var response = JSON.parse(data);
                    if (myObject.readyState == 4) {
                        if(response.success == 1){

                            if (response.has_form == 'yes'){
                                document.getElementById('filing_year').style.display = 'block';
                                document.getElementById('item_contents').style.display = 'block';
                                document.getElementById('item_name').value = response.item_name;
                                //document.getElementById('item_amount').value = response.item_amount;
                                document.getElementById('item_amount').value = item_fee;
                                document.getElementById('penalty_amount').value = response.penalty_amount;
                                //document.getElementById('copy_charge').value = response.cp_charge;
                                document.getElementById('charge_days').value = response.days;
                                document.getElementById('currency').value = response.currency;
                                document.getElementById('number_of_files').value = response.number_of_files;

                            }else{
                                document.getElementById('filing_year').style.display = 'none';
                                document.getElementById('item_contents').style.display = 'block';

                                document.getElementById('item_name').value = response.item_name;
                                //document.getElementById('item_amount').value = response.item_amount;
                                document.getElementById('item_amount').value = item_fee;
                                document.getElementById('penalty_amount').value = response.penalty_amount;
                                //document.getElementById('copy_charge').value = response.cp_charge;
                                document.getElementById('charge_days').value = response.days;
                                document.getElementById('currency').value = response.currency;
                                document.getElementById('number_of_files').value = response.number_of_files;
                            }

                        }
                        else{

                            if (response.success == 5){

                                bootbox.dialog({
                                    closeButton: false,
                                    message: "&nbsp;&nbsp;&nbsp;The filing date is less than one year...",
                                    title: "&nbsp;&nbsp;Message",
                                    buttons: {
                                        main: {
                                            label: "Okay",
                                            className: "btn-primary",
                                            callback: function() {
                                                //do something else
                                                return true;
                                            }
                                        }
                                    }
                                });


                            }
                            else if (response.success == 10){

                                bootbox.dialog({
                                    closeButton: false,
                                    message: "&nbsp;&nbsp;&nbsp;The filing date cannot be the same as today's date...",
                                    title: "&nbsp;&nbsp;Message",
                                    buttons: {
                                        main: {
                                            label: "Okay",
                                            className: "btn-primary",
                                            callback: function() {
                                                //do something else
                                                return true;
                                            }
                                        }
                                    }
                                });


                            }else{
                                bootbox.alert(response.message);
                                return false;
                            }




                        }

                        $('.loading').fadeOut(2000, function (){ $('a[href]').unbind("click"); });
                        filterButton.prop('disabled',false);

                    }
                }; //specify name of function that will handle server response........
                myObject.open('GET','{{ url("assessments/calculate-fee") }}?item_amount='+item_fee+'&licenceType='+licenceType+'&calculationType='+calculationType+'&number_of_files='+number_of_files+'&year='+year+'&filing_date='+filing_date+'&division_id='+division_id+'&fee_account_id='+fee_account_id+'&fee_id='+fee_id+'&item_id='+item_id,true);
                myObject.send();

            }else{
                document.getElementById('item_contents').style.display = 'none';
            }



        }

        //check company information
        function check_company_info(){
            var company_number = document.getElementById('company_number').value;
            var company_name = document.getElementById('company_name').value;
            var filing_date = document.getElementById('filing_date').value;

            if (document.getElementById('company_number').value == null){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter Company number  ...",
                    title: "&nbsp;&nbsp;Message",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                //do something else
                                return true;
                            }
                        }
                    }
                });
            }





        }



        function get_items(){
            var fee_id = document.getElementById('fee_id').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('item_id').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("get-items") }}?fee_id='+fee_id,true);
            myObject.send();
        }

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

            if (document.getElementById('company_number').value == '' || document.getElementById('company_name').value == ''){
                bootbox.dialog({
                    closeButton: false,
                    message: "&nbsp;&nbsp;&nbsp;Please enter Company number and Company name  ...",
                    title: "&nbsp;&nbsp;Message",
                    buttons: {
                        main: {
                            label: "Okay",
                            className: "btn-primary",
                            callback: function() {
                                location.reload();
                            }
                        }
                    }
                });
            }else{


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





        }


        function print_assessment(payment_id,type)
        {
            testprintout=window.open("{{ URL::route('print-bill-payment') }}?type="+type+"&payment_id="+payment_id+"","t","width=1000,height=700,menubar=yes,resizable=yes,scrollbars=yes,toolbar=yes,location=no").print();
        }

        //remove item
        function remove_item(temp_item_id,temp_payment_id){

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

                    if (response.success == '1'){

                        var company_number = response.company_number;
                        var company_name = response.company_name;
                        var filing_date = response.filing_date;
                        var phone_number = response.phone_number;
                        var expire_days = response.expire_days;


                        document.getElementById('company_number').value = company_number;
                        document.getElementById('company_name').value = company_name;
                        document.getElementById('filing_date').value = filing_date;
                        document.getElementById('phone_number').value = phone_number;
                        document.getElementById('expire_days').value = expire_days;
                        //document.getElementById('temp_payment_id').value = filing_date;


                        document.getElementById('filing_year').style.display = 'none';
                        document.getElementById('item_contents').style.display = 'none';
                        document.getElementById('calculate_fee_button').style.display = 'none';

                        document.getElementById('item_name').value = '';
                        document.getElementById('item_amount').value = '';
                        document.getElementById('penalty_amount').value = '';
                        //document.getElementById('copy_charge').value = '';
                        document.getElementById('charge_days').value = '';
                        document.getElementById('currency').value = '';
                        document.getElementById('year').value = '';



                        if(window.XMLHttpRequest) {
                            myObject = new XMLHttpRequest();
                        }else if(window.ActiveXObject){
                            myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                            myObject.overrideMimeType('text/xml');
                        }

                        myObject.onreadystatechange = function (){
                            data = myObject.responseText;
                            if (myObject.readyState == 4) {
                                document.getElementById('selected_items').innerHTML = data;
                            }
                        }; //specify name of function that will handle server response........
                        myObject.open('GET','{{ URL::route("get-selected-items") }}?company_number='+company_number,true);
                        myObject.send();


                    }else{

                    }






                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("remove-item") }}?temp_payment_id='+temp_payment_id+'&temp_item_id='+temp_item_id,true);
            myObject.send();
        }

    </script>



@stop
