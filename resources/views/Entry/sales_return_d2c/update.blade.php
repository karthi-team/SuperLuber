<form action="javascript:insert_update_row('<?php echo $sales_return_d2c_main['id']; ?>',order_date.value,dealer_creation_id.value,dealer_address.value,mop.value,order_no.value,status1.value,credit_note_no.value,description.value,sales_exec.value)">
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>Return Date </label>
            <input type="date" id="order_date" class="form-control" value="<?php echo ($sales_return_d2c_main['order_date']!="")?date('Y-m-d',strtotime($sales_return_d2c_main['order_date'])):date("Y-m-d"); ?>">
            <div id="order_date_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Return Number </label>
            <input type="text" id="order_no" class="form-control" placeholder="Order Number" readonly value="<?php echo $sales_return_d2c_main['order_no']; ?>">
            <div id="order_no_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Sales Executive<b class="mark_label_red">*</b></label>
            <select class="form-control select2_comp" id="sales_exec" style="width:100%;">
                <option value="">Select</option>
                <?php foreach($sales_name as $sales_name1){ ?>
                <option value="<?php echo $sales_name1['id']; ?>" <?php if ($sales_name1['id'] == $sales_return_d2c_main['sales_exec']) {
                    echo 'selected';
                } ?>><?php echo $sales_name1['sales_ref_name']; ?></option>
                <?php } ?>
            </select>
            <div id="sales_exec_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Dealer Name </label>
            <select class="form-control select2_comp" id="dealer_creation_id" style="width:100%;">
                <option value="">Select</option>
                <?php foreach($dealer_creation as $dealer_creation1){ ?>
                <option value="<?php echo $dealer_creation1['id']; ?>" <?php if($dealer_creation1['id']==$sales_return_d2c_main['dealer_creation_id']){echo " selected";} ?>><?php echo $dealer_creation1['dealer_name']; ?></option>
                <?php } ?>
            </select>
            <div id="dealer_creation_id_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="dealer_address">Dealer Address</label>
            <textarea class="form-control" id="dealer_address" name="dealer_address"  rows="4"><?php echo $sales_return_d2c_main['dealer_address']; ?></textarea>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Mode Of Payment</label>
            <select id="mop" class="form-control">
            <option value="">Select</option>
                <option value="1" <?php if($sales_return_d2c_main['mop']=="1"){echo " selected";} ?>>Credit</option>
                <option value="0" <?php if($sales_return_d2c_main['mop']!="1"){echo " selected";} ?>>Debit</option>
            </select>
            <div id="mop_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Status </label>
            <select id="status1" class="form-control">
                <option value="1" <?php if($sales_return_d2c_main['status']=="1"){echo " selected";} ?>>Active</option>
                <option value="0" <?php if($sales_return_d2c_main['status']!="1"){echo " selected";} ?>>In Active</option>
            </select>
            <div id="status1_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Credit Note No<b class="mark_label_red">*</b></label>
            <input type="text" id="credit_note_no" class="form-control" placeholder="Credit Note No" value="<?php echo $sales_return_d2c_main['credit_note_no']; ?>">
            <div id="credit_note_no_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-2">
    <div class="form-group">
        <label for="description">Description</label>
       <textarea class="form-control" id="description" name="description" rows="4"><?php echo $sales_return_d2c_main['description']; ?></textarea>
   </div>
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
<script>
$(function () {
    $(".select2_comp").select2();
    load_model_sublist('<?php echo $sales_return_d2c_main['id']; ?>','');
});
</script>
