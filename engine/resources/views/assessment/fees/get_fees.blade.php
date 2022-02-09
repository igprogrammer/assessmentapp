<select name="fee_id" class="form-control" id="fee_id" onchange="get_items()">
    <option value="">Select fee</option>
    <?php
    foreach ($fees as $fee){ ?>
    <option value="<?php echo $fee->id; ?>"><?php echo $fee->fee_name; ?></option>
    <?php }
    ?>
</select>