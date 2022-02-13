{!! Form::select('item_id',[''=>'Select item']+$fee_items,array(),['class'=>'form-control','onchange'=>'get_item_contents()','id'=>'item_id']) !!}
