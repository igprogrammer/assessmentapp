@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        {!! Form::open(['url'=>'','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            <div class="col-md-4">
                <a class="btn btn-primary" href="{{ url('assessments/list/'.$flag) }}"><i class="glyphicon glyphicon-log-out"></i> Back<br></a>
            </div>
            <br><br><br>
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Assessment items</div>

                <div class="panel-body">

                    <div class="col-md-12">

                        @if(Session::has('success-message'))

                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {!! Session::get('success-message') !!}
                            </div>
                        @endif

                        <div  id="bl_details">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>
                                        SNO
                                    </th>
                                    <th>
                                        Item name
                                    </th>
                                    <th>
                                        Fee amount
                                    </th>
                                    <th>
                                        Date of assessment
                                    </th>
                                    <th>
                                        Month
                                    </th>
                                    <th>
                                        Year
                                    </th>
                                    <th>
                                        Form year
                                    </th>

                                </tr>

                                @if(count($payments) > 0)
                                    <?php $sn = 1;$total = 0; ?>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td><?php echo $sn; ?></td>
                                            <td>
                                                <?php
                                                $fee_item = \App\Models\Assessment\FeeItem::find($payment->fee_item_id);
                                                if (!empty($fee_item)){
                                                    echo $fee_item->item_name;
                                                }else{
                                                    echo "Unknown customer";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $payment->fee_amount; ?></td>
                                            <td><?php echo $payment->date_of_payment; ?></td>
                                            <td><?php echo $payment->month; ?></td>
                                            <td><?php echo $payment->year; ?></td>
                                            <td><?php echo $payment->fyear; ?></td>
                                        </tr>
                                        <?php $sn++; $total = $total + $payment->fee_amount; ?>
                                    @endforeach
                                    <tr>
                                        <td><b>Total</b></td><td colspan="6"><b>{{ number_format($total) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <div class="pagination">
                                                {!! str_replace('/?', '?', $payments->render() )!!}
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

            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Assessment attachment(s)</div>

                <div class="panel-body">

                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>File name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($attachments) > 0){
                                $sn = 1;
                                foreach ($attachments as $attachment){ ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td><?php echo $attachment->file_name; ?></td>
                                    <td>
                                        <a href="{{ url('assessments/get-attachment') }}/{{ $attachment->id }}" target="_blank" class="btn btn-success">
                                            View attachment
                                        </a>
                                    </td>
                                </tr>

                                <?php $sn++; }

                                }else{ ?>
                                    <tr>
                                        <td colspan="3">
                                            No attachment found.
                                        </td>
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>

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
        function print_assessment(payment_id)
        {
            testprintout=window.open("{{ URL::route('print-assessment') }}?payment_id="+payment_id+"","t","width=1000,height=700,menubar=yes,resizable=yes,scrollbars=yes,toolbar=yes,location=no").print();
        }
    </script>



@stop
