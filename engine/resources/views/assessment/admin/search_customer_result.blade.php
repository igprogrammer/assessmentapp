<table class="table">
    <tr>
        <th>
            SNO
        </th>
        <th>
            Customer name
        </th>
        <th>
            Customer number
        </th>
        <th>
            Date
        </th>
        <th>
            Type
        </th>
        <th>
            Edit
        </th>
    </tr>

    @if(count($customers) > 0)
        <?php $sn = 1; ?>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $sn }}</td>
                <td>{{ $customer->customer_name }}</td>
                <td>{{ $customer->company_number }}</td>
                <td>
                    @if(!empty($customer->regDate))
                        {{ date('d-m-Y', strtotime($customer->regDate)) }}</td>
                @else
                    NIL
                @endif
                <td>{{ $customer->entityType }}</td>
                <td>
                    <a href='edit/{{ encrypt($customer->id) }}' class="btn btn-success"><i class='glyphicon glyphicon-edit'>View</i></a>
                </td>
            </tr>
            <?php $sn++; ?>
        @endforeach

    @else
        <tr>
            <td colspan="6">
                <b style="font-size: 14px;color: deeppink;">
                    No customer found.
                </b>
            </td>
        </tr>
    @endif


    <tr>
        <td colspan="9">
            <div class="pagination">
                {!! str_replace('/?', '?', $customers->render() )!!}
            </div>
        </td>
    </tr>

</table>
