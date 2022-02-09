@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        {!! Form::open(['url'=>'divisions/add','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="col-md-12">
            <div class="panel panel-primary">
                <!-- Default panel contents -->

                <div class="panel-body">

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <a class="btn btn-primary" href="{{ url('divisions/add') }}"><i class="glyphicon glyphicon-plus-sign"></i> Add division<br></a>
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
                                        Division code
                                    </th>
                                    <th>
                                        Division name
                                    </th>

                                    <th>
                                       Description
                                    </th>
                                    <th>
                                        Edit
                                    </th>

                                </tr>

                                @if(count($divisions) > 0)
                                    <?php $sn = 1; ?>
                                    @foreach($divisions as $division)
                                        <tr>
                                            <td>{{ $sn }}</td>
                                            <td>{{ $division->division_code }}</td>
                                            <td>{{ $division->division_name }}</td>
                                            <td>{{ $division->description }}</td>
                                            <td><a class="btn btn-warning" href="{{ url('divisions/edit-division') }}/{{ encrypt($division->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a></td>
                                        </tr>
                                    <?php $sn++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6"><b>No record found.</b></td>
                                    </tr>
                                @endif

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
