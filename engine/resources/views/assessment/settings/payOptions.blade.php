@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

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
                                Option
                            </th>

                            <th>
                                Edit
                            </th>

                        </tr>

                        <?php

                        if (count($options) > 0){
                        $sn = 1;
                        foreach ($options as $option){ ?>
                        <tr>
                            <td>{{ $sn }}</td>
                            <td>{{ $option->BillPayOptName }}</td>
                            <td><a class="btn btn-warning" href="{{ url('settings/pay-option') }}/{{ encrypt($option->id) }}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></td>
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



    </div>



@stop
