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
            Items
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
                   {{ $payment->name }}
                </td>
                <td><a class="btn btn-warning" href="{{ url('assessments/assessment-items') }}/{{ encrypt($payment->id) }}/{{ $flag }}"><i class="glyphicon glyphicon-eye-open"></i> View</a></td>
                <td>
                    <a onclick="print_assessment('{{ encrypt($payment->id) }}','normal')" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Print normal bill</a>
                    <br><br>
                    <a onclick="print_assessment('{{ encrypt($payment->id) }}','nmb')" class="btn btn-info"><i class="glyphicon glyphicon-print"></i> NMB transfer</a>
                    <br><br>
                    <a onclick="print_assessment('{{ encrypt($payment->id) }}','crdb')" class="btn btn-warning"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a>
                    <br><br>
                    <a onclick="print_assessment('{{ encrypt($payment->id) }}','nbc')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> NBC transfer</a>
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
