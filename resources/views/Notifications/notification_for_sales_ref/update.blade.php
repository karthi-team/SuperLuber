<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <form action="javascript:insert_update_row('<?php echo $notification_for_sales_ref['id']; ?>', group_id.value, item_id.value, description.value,datetime.value,before_login_or_after_login.value)">

                <div class="form-group row">
                    <div class="col-md-3">
                        <label for="datetime">Date and Time:</label><label style="color:red">*</label>
                        <input type="datetime-local" id="datetime" name="datetime" class="form-control"
                            value="<?php echo htmlspecialchars($notification_for_sales_ref['datetime'], ENT_QUOTES, 'UTF-8'); ?>" />
                        <div id="group_id_validate_div" style="color:red;"></div>
                    </div>

                    <div class="col-md-3">
                        <label for="group_id" class="stl">Group Name</label><label style="color:red">*</label>
                        <select class="form-control select2" id="group_id" onchange="find_item_id();" required>
                            <?php
                            $group_id = $notification_for_sales_ref['group_id'];
                            foreach ($group_names as $group_name) {
                                $group_nam = ucfirst($group_name['group_name']);
                                $selected = $group_id == $group_name['id'] ? 'selected' : '';
                            ?>
                            <option value="<?php echo $group_name['id']; ?>" <?php echo $selected; ?>><?php echo $group_nam; ?></option>
                            <?php } ?>
                        </select>
                        <div id="group_id_validate_div" style="color:red;"></div>
                    </div>

                    <div class="col-md-3">
                        <label for="item_id" class="stl">Item Name</label><label style="color:red">*</label>
                        <select class="form-control select2" id="item_id">
                            <?php
                            $item_id = $notification_for_sales_ref['item_id'];
                            foreach ($item_names as $item_name) {
                                $item_nam = ucfirst($item_name['item_name']);
                                $selected = $item_id == $item_name['id'] ? 'selected' : '';
                            ?>
                            <option value="<?php echo $item_name['id']; ?>" <?php echo $selected; ?>><?php echo $item_nam; ?></option>
                            <?php } ?>
                        </select>
                        <div id="item_id_validate_div" style="color:red;"></div>
                    </div>

                    <div class="col-md-3">
                        <label class="stl">Before Login or After Login</label><label style="color:red">*</label>
                        <select class="form-control select2" id="before_login_or_after_login">
                            <option value=''>--Select--</option>
                            <option value='before_login' <?php if ($notification_for_sales_ref['before_login_or_after_login'] == 'before_login') {
                                echo 'selected';
                            } ?>>Before Login</option>
                            <option value='after_login' <?php if ($notification_for_sales_ref['before_login_or_after_login'] == 'after_login') {
                                echo 'selected';
                            } ?>>After Login</option>
                        </select>
                        <div id="item_id_validate_div" style="color:red;"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label for="description" class="stl">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo $notification_for_sales_ref['description']; ?></textarea>
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
                            @php
                                $imageNames = json_decode($notification_for_sales_ref['upd_images'], true);

                                $defaultImage = asset('storage/default/default_image.png');
                            @endphp

                            @if (!empty($imageNames) && is_array($imageNames))
                                @foreach ($imageNames as $imageName)
                                    @php
                                        $imagePath = !empty($imageName)
                                            ? asset('storage/notification_sales_ref_img/' . $imageName)
                                            : $defaultImage;
                                    @endphp
                                    <img src="{{ $imagePath }}" width="75" height="75" alt="Image Preview">
                                @endforeach
                            @else
                                <!-- Display default image if no images are available -->
                                <img src="{{ $defaultImage }}" width="75" height="75" alt="Default Image">
                            @endif
                        </div>
                        {{-- Image Upload End --}}
                    </div>
                </div>
                <div class="row">
                    <div style="width:100%;">
                        <div class="customcheckbox_div" style="float:left;">
                            &nbsp;&nbsp;&nbsp; Checked All&nbsp;<input class="customcheckbox_div_checkbox" id="checkbox" type="checkbox"
                            onchange="perm1_function(this, -1); updateSelectAllValue(this);" <?php echo ($notification_for_sales_ref['checkbox'] ?? "") == "1" ? "checked" : ""; ?>>
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
                                $notificationStatus = explode(',', $notification_for_sales_ref['notification_status']);
                                @endphp
                                @foreach ($sales_rep_creation as $sales_rep_creation1)
                                    <tr>
                                        <td>{{ $i1++ }}</td>
                                        <td>
                                            @php
                                                $index = $i1 - 2;
                                                $isChecked = isset($notificationStatus[$index]) && $notificationStatus[$index] == "1";
                                            @endphp
                                            <input class="customcheckbox_div_checkbox" type="checkbox" onchange="perm_function(this, '{{ $index }}')" {{ $isChecked ? 'checked' : '' }} />
                                        </td>

                                        <td>
                                            {{ $sales_rep_creation1->sales_ref_name }}
                                            <input type="hidden" class="sales_ref_name" value="{{ $sales_rep_creation1->id }}" />
                                        </td>

                                        <td>
                                            {{ $sales_rep_creation1->notification_status }}
                                            <select class="form-control notification_status" id="notification_status_{{ $index }}" disabled>
                                                <option value="0" @php if (isset($notificationStatus[$index]) && $notificationStatus[$index] != '1') {
                                                    echo ' selected';
                                                } @endphp>Close</option>
                                                <option value="1" @php if (isset($notificationStatus[$index]) && $notificationStatus[$index] == '1') {
                                                    echo ' selected';
                                                } @endphp>Send</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        </div>
    </div>
</div>

<script>
function perm_function(checkbox, rowIndex) {
    var checked = checkbox.checked;
    var notificationStatusSelect = document.getElementById('notification_status_' + rowIndex);

    if (checked) {
        notificationStatusSelect.value = "1";
    } else {
        notificationStatusSelect.value = "0";
    }
}

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
</script>
<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: 350px;
        min-height: 42px;
        line-height: 42px;

        padding-right: 20px;
        display: inline-block;
        overflow: hidden;
        padding-left: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
        /* background-color: #fdfdff;
    border-color: #e4e6fc; */
    }
</style>
