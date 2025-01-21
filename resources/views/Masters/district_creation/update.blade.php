<form action="javascript:insert_update_row('<?php echo $district_creation['id']; ?>',machine_id.value, machine_name.value, machine_type.value, model_number.value, purchase_date.value, description.value)">
    <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="machine_id" class="stl">Machine ID</label><b class="mark_label_red">*</b>
            <input type="text" id="machine_id" class="form-control" value="<?php echo $district_creation['machine_id']; ?>" required>
            <div id="machine_id_validate_div" style="color:red;"></div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="machine_name" class="stl">Machine Name</label><b class="mark_label_red">*</b>
            <input type="text" id="machine_name" class="form-control" value="<?php echo $district_creation['machine_name']; ?>" required>
            <div id="machine_name_validate_div" style="color:red;"></div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="machine_type" class="stl">Machine Type</label><b class="mark_label_red">*</b>
            <input type="text" id="machine_type" class="form-control" value="<?php echo $district_creation['machine_type']; ?>" required>
            <div id="machine_type_validate_div" style="color:red;"></div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="model_number" class="stl">Model Number</label><b class="mark_label_red">*</b>
            <input type="text" id="model_number" class="form-control" value="<?php echo $district_creation['model_number']; ?>" required>
            <div id="model_number_validate_div" style="color:red;"></div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="purchase_date" class="stl">Purchase Date</label><b class="mark_label_red">*</b>
            <input type="date" id="purchase_date" class="form-control" value="<?php echo $district_creation['purchase_date']; ?>" required>
            <div id="purchase_date_validate_div" style="color:red;"></div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="description" class="stl">Description</label>
            <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="4"><?php echo $district_creation['description']; ?></textarea>
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
