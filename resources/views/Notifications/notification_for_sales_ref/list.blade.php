<link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">
<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;">
    <thead>
    <tr align="center" class="stl">
        <th>Sno</th>
         <th>Date & Time</th>
        <th>Group Name</th>
        <th>Item Name</th>
        <th class="text-center">Description</th>
           <th class="text-center">Before/After Login</th>
        <th>Status</th>
        <?php if(($user_rights_edit_1==1) || ($user_rights_delete_1==1)){ ?>
            <th class="text-center">Action</th>
            <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $i1=1;
    foreach($notification_for_sales_ref as $notification_for_sales_ref1)
    { ?>
    <tr align="center">
        <td class="stl"><?php echo $i1;$i1++; ?></td>
        <td class="stl"><?php echo $notification_for_sales_ref1['datetime']; ?></td>
        <td class="stl"><?php echo  ucfirst($notification_for_sales_ref1['group_name']); ?>
    </td>
        <td class="stl"><?php echo  ucfirst($notification_for_sales_ref1['item_name']); ?></td>
        <td class="stl"><?php echo  ucfirst($notification_for_sales_ref1['description']); ?></td>
        
           <td class="stl"><?php $formattedText = ucfirst(str_replace('_', ' ',$notification_for_sales_ref1['before_login_or_after_login'])); echo  $formattedText; ?></td>
        <td align="center" class="stl">
            <div class="pretty p-default p-curve p-toggle">
                <input type="checkbox"  <?php if ($notification_for_sales_ref1['status'] == "1") { echo 'checked'; } ?> onclick="change_fun('<?php echo $notification_for_sales_ref1['id']; ?>', this)">
                <div class="state p-success p-on">
                  <label style="color: green">Active</label>
                </div>
                <div class="state p-danger p-off">
                  <label style="color: red">In Active </label>
                </div>
              </div>
        </td>
        <?php if(($user_rights_edit_1==1) || ($user_rights_delete_1==1)){ ?>
            <td align="center" class="stl">
                <?php if($user_rights_edit_1==1){ ?>
        <a class="btn btn-icon icon-left btn-info hover-gradient" data-toggle="tooltip" style="cursor: pointer" title="Edit" onclick="open_model('Update Notification Creation','<?php echo $notification_for_sales_ref1['id']; ?>')"><i class="far fa-edit"></i>Edit</a>
        <?php } ?>
        <?php if($user_rights_delete_1==1){ ?>
        <a class="btn btn-icon icon-left btn-danger" data-toggle="tooltip" style="cursor: pointer" title="Delete" onclick="delete_row('<?php echo $notification_for_sales_ref1['id']; ?>')"><i class="fas fa-trash"></i>Delete</a>
        <?php } ?>
    </td>
    <?php } ?>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<script>
    function change_fun(id, checkbox) {
    var status = checkbox.checked ? "1" : "0";  // Update status based on checkbox state

    var _token1 = $("#_token1").val();
    var sendInfo = {"action": "statusinfo", "_token": _token1, "id": id, "status": status};

    $.ajax({
        type: "POST",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(data) {
            // Handle success
        },
        error: function() {
            alert('error handling here');
        }
    });
}
</script>
