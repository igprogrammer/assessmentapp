<div class="col-md-12">
    <br>
    <div class="col-md-3">

        <div class="form-group {{$errors->has('questionTitle')?'has-error':''}}">
            <label>Year</label>
            <select name="year" id="year" class="form-control">
                <option value="0" selected>Select year</option>
                <?php
                $current_date = date('Y-m-d');
                $current_year = date('Y');
                $start_year = '1960';
                for ($i=1;$start_year+$i <= $current_year;$i++){ ?>
                <option value="<?php echo $start_year + $i; ?>"><?php echo $start_year + $i; ?></option>

                <?php }
                ?>
            </select>

        </div>
    </div>
</div>