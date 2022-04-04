@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">
        <a class="btn btn-primary" href="{{ url('fees/add') }}"><i class="glyphicon glyphicon-plus-sign"></i> Add fee<br></a>
        <br><br>

        {!! Form::open(['url'=>'','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="panel panel-primary">
            <!-- Default panel contents -->

            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">

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
                                Division
                            </th>
                            <th>
                                Fee account
                            </th>
                            <th>
                                Fee code
                            </th>
                            <th>
                                Fee name
                            </th>

                            <th>
                                Account code
                            </th>

                            <th>
                                Type
                            </th>

                            <th>
                                GFS code
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Has form
                            </th>
                            <th>
                                Define fee?
                            </th>

                            <th>
                                Edit
                            </th>

                        </tr>

                        <?php

                        if (count($fees) > 0){
                        $sn = 1;
                        foreach ($fees as $fee){ ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td>
                                <?php
                                $fee_account = \App\Models\Assessment\FeeAccount::find($fee->fee_account_id);
                                if (!empty($fee_account)){
                                    $division = \App\Models\Assessment\Division::find($fee_account->division_id);
                                    echo $division->division_name;
                                }else{
                                    echo "No division";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $fee_account = \App\Models\Assessment\FeeAccount::find($fee->fee_account_id);
                                if (!empty($fee_account)){
                                    $fee_account = \App\Models\Assessment\FeeAccount::find($fee_account->id);
                                    echo $fee_account->account_name;
                                }else{
                                    echo "No fee account";
                                }
                                ?>
                            </td>

                            <td><?php echo $fee->fee_code; ?></td>
                            <td><?php echo $fee->fee_name; ?></td>
                            <td><?php echo $fee->account_code; ?></td>
                            <td><?php echo $fee->type; ?></td>
                            <td><?php echo $fee->gfs_code; ?></td>
                            <td><?php echo $fee->amount; ?></td>
                            <td>
                                @if($fee->has_form == 'yes')
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td>
                                @if($fee->defineFeeAmount == 1)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td><a class="btn btn-warning" href="{{ url('fees') }}/edit-fee/{{ encrypt($fee->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a></td>
                        </tr>
                        <?php $sn++; } ?>
                        <?php }else{ ?>
                        <tr>
                            <td colspan="9"><b>No record found.</b></td>
                        </tr>
                        <?php } ?>


                        <tr>
                            <td colspan="13">
                                <div class="pagination">

                                </div>
                            </td>
                        </tr>




                    </table>
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
