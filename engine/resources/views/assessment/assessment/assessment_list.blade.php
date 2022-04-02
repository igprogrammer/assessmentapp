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
            Control number
        </th>
        <th>
            Assessed by
        </th>

        <th>
            Payment status
        </th>

        <th>
            Action
        </th>

    </tr>

    @if(count($payments) > 0)
        <?php $sn = 1; ?>
        @foreach($payments as $payment)

            <tr>
                <td>{{ $sn }}</td>
                <td>
                    <?php $customer = \App\Models\Customer\Customer::find($payment->customer_id); ?>
                    @if(!empty($customer))
                        {{ $customer->company_number ?? 'No customer' }}
                    @endif
                </td>
                <td>{{ $customer->customer_name }}</td>
                <td>{{ $payment->date_of_payment }}</td>
                <td>{{ $payment->currency }}</td>
                <td>{{ $payment->amount }}</td>
                <td>{{ $payment->invoice }}</td>
                <td>
                <?php $user = \App\Models\User::find($payment->user_id); ?>
                {{ $user->name }}
                <td>
                    @if($payment->isPaid == 1)
                        <a class="btn btn-success"><i class="glyphicon glyphicon-check"> Paid</i></a>
                    @else
                        <a class="btn btn-danger"><i class="glyphicon glyphicon-remove"> Not paid</i></a>
                    @endif
                </td>
                <td>
                    <a style="width: 100%" class="btn btn-warning" href="{{ url('assessments/assessment-items') }}/{{ encrypt($payment->id) }}/{{ $flag }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
                    <br><br>
                @if((int)$payment->invoice >= 991350000000)

                        <a onclick="print_assessment('{{ encrypt($payment->id) }}','normal')" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Print normal bill</a>
                        <br><br>
                        <a onclick="print_assessment('{{ encrypt($payment->id) }}','nmb')" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> NMB transfer</a>
                        <br><br>
                        <a onclick="print_assessment('{{ encrypt($payment->id) }}','crdb')" class="btn btn-warning"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a>
                        <br><br>
                        <a onclick="print_assessment('{{ encrypt($payment->id) }}','nbc')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> NBC transfer</a>

                    @else
                        <form id="request-control-number{{ $payment->id }}" class="request-control-number"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="paymentId" id="serviceCode" value="{{ $payment->id }}">
                            <input type="submit" name="submit" value="Request control number" class="btn btn-primary" id="requestControlNumber">


                        </form>
                    @endif
                </td>
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