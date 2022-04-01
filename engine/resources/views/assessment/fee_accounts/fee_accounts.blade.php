@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">
        <a class="btn btn-primary" href="{{ url('fees/add-fee-account') }}"><i class="glyphicon glyphicon-plus-sign"></i> Add fee account<br></a>
        <br><br>
        {!! Form::open(['url'=>'','method'=>'post','class'=>'form','files'=>true])!!}

        <div class="panel panel-primary">
            <!-- Default panel contents -->

            <div class="panel-heading">{{ $title }}</div>
            <div class="panel-body">

                <div class="col-md-12">
                    <div class="col-md-4">
                    </div>


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
                                    Account code
                                </th>
                                <th>
                                    Account name
                                </th>

                                <th>
                                    Group number
                                </th>

                                <th>
                                    Division
                                </th>

                                <th>
                                    Edit
                                </th>

                            </tr>

                            @if(count($fee_accounts) > 0)
                                <?php $sn = 1;?>
                                @foreach($fee_accounts as $fee_account)
                                    <tr>
                                        <td>{{ $sn }}</td>
                                        <td>{{ $fee_account->account_code }}</td>
                                        <td>{{ $fee_account->account_name }}</td>
                                        <td>{{ $fee_account->group_number }}</td>
                                        <td>
                                            <?php $division = \App\Models\Assessment\Division::find($fee_account->division_id); ?>
                                            {{ $division->division_name ?? 'No division name' }}
                                        </td>
                                        <td><a class="btn btn-warning" href="{{ url('fees/edit-fee-account') }}/{{ encrypt($fee_account->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a></td>
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

        {!!Form::close()!!}

    </div>



@stop
