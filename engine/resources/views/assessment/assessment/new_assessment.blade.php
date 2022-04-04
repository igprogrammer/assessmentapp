@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        @if($tempStatus == 1)

            @if($payment_id != 0)

                @if((int)$payment->invoice >= 991350000000)
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <a onclick="print_assessment('{{ encrypt($payment_id) }}','normal')" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Print normal bill</a>
                        <a onclick="print_assessment('{{ encrypt($payment_id) }}','nmb')" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> NMB transfer</a>
                        <a onclick="print_assessment('{{ encrypt($payment_id) }}','crdb')" class="btn btn-warning"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a>
                        <a onclick="print_assessment('{{ encrypt($payment_id) }}','nbc')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> NBC transfer</a>
                    </div>
                @endif

            @endif

        @endif



{{--{!! Form::open(array('url'=>'assessments/save-assessment','method'=>'post','files'=>true)) !!}
{!! csrf_field() !!}--}}
        <form id="generate-invoice" class="generate-invoice"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            @csrf

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
                                {!! Form::label('title','Company number') !!}
                                {!! Form::text('company_number',null,['class'=>'form-control','id'=>'company_number','placeholder'=>'Company number']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Company name') !!}
                                {!! Form::text('company_name',null,['class'=>'form-control','id'=>'company_name','placeholder'=>'Company name']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                                {!! Form::label('title','Date') !!}
                                {!! Form::text('filing_date',date('Y-m-d'),['class'=>'form-control datepicker','id'=>'filing_date']) !!}
                            </div>
                        </div>
                    </div>

                    @include('assessment.assessment.item_selection')

                    <div id="selected_items">

                    </div>





                </div>
            </div>






    <div id="generate_invoice" style="visibility: visible">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-6">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Attach assessment form') !!}
                        {!! Form::file('assessment_form_file',null,['class'=>'form-control','id'=>'assessment_form_file','required'=>'required']) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php $config = \App\Http\Controllers\Assessment\GeneralController::invoiceGeneration(); ?>
                            @if(!empty($config))
                                @if($config->invoiceGeneration == 0)
                                    {!! Form::hidden('tempStatus',1) !!}
                                    {!! Form::submit('Generate invoice',['class'=>'btn btn-success','id'=>'generateInvoice']) !!}
                                @elseif($config->invoiceGeneration == 1)

                                    @if(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 1)
                                            {!! Form::hidden('tempStatus',1) !!}
                                            {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                    @else
                                            {!! Form::hidden('tempStatus',2) !!}
                                            {!! Form::submit('Forward to supervisor',['class'=>'btn btn-primary']) !!}
                                    @endif
                                @else
                                        @if(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 1)
                                            {!! Form::hidden('tempStatus',3) !!}
                                            {!! Form::submit('Forward to accounts',['class'=>'btn btn-primary']) !!}
                                        @elseif(\Illuminate\Support\Facades\Auth::user()->isSupervisor == 2)
                                            {!! Form::hidden('tempStatus',1) !!}
                                            {!! Form::submit('Generate invoice',['class'=>'btn btn-success']) !!}
                                        @else
                                            {!! Form::hidden('tempStatus',2) !!}
                                            {!! Form::submit('Forward to supervisor',['class'=>'btn btn-primary']) !!}
                                        @endif

                                @endif
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>

    </div>

    <script>

        //function to add fee
        function add_fee(){
            var company_number = document.getElementById('company_number').value;
            var company_name = document.getElementById('company_name').value;
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
                            var filing = response.filing_date;
                            var phone_number = response.phone_number;
                            var expire_days = response.expire_days;
                            var number_of_files = response.number_of_files;
                            var calculationType = response.calculationType;
                            var licenceType = response.licenceType;
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


                                            document.getElementById('company_number').value = company_number;
                                            document.getElementById('company_name').value = company_name;
                                            document.getElementById('filing_date').value = filing_date;
                                            document.getElementById('phone_number').value = phone_number;
                                            document.getElementById('expire_days').value = expire_days;
                                            document.getElementById('number_of_files').value = number_of_files;
                                            document.getElementById('calculationType').value = calculationType;
                                            document.getElementById('licenceType').value = licenceType;
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
                myObject.open('GET','{{ URL::route("add-assessment-fee") }}?licenceType='+licenceType+'&calculationType='+calculationType+'&number_of_files='+number_of_files+'&expire_days='+expire_days+'&phone_number='+phone_number+'&charge_days='+charge_days+'&company_number='+company_number+'&company_name='+company_name+'&filing_date='+filing_date+'&division_id='+division_id+'&fee_account_id='+fee_account_id+'&fee_id='+fee_id+'&item_id='+item_id+'&year='+year+'&item_name='+item_name+'&currency='+currency+'&item_amount='+item_amount+'&penalty_amount='+penalty_amount,true);
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



        //get selected item contents
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
