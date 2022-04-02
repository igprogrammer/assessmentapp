@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">


        <div class="panel panel-primary">

            <div class="panel-heading">Assessment list</div>
            <div class="panel-body">

                {!! Form::open(['url'=>'assessments/temp-filter','method'=>'post','class'=>'form','files'=>true])!!}
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
                {!!Form::close()!!}


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
                                Assessed by
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                        @if(count($payments) > 0)
                            <?php $sn = 1; ?>
                            @foreach($payments as $payment)

                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td>{{ $payment->company_number }}</td>
                                    <td>{{ $payment->company_name }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($payment->updated_at)) }}</td>
                                    <td>{{ $payment->currency }}</td>
                                    <td>

                                        <?php
                                            $amount = \App\Models\Payment\TempItem::where(['temp_payment_id'=>$payment->id])->sum(\Illuminate\Support\Facades\DB::raw('CAST(fee_amount as INT)'));
                                        ?>

                                        {{ number_format($amount) }}

                                    </td>
                                    <td>
                                    <?php $user = \App\Models\User::find($payment->user_id); ?>
                                    {{ $user->name }}
                                    <td>

                                        <?php
                                            $payInfo = \App\Models\Payment\Payment::where(['temp_payment_id'=>$payment->id])->first();
                                        ?>

                                        @if(!empty($payInfo))

                                            @if((int)$payInfo->invoice >= 991350000000)
                                                <a class="btn btn-success"><i class="glyphicon glyphicon-ok-circle"></i> Approved</a>
                                            @else
                                               <a class="btn btn-info"><i class="glyphicon glyphicon-remove-circle"></i> Pending</a>
                                            @endif

                                        @else
                                                <a class="btn btn-info"><i class="glyphicon glyphicon-remove-circle"></i> Pending</a>
                                        @endif

                                    </td>
                                </tr>
                                <?php $sn++; ?>
                            @endforeach



                            <tr>
                                <td colspan="7">
                                    <div class="pagination">

                                    </div>
                                </td>
                            </tr>

                        @else
                            <tr>
                                <td colspan="7"><b>No record found.</b></td>
                            </tr>
                        @endif


                    </table>

                </div>


            </div>
        </div>

    </div>


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
