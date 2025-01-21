<form action="javascript:insert_update_row('',
 document.getElementById('alert_title').value,
 document.getElementById('alert_type').value,
 document.getElementById('description').value,
 document.getElementById('status').value,
)">
    <div class="row">
      <!-- Alert Title -->
      <div class="col-md-6">
        <div class="form-group">
          <label class="stl">Alert Title</label><b class="mark_label_red">*</b>
          <input type="text" id="alert_title" class="form-control" placeholder="Enter Alert Title">
          <div id="alert_title_validate_div" style="color:red;"></div>
        </div>
      </div>

      <!-- Description -->
      <div class="col-md-6">
        <div class="form-group">
          <label for="description" class="stl">Description</label><b class="mark_label_red">*</b>
          <textarea class="form-control" id="description" name="description" placeholder="Enter Description" rows="4"></textarea>
          <div id="description_validate_div" style="color:red;"></div>
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
      <!-- Cancel Button -->
      <div class="col-md-6">
        <button class="btn btn-icon icon-left btn-danger" data-dismiss="modal" aria-label="Close">
          <span class="fas fa-times"></span> Cancel
        </button>
      </div>

      <!-- Submit Button -->
      <div class="col-md-6 text-right">
        <button class="btn btn-icon icon-left btn-success" type="submit">
          <span class="fas fa-check"></span> Submit
        </button>
      </div>
    </div>
  </form>
