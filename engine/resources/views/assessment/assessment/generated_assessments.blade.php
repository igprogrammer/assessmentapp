@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        {!! Form::open(['url'=>'assessments/filter','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            {!! Form::open(['url'=>'add-fee','method'=>'post','class'=>'form','files'=>true])!!}
            <table class="table table-striped table-bordered">
                <td>From</td>
                <td>
                    <input type="text" name="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control datepicker">
                </td>
                <td>To</td>
                <td>
                    <input type="text" name="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control datepicker">
                </td>
                <td></td>
                <td>
                    {!! Form::hidden('flag',$flag) !!}
                    {!! Form::submit('Filter',['class'=>'btn btn-success']) !!}
                </td>
            </table>
            <table class="table">
                    <tr>
                        <td>
                            <b>
                                Search item
                            </b>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="entityNumber" onkeyup="search_entity()" id="entityNumber" placeholder="Search by number">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="entityName" onkeyup="search_entity()" id="entityName" placeholder="Search by name">
                        </td>
                    </tr>
                </table>

            {!!Form::close()!!}
            <div class="panel panel-primary">
                <!-- Default panel contents -->

                <div class="panel-body">

                    <div class="col-md-12">

                        @if(Session::has('success-message'))

                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {!! Session::get('success-message') !!}
                            </div>
                        @endif

                        <div  id="assessment-details">
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <th>
                                        SNO
                                    </th>
                                    <th>
                                        Company number
                                    </th>
                                    <th>
                                        Company name
                                    </th>
                                    <th>
                                        Date of assessment
                                    </th>
                                    <th>
                                        Currency
                                    </th>
                                    <th>
                                        Assessment amount
                                    </th>
                                    <th>
                                        Control number
                                    </th>
                                    <th>
                                        Assessed by
                                    </th>

                                    <th>
                                        Items
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                                @if(count($payments) > 0)
                                    <?php $sn = 1; ?>
                                    @foreach($payments as $payment)

                                        <tr>
                                            <td>{{ $sn }}</td>
                                            <td>
                                                <?php $customer = \App\Models\Customer\Customer::find($payment->customer_id); ?>
                                                @if(!empty($customer))
                                                    {{ $customer->company_number ?? 'No customer' }}
                                                @endif
                                            </td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>{{ $payment->date_of_payment }}</td>
                                            <td>{{ $payment->currency }}</td>
                                            <td>{{ $payment->amount }}</td>
                                            <td>{{ $payment->invoice }}</td>
                                            <td>
                                                <?php $user = \App\Models\User::find($payment->user_id); ?>
                                                {{ $user->name }}
                                            <td><a class="btn btn-warning" href="{{ url('assessments/assessment-items') }}/{{ encrypt($payment->id) }}/{{ $flag }}"><i class="glyphicon glyphicon-eye-open"></i> View</a></td>
                                            <td>
                                                <a onclick="print_assessment('{{ encrypt($payment->id) }}','normal')" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Print normal bill</a>
                                                <br><br>
                                                <a onclick="print_assessment('{{ encrypt($payment->id) }}','nmb')" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> NMB transfer</a>
                                                <br><br>
                                                <a onclick="print_assessment('{{ encrypt($payment->id) }}','crdb')" class="btn btn-warning"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a>
                                                <br><br>
                                                <a onclick="print_assessment('{{ encrypt($payment->id) }}','nbc')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> NBC transfer</a>
                                            </td>
                                        </tr>
                                        <?php $sn++; ?>
                                    @endforeach



                                    <tr>
                                        <td colspan="13">
                                            <div class="pagination">

                                            </div>
                                        </td>
                                    </tr>

                                @else
                                    <tr>
                                        <td colspan="9"><b>No record found.</b></td>
                                    </tr>
                                @endif


                            </table>
                        </div>



                    </div>

                </div>
            </div>
        </div>






        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">

                </div>
            </div>
        </div>

        {!!Form::close()!!}

    </div>

    <script>
        function print_assessment(payment_id,type)
        {
            testprintout=window.open("{{ URL::route('print-bill-payment') }}?type="+type+"&payment_id="+payment_id+"","t","width=1000,height=700,menubar=yes,resizable=yes,scrollbars=yes,toolbar=yes,location=no").print();
        }



        function search_entity(){
            var entityName = document.getElementById('entityName').value;
            var entityNumber = document.getElementById('entityNumber').value;
            var flag = '<?php echo $flag; ?>'

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('assessment-details').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("search-assessment") }}?flag='+flag+'&entityName='+entityName+'&entityNumber='+entityNumber,true);
            myObject.send();
        }
    </script>



@stop
