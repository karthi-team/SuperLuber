<form action="javascript:insert_update_row('<?php echo $working_days['id']; ?>',month_year.value,actual_month_days.value,working_days.value,description.value)">
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="stl">Month Year<b class="mark_label_red">*</b></label>
            <input type="month" id="month_year" class="form-control" value="<?php echo $working_days['month_year']; ?>">
            <div id="month_year_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="stl">Actual Month Days</label>
            <input type="text" id="actual_month_days" class="form-control" value="<?php echo $working_days['actual_month_days']; ?>" readonly>
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-6">
        <div class="form-group">
            <label class="stl">Working Days<b class="mark_label_red">*</b></label>
            <input type="text" id="working_days" class="form-control" value="<?php echo $working_days['working_days']; ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            <div id="working_days_validate_div" class="mark_label_red"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="stl">Description</label>
            <textarea id="description" class="form-control"><?php echo $working_days['description']; ?></textarea>
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
        <button class="btn btn-icon icon-left btn-success" type="update">
            <span class="fas fa-check"></span>Update
        </button>
    </div>
</div>
</form>
