<link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">
<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;">
    <thead>
    <tr class="stl">
        <th>SNo</th>
          <th>Supplier Name</th>
          <th>Contact Person</th>
          <th>Contact Number</th>
          <th>Email ID</th>
          <th>Address</th>
          <th>GST Number</th>
          <th>Creation Time</th>
          <th>Next Review Date</th>
          <th>Status</th>

        <?php if(($user_rights_edit_1==1) || ($user_rights_delete_1==1)){ ?>
            <th class="text-center">Action</th>
            <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $i1=1;
    foreach($shops_type as $shops_type1)
    { ?>
    <tr>
        <td class="stl"><?php echo $i1;$i1++; ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['supplier_name']); ?></td>
        {{-- <td class="stl"><?php echo  ucfirst($shops_type1['supplier_id']); ?></td> --}}
        <td class="stl"><?php echo  ucfirst($shops_type1['contact_person']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['contact_number']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['email_id']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['address']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['gst_number']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['creation_time']); ?></td>
        <td class="stl"><?php echo  ucfirst($shops_type1['next_review_date']); ?></td>

        <td align="center" class="stl">
            <div class="pretty p-default p-curve p-toggle">
                <input type="checkbox" <?php if ($shops_type1['status']=="1"){ echo 'checked'; }else{ } ?> onclick="change_fun('<?php echo $shops_type1['id']; ?>','<?php echo $shops_type1['status1']; ?>')">
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
        <a class="btn btn-icon icon-left btn-info" data-toggle="tooltip" title="Edit" onclick="open_model('Update Shops Type','<?php echo $shops_type1['id']; ?>')"><i class="far fa-edit"></i>Edit</a>
        <?php } ?>
        <?php if($user_rights_delete_1==1){ ?>
        <a class="btn btn-icon icon-left btn-danger" data-toggle="tooltip" title="Delete" onclick="delete_row('<?php echo $shops_type1['id']; ?>')"><i class="fas fa-trash"></i>Delete</a>
        <?php } ?>
    </td>
    <?php } ?>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<script>
    function change_fun(id,status1){
        // alert();
      var sendInfo={"action":"statusinfo","id":id,"status1":status1};
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(data){

        },
        error: function() {
          alert('error handing here');
        }
      });
    }
</script>
