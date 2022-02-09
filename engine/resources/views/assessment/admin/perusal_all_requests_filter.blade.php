@extends('...layouts.admin')
@section('content')
    <div style="font-size:12px;border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="fbbluebox">

        @if(Session::has('success-message'))

            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! Session::get('success-message') !!}
            </div>
        @endif

        @if(Session::has('error-message'))
            <p>

                @if($errors->has())

                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            <p>{!! Session::get('error-message') !!}
            <ul>
                {!! $errors->first('fileName', '<li>:message</li>') !!}
                {!! $errors->first('fileNo', '<li>:message</li>') !!}
                {!! $errors->first('requestReason', '<li>:message</li>') !!}
            </ul>
            </p>
    </div>
    </p>

    @endif
    @endif

    {!! Form::open(array('url'=>'perusal-filter-files','method'=>'post')) !!}
    <table class="table" style="width: 100%">

        <tr>
            <td>
                <b>
                    Search by
                </b>
            </td>
            <td></td>
            <td><b>Date from</b></td>
            <td>
                <input type="text" name="dateFrom" class="form-control datepicker" required="required">
            </td>

            <td><b>Date to</b></td>
            <td>
                <input type="text" name="dateTo" class="form-control datepicker" required="required">
            </td>
            <td>
                <b>File status</b>
            </td>
            <td>
                <select name="fileStatus" class="form-control">
                    <option value="all">All</option>
                    <option value="available">Available</option>
                    <option value="notavailable">Not available</option>
                </select>
            </td>
            <td>
                <input type="submit" name="submit" value="Filter" class="btn btn-primary">
            </td>
        </tr>
    </table>
    {!! Form::close() !!}


    <table class="table">
        <tr>
            <th>
                SNO
            </th>
            <th>
                File Name
            </th>
            <th>
                File Number
            </th>
            <th>
                Requested by
            </th>
            <th>
                Date
            </th>
            <th>
                File status
            </th>
            <th>
                Payment status
            </th>
            <th>
                Registry status
            </th>
            <th>
                Perusal officer status
            </th>
        </tr>

        <?php

        if( count($perusal_files) > 0 ){


        $i=1;
        foreach($perusal_files as $request){?>
        <tr>
            <td>
                {!! $i !!}
            </td>
            <td>
                {!! $request->companyName !!}
            </td>
            <td>
                {!! $request->fileNo !!}
            </td>
            <td>
                {!! $request->employee->Employee_Name !!}
            </td>
            <td>
                {!! date('jS F, Y  H:i:s', strtotime($request->created_at)) !!}
            </td>

            <td>
                <?php
                if($request->fileStatus == 'available'){?>
                <a class="btn btn-success">Available</a>
                <?php }else{?>
                <a class="btn btn-warning">Not available</a>
                <?php }

                ?>
            </td>

            <td>
                <?php
                if($request->fileStatus == 'available'){
                if($request->paymentStatus == 'unpaid'){ ?>
                <a class="btn btn-danger">Not paid</a>
                <?php }else{ ?>
                <a class="btn btn-success">Paid</a>
                <?php }
                }else{
                if($request->paymentStatus == 'unpaid'){?>
                <button class="btn btn-warning">No payment</button>
                <?php }else{?>
                <button class="btn btn-warning">No payment</button>
                <?php }
                }

                ?>

                <script>
                    function update_payment<?php echo $request->id.$request->fileNo;?>(){

                        var conf = confirm('Are you sure the customer has paid?');

                        if(conf){
                            var perusal_file_id = document.getElementById('<?php echo $request->id.$request->fileNo;?>').value;

                            if(window.XMLHttpRequest) {
                                myObject = new XMLHttpRequest();
                            }else if(window.ActiveXObject){
                                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                                myObject.overrideMimeType('text/xml');
                            }

                            myObject.onreadystatechange = function (){
                                data = myObject.responseText;
                                if (myObject.readyState == 4) {
                                    location.reload(true);
                                    //alert(data)
                                }
                            }; //specify name of function that will handle server response........
                            myObject.open('GET','{{ URL::route("updatePayment") }}?perusal_file_id='+perusal_file_id,true);
                            myObject.send();
                            return true;

                        }else{
                            return false;
                        }

                    }
                </script>


            </td>
            <td>
                <?php
                if($request->registryStatus == 'pending'){?>
                <a class="btn btn-danger">Pending registry</a>
                <?php }else{?>
                <a class="btn btn-success">Approved</a>
                <?php }
                ?>
            </td>

            <td>
                <?php
                if($request->perusalOfficerAcknowledgeReceive == 'notreceived'){?>
                <a class="btn btn-danger">Not perused</a>
                <?php }else{?>
                <a class="btn btn-success">Perused</a>
                <?php }
                ?>
            </td>


        </tr>
        <?php $i++; }
        }else{ ?>
        <tr>
            <td colspan="8">
                <b style="font-size: 14px;color: deeppink;">
                    No new requests found.
                </b>
            </td>
        </tr>

        <?php }

        ?>

        <tr>
            <td colspan="9">
                <hr/>
            </td>
        </tr>


    </table>
    </div>




@endsection