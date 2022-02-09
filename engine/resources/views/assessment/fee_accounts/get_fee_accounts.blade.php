<select name="fee_account_id" class="form-control" id="fee_account_id" onchange="get_code()">
    <option value="">Select account</option>
    <?php
        foreach ($fee_accounts as $fee_account){ ?>
            <option value="<?php echo $fee_account->id; ?>"><?php echo $fee_account->account_name; ?></option>
        <?php }
    ?>
</select>