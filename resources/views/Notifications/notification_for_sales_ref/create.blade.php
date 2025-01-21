<script>
    $(document).ready(function() {
        $('#country_id').select2();
    });
</script>
<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: 350px;
        min-height: 42px;
        line-height: 42px;
        padding-left: 20px;
        padding-right: 20px;
        /* background-color: #fcfcff; */
        /* border: 1px solid #e4e6fc !important; */
        font-size: 15px;
        font-weight: normal;
    }
</style>


<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <form action="javascript:insert_update_row('',group_id.value,item_id.value,description.value,datetime.value,before_login_or_after_login.value)"
                enctype="multipart/form-data">

                <div class="form-group row">
                    <div class="col-md-3">
                        <label for="datetime">Date and Time:</label><label style="color:red">*</label>
                        <input type="datetime-local" id="datetime" name="datetime" class="form-control"
                            value="<?php echo date('Y-m-d\TH:i'); ?>" />
                        <div id="datetime_validate_div" style="color:red;"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="stl">Group Name</label><label style="color:red">*</label>
                        <select class="form-control select2" id="group_id" onchange="find_item_id();">
                            <option value=''>--Select Group--</option>
                            <?php foreach($group_names as  $group_name){ ?>
                            <option value="<?php echo $group_name['id']; ?>"><?php echo $group_name['group_name']; ?></option>
                            <?php } ?>
                        </select>
                        <div id="group_id_validate_div" style="color:red;"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="stl">Item Name</label><label style="color:red">*</label>
                        <select class="form-control select2" id="item_id">
                            <option value=''>--Select Item Name--</option>
                        </select>
                        <div id="item_id_validate_div" style="color:red;"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="stl">Before Login or After Login</label><label style="color:red">*</label>
                        <select class="form-control select2" id="before_login_or_after_login">
                            <option value=''>--Select--</option>
                            <option value='before_login'>Before Login</option>
                            <option value='after_login'>After Login</option>
                        </select>
                        <div id="item_id_validate_div" style="color:red;"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label for="description" class="stl">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Description" rows="4"></textarea>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="image" class="stl">Image upload</label>
                            <input type="file" class="form-control" id="image_name" name="image_name[]" multiple
                                onchange="previewImage(event)">
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="form-group" id="image_previews">
                            <img id="image_preview" src="{{ asset('storage/default/default_image.png') }}"
                                width="75" height="75" alt="Image Preview">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div style="width:100%;">
                        <div class="customcheckbox_div" style="float:left;">
                            &nbsp;&nbsp;&nbsp;Checked All&nbsp;<input class="customcheckbox_div_checkbox" id="checkbox" type="checkbox"
                            onchange="perm1_function(this, -1); updateSelectAllValue(this.value);">

                        </div>
                    </div>
                    <br><br>
                    <div class="col-md-12">
                        <table class="table table-sm table-hover" id="rights_tableExport" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Check</th>
                                    <th>Sales Executive Name</th>
                                    <th>Notification Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i1 = 1;
                                @endphp
                                @foreach ($sales_rep_creation as $sales_rep_creation1)
                                <tr>
                                    <td> {{ $i1++ }} </td>
                                    <td>
                                        <input class="customcheckbox_div_checkbox" type="checkbox" onchange="perm_function(this, '{{ $i1 - 2 }}')"/>
                                    </td>
                                    <td>
                                        {{ $sales_rep_creation1->sales_ref_name }}
                                            <input type="hidden" class="sales_ref_name" value="{{ $sales_rep_creation1->id }}" />
                                    </td>
                                    <td>
                                        <select class="form-control notification_status" id="notification_status_<?php echo $i1 - 2; ?>" disabled>
                                            <option value="0">Close</option>
                                            <option value="1">Send</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
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
        </div>
    </div>
</div>

<script>
function updateSelectAllValue(checkbox) {
    if (checkbox.checked) {
        checkbox.value = 1;
    } else {
        checkbox.value = 0;
    }
}

function perm1_function(checkbox, rowIndex) {
    var checked = checkbox.checked;
    document.querySelectorAll('.customcheckbox_div_checkbox').forEach(function(checkbox, index) {
        if (index === rowIndex) return;
        checkbox.checked = checked;
        var notificationStatusSelect = document.getElementById('notification_status_' + (index - 1));
        if (notificationStatusSelect) {
            notificationStatusSelect.value = checked ? "1" : "0";
        }
    });
}

function perm_function(checkbox, rowIndex) {
    var checked = checkbox.checked;
    var row = checkbox.closest('tr');
    var checkboxesInRow = row.querySelectorAll('.customcheckbox_div_checkbox');
    checkboxesInRow.forEach(function(checkbox) {
        checkbox.checked = checked;
    });
    var notificationStatusSelect = document.getElementById('notification_status_' + rowIndex);
    notificationStatusSelect.value = checked ? "1" : "0";
}

function validateForm() {

    var subExpenseType = document.getElementById('state_name');
    var errorMessage = document.getElementById('error_message');
    var errorMessage_1 = document.getElementById('error_message_1');

    var expense_type = $('#country_id').val();
    if (expense_type == 0) {

        errorMessage.style.display = 'block';
        return false;
    } else {

        errorMessage.style.display = 'none';
    }

    if (subExpenseType.value.trim() === '') {

        subExpenseType.style.borderColor = 'red';
        errorMessage_1.style.display = 'block';
        return false;
    } else {
        subExpenseType.style.borderColor = '';
        errorMessage_1.style.display = 'none';
    }
    return true;
}
</script>
