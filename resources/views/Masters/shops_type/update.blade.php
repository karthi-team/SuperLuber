<form action="javascript:insert_update_row('<?php echo $shops_type['id']; ?>',
    supplier_name.value,
    supplier_id.value,
    contact_person.value,
    contact_number.value,
    email_id.value,
    address.value,
    gst_number.value,
    creation_time.value,
    next_review_date.value,
    status1.value,
    description.value
    )">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Supplier Name<b class="mark_label_red">*</b></label>
                <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="<?php echo $shops_type['supplier_name']; ?>">
                <div id="supplier_name_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Supplier ID<b class="mark_label_red">*</b></label>
                <input type="text" id="supplier_id" name="supplier_id" class="form-control" value="<?php echo $shops_type['supplier_id']; ?>">
                <div id="supplier_id_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Contact Person <b class="mark_label_red">*</b></label>
                <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?php echo $shops_type['contact_person']; ?>">
                <div id="contact_person_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Contact Number <b class="mark_label_red">*</b></label>
                <input type="text" id="contact_number" name="contact_number" class="form-control" value="<?php echo $shops_type['contact_number']; ?>">
                <div id="contact_number_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Email ID <b class="mark_label_red">*</b></label>
                <input type="email" id="email_id" name="email_id" class="form-control" value="<?php echo $shops_type['email_id']; ?>">
                <div id="email_id_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Address <b class="mark_label_red">*</b></label>
                <input type="text" id="address" name="address" class="form-control" value="<?php echo $shops_type['address']; ?>">
                <div id="address_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">GST Number  <b class="mark_label_red">*</b></label>
                <input type="text" id="gst_number" name="gst_number" class="form-control" value="<?php echo $shops_type['gst_number']; ?>">
                <div id="gst_number_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Creation Time  <b class="mark_label_red">*</b></label>
                <input type="datetime-local" id="creation_time" name="creation_time" class="form-control" value="<?php echo $shops_type['creation_time']; ?>">
                <div id="creation_time_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Next Review Date   <b class="mark_label_red">*</b></label>
                <input type="date" id="next_review_date" name="next_review_date" class="form-control" value="<?php echo $shops_type['next_review_date']; ?>">
                <div id="next_review_date_validate_div" class="mark_label_red"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Status</label>
                <select id="status1" name="status1" class="form-control">
                    <option value="1" <?php echo ($shops_type['status1'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo ($shops_type['status1'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="stl">Description</label>
                <textarea id="description" name="description" class="form-control"><?php echo $shops_type['description']; ?></textarea>
            </div>
        </div>
    </div>
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
