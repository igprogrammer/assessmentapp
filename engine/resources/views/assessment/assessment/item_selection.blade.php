<div class="col-md-12">
    <div class="panel panel-info">
        <!-- Default panel contents -->
        <div class="panel-heading">Item selection</div>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Division') !!}
                        {!! Form::select('division_id',[''=>'Select division']+$divisions,array(),['class'=>'form-control','onchange'=>'get_fee_accounts()','id'=>'division_id']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Account') !!}
                        {!! Form::select('fee_account_id',[''=>'Select account'],array(),['class'=>'form-control','id'=>'fee_account_id','onchange'=>'get_fees()']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Fee') !!}
                        {!! Form::select('fee_id',[''=>'Select fee'],array(),['class'=>'form-control','id'=>'fee_id','onchange'=>'get_items()']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                        {!! Form::label('title','Item/Form name') !!}
                        {!! Form::select('item_id',[''=>'Select item'],array(),['class'=>'form-control','id'=>'item_id','onchange'=>'display_fields()']) !!}
                    </div>
                </div>
            </div>

            <div  id="filing_year" style="display: none">
                <div class="col-md-12">
                    <br>
                    <div class="col-md-3">

                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            <label>Year</label>
                            <?php
                            $years = array();
                            $current_date = date('Y-m-d');
                            $current_year = date('Y');
                            $start_year = '1960';

                            for ($i=1;$start_year+$i <= $current_year;$i++){
                                $years[$start_year + $i] = $start_year + $i;
                            }

                            ?>

                            {!! Form::select('year',[''=>"Select year"]+$years,2022,['class'=>'form-control','id'=>'year','selected'=>'selected']) !!}


                        </div>
                    </div>
                </div>
            </div>
            <div  id="calculate_fee_button" style="display: none;">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Majority ownership') !!}
                            {!! Form::select('calculationType',[''=>'Select type','1'=>'Local','2'=>'Foreign'],array(),['class'=>'form-control','id'=>'calculationType']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Licence type') !!}
                            {!! Form::select('licenceType',[''=>'Select type','1'=>'Principal','2'=>'Branch'],array(),['class'=>'form-control','id'=>'licenceType']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Phone number') !!}
                            {!! Form::text('phone_number',null,['class'=>'form-control','id'=>'phone_number','placeholder'=>'Phone number']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Expire days') !!}
                            {!! Form::text('expire_days',14,['class'=>'form-control','id'=>'expire_days','placeholder'=>'Expire days']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Number of files') !!}
                            {!! Form::number('number_of_files',1,['class'=>'form-control','id'=>'number_of_files','required','min'=>'1']) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {!! Form::label('title','Item fee') !!}
                            {!! Form::text('item_fee',null,['class'=>'form-control','id'=>'item_fee','placeholder'=>'Please specify item amount here']) !!}
                        </div>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
                            {{--<input type="submit" name="submit" value="Calculate fee" class="btn btn-primary" id="calculateFee">--}}
                            <br><a class="btn btn-success" onclick="calculate_fee()">Calculate fee</a>
                        </div>
                    </div>
                </div>
            </div>


            <div  id="item_contents" style="display: none;">
                <div class="col-md-12"><b style="text-decoration: underline;">Item details</b>
                    <hr>

                    <table class="table">
                        <thead>
                        <tr>
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
                                Penalty amount
                            </th>
                            <th>
                                Charge days
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Item name" readonly>
                            </td>
                            <td>
                                <input type="text" name="currency" id="currency" class="form-control" placeholder="Currency" readonly>
                            </td>
                            <td>
                                <input type="text" name="item_amount" id="item_amount" class="form-control" placeholder="Item amount" readonly>
                            </td>
                            <td>
                                <input type="text" name="penalty_amount" id="penalty_amount" class="form-control" placeholder="Penalty amount" readonly>
                            </td>
                            <td>
                                <input type="text" name="charge_days" id="charge_days" class="form-control" placeholder="Charge days" readonly>
                            </td>
                            <td>
                                <a class="btn btn-success" onclick="add_fee()"> Add fee</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>


        </div>

    </div>
</div>
