<form action="javascript:insert_update_row(
    '',
    document.getElementById('supplier_name').value,
    document.getElementById('supplier_id').value,
    document.getElementById('contact_person').value,
    document.getElementById('contact_number').value,
    document.getElementById('email_id').value,
    document.getElementById('address').value,
    document.getElementById('gst_number').value,
    document.getElementById('creation_time').value,
    document.getElementById('next_review_date').value,
    document.getElementById('status1').value,
    document.getElementById('description').value
)">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Supplier Name<b class="mark_label_red">*</b></label>
                <input type="text" id="supplier_name" name="supplier_name" class="form-control">
                <div id="supplier_name_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Supplier ID<b class="mark_label_red">*</b></label>
                <input type="text" id="supplier_id" name="supplier_id" class="form-control">
                <div id="supplier_id_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Contact Person <b class="mark_label_red">*</b></label>
                <input type="text" id="contact_person" name="contact_person" class="form-control">
                <div id="contact_person_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Contact Number <b class="mark_label_red">*</b></label>
                <input type="text" id="contact_number" name="contact_number" class="form-control">
                <div id="contact_number_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Email ID <b class="mark_label_red">*</b></label>
                <input type="email" id="email_id" name="email_id" class="form-control">
                <div id="email_id_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Address <b class="mark_label_red">*</b></label>
                <input type="text" id="address" name="address" class="form-control">
                <div id="address_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">GST Number  <b class="mark_label_red">*</b></label>
                <input type="text" id="gst_number" name="gst_number" class="form-control">
                <div id="gst_number_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Creation Time  <b class="mark_label_red">*</b></label>
                <input type="datetime-local" id="creation_time" name="creation_time" class="form-control">
                <div id="creation_time_validate_div" class="mark_label_red"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Next Review Date   <b class="mark_label_red">*</b></label>
                <input type="date" id="next_review_date" name="next_review_date" class="form-control">
                <div id="next_review_date_validate_div" class="mark_label_red"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="stl">Status</label>
                <select id="status1" name="status1" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="stl">Description</label>
                <textarea id="description" name="description" class="form-control"></textarea>
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
                <span class="fas fa-check"></span>Submit
            </button>
        </div>
    </div>
</form>


{{--

Supplier Name (Text field) - To specify the name of the supplier.
Supplier ID (Text field) - A unique identifier for the supplier.
Contact Person (Text field) - Name of the primary contact person.
Contact Number (Mobile field) - Mobile number of the supplier or contact person.
Email ID (Email field) - Email address of the supplier.
Address (Textarea) - Full address of the supplier.
GST Number (Text field) - Supplier’s GST registration number.
Creation Time (DateTime Picker) - Time when the supplier is added to the system.
Next Review Date (Date Picker) - Time for the next review or update.
Status (Radio buttons) - Active or Inactive. --}}
