@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        {!! Form::open(['url'=>'','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            <div class="panel panel-primary">
                <!-- Default panel contents -->

                <div class="panel-body">

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <a class="btn btn-primary" href="{{ url('fees/add-fee-item') }}"><i class="glyphicon glyphicon-plus-sign"></i> Add fee item<br></a>
                        </div>
                        <br><br><br>

                        @if(Session::has('success-message'))

                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {!! Session::get('success-message') !!}
                            </div>
                        @endif

                        <div  id="bl_details">
                            <table class="table">
                                <tr>
                                    <th>
                                        SNO
                                    </th>
                                    <th>
                                        Fee type
                                    </th>
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
                                        Penalty
                                    </th>

                                    <th>
                                        Days
                                    </th>

                                    <th>
                                        Copy charge
                                    </th>

                                    <th>
                                        Stamp duty amount
                                    </th>

                                    <th>
                                        Edit
                                    </th>

                                </tr>
                                <?php $sn = 1; ?>
                                @if(count($fee_items) > 0)
                                    @foreach($fee_items as $fee_item)
                                        <tr>
                                            <td><?php echo $sn; ?></td>
                                            <td>
                                                <?php $fee = \App\Models\Assessment\Fee::find($fee_item->fee_id); ?>

                                                @if(!empty($fee))
                                                {{ $fee->fee_name }}
                                                @else
                                                    Invalid fee
                                                @endif
                                            </td>
                                            <td>{{ $fee_item->item_name }}</td>
                                            <td>{{ $fee_item->currency }}</td>
                                            <td>{{ $fee_item->item_amount }}</td>
                                            <td>{{ $fee_item->penalty_amount }}</td>
                                            <td>{{ $fee_item->days }}</td>
                                            <td>{{ $fee_item->copy_charge }}</td>
                                            <td>{{ $fee_item->stamp_duty_amount }}</td>
                                            <td><a class="btn btn-warning" href="{{ url('fees/fee-item') }}/<?php echo \Illuminate\Support\Facades\Crypt::encrypt($fee_item->id); ?>/<?php echo $flag = 'edit'; ?>"><i class="glyphicon glyphicon-edit"></i> Edit</a></td>
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



@stop
