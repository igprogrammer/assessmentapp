@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">


        <div class="panel panel-primary">

            <div class="panel-heading">Assessment list</div>
            <div class="panel-body">

                {!! Form::open(['url'=>'assessments/filter','method'=>'post','class'=>'form','files'=>true])!!}
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
                    @include('assessment.assessment.assessment_list')
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
