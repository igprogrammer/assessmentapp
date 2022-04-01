<table class="table table-bordered table-striped">
    <tr>
        <th colspan="3">Assessment attachments</th>
    </tr>
    <tr>
        <td>SN</td>
        <td>File name</td>
        <td>Action</td>
    </tr>

    @if(count($attachments) > 0)
        <?php $sn = 1; ?>
        @foreach($attachments as $attachment)
            <tr>
                <td><?php echo $sn; ?></td>
                <td><?php echo $attachment->file_name; ?></td>
                <td>
                    <a href="{{ url('assessments/get-attachment') }}/{{ $attachment->id }}" target="_blank" class="btn btn-success">
                        View attachment
                    </a>
                </td>
            </tr>
            <?php $sn++; ?>
        @endforeach
    @else
        <tr>
            <td colspan="3">
                No attachment found.
            </td>
        </tr>
    @endif
</table>
