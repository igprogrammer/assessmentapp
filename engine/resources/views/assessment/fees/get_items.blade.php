<select name="item_id" class="form-control" id="item_id" onchange="get_item_contents()">
    <option value="">Select item</option>
    <?php
    foreach ($fee_items as $fee_item){ ?>
    <option value="<?php echo $fee_item->id; ?>"><?php echo $fee_item->item_name; ?></option>
    <?php }
    ?>
</select>