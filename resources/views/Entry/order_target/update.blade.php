<form
    action="javascript:insert_update_row('<?php echo $order_target['id']; ?>', entry_date.value, target_number.value, sales_executive_id.value, description.value)">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="stl">Order Target<b class="mark_label_red">*</b></label>
                <input type="date" id="entry_date" class="form-control" value="<?php echo $order_target['entry_date']; ?>">
                <div id="entry_date_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="stl">Target Number<b class="mark_label_red">*</b></label>
                <input type="text" id="target_number" class="form-control" value="<?php echo $order_target['target_number']; ?>" readonly>
                <div id="entry_date_validate_div" class="mark_label_red"></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="stl">Sales Executive</label>
                <select id="sales_executive_id" class="form-control" disabled>
                    <option value="">Select</option>
                    @foreach ($sales_ref_creation as $sales_ref_creation_1)
                        <option value="{{$sales_ref_creation_1->id}}" {{$sales_ref_creation_1->id == $order_target->sales_executive_id ? ' selected' : '' }}>{{$sales_ref_creation_1->sales_ref_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label for="description" class="stl">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="4"><?php echo $order_target['description']; ?></textarea>
            </div>
        </div>
    </div>
    <div id="sublist_div" style="width:100%;"></div>
    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-icon icon-left btn-danger" data-dismiss="modal" aria-label="Close">
                <span class="fas fa-times"></span>Cancel
            </button>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-icon icon-left btn-success" type="submit">
                <span class="fas fa-check"></span>Update
            </button>
        </div>
    </div>
</form>

