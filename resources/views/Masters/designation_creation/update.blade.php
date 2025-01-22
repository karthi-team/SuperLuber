{{-- <form action="javascript:insert_update_row('<?php echo $designation_creation['id']; ?>',alert_title.value,description.value,alert_type.value,status.value)">
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="stl">Alert Title</label><b class="mark_label_red">*</b>
            <input type="text" id="alert_title" class="form-control" value="<?php echo $designation_creation['alert_title']; ?>" >
            <div id="designation_name_validate_div" style="color:red;"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="description" class="stl">Description</label>
          <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="4"><?php echo $designation_creation['description']; ?></textarea>
      </div>

        </div>
    </div>
</div>
<div class="row">
    <!-- Alert Type -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="alert_type" class="stl">Alert Type</label><b class="mark_label_red">*</b>
        <select id="alert_type" class="form-control">
          <option value="" disabled selected>Select Alert Type</option>
          <option value="info">Information</option>
          <option value="warning">Warning</option>
          <option value="error">Error</option>
          <option value="success">Success</option>
        </select>
        <div id="alert_type_validate_div" style="color:red;"></div>
      </div>
    </div>

    <!-- Alert Level -->
    <div class="col-md-6">
      <div class="form-group">
          <label class="stl">Status</label>
          <select id="status" name="status" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
          </select>
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
</form> --}}


<form action="javascript:insert_update_row('<?php echo $designation_creation['id']; ?>', alert_title.value, description.value, alert_type.value, status.value)">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="stl">Alert Title</label><b class="mark_label_red">*</b>
          <input type="text" id="alert_title" class="form-control" value="<?php echo $designation_creation['alert_title']; ?>" />
          <div id="designation_name_validate_div" style="color:red;"></div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="description" class="stl">Description</label>
          <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="4"><?php echo $designation_creation['description']; ?></textarea>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Alert Type -->
      <div class="col-md-6">
        <div class="form-group">
          <label for="alert_type" class="stl">Alert Type</label><b class="mark_label_red">*</b>
          <select id="alert_type" class="form-control">
            <option value="" disabled <?php echo !$designation_creation['alert_type'] ? 'selected' : ''; ?>>Select Alert Type</option>
            <option value="info" <?php echo $designation_creation['alert_type'] == 'info' ? 'selected' : ''; ?>>Information</option>
            <option value="warning" <?php echo $designation_creation['alert_type'] == 'warning' ? 'selected' : ''; ?>>Warning</option>
            <option value="error" <?php echo $designation_creation['alert_type'] == 'error' ? 'selected' : ''; ?>>Error</option>
            <option value="success" <?php echo $designation_creation['alert_type'] == 'success' ? 'selected' : ''; ?>>Success</option>
          </select>
          <div id="alert_type_validate_div" style="color:red;"></div>
        </div>
      </div>

      <!-- Status -->
      <div class="col-md-6">
        <div class="form-group">
          <label class="stl">Status</label>
          <select id="status" name="status" class="form-control">
            <option value="1" <?php echo $designation_creation['status'] == '1' ? 'selected' : ''; ?>>Active</option>
            <option value="0" <?php echo $designation_creation['status'] == '0' ? 'selected' : ''; ?>>Inactive</option>
          </select>
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
