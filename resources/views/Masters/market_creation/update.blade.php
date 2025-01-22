<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="javascript:insert_update_row('<?php echo $market_creation['id']; ?>',operator.value,description.value,pumpstatus.value,datetime.value,duration.value)">
                
                <!-- Operator Name -->
                <div class="form-group">
                    <label class="stl">Operator Name</label><label style="color: red">*</label>
                    <input type="text" id="operator" class="form-control" value="<?php echo $market_creation['operator']; ?>" placeholder="Enter Operator Name">
                    <div id="operator_validate_div" class="mark_label_red"></div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="stl">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Description" rows="4"><?php echo $market_creation['description']; ?></textarea>
                    <div id="description_validate_div" class="mark_label_red"></div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="stl">Pump Status</label><label style="color: red">*</label>
                    <select class="select2-search__field form-" id="pumpstatus" name="pumpstatus">
                        <option value="">Select Status</option>
                        <option value="running" <?php echo $market_creation['pumpstatus'] == 'running' ? 'selected' : ''; ?>>Running</option>
                        <option value="stopped" <?php echo $market_creation['pumpstatus'] == 'stopped' ? 'selected' : ''; ?>>Stopped</option>
                        <option value="ideal" <?php echo $market_creation['pumpstatus'] == 'ideal' ? 'selected' : ''; ?>>Ideal</option>
                    </select>
                    <div id="status_validate_div" class="mark_label_red"></div>
                </div>

                <!-- Date/Time -->
                <div class="form-group">
                    <label class="stl">Date/Time</label><label style="color: red">*</label>
                    <input type="datetime-local" id="datetime" class="form-control" value="<?php echo $market_creation['datetime']; ?>" placeholder="Enter Date and Time">
                    <div id="datetime_validate_div" class="mark_label_red"></div>
                </div>

                <!-- Duration -->
                <div class="form-group">
                    <label class="stl">Duration (hours)</label><label style="color: red">*</label>
                    <input type="number" id="duration" class="form-control" value="<?php echo $market_creation['duration']; ?>" placeholder="Enter Duration">
                    <div id="duration_validate_div" class="mark_label_red"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-icon icon-left btn-danger" data-dismiss="modal" aria-label="Close">
                            <span class="fas fa-times"></span> Cancel
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-icon icon-left btn-success" type="submit">
                            <span class="fas fa-check"></span> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#state_id').select2();
        $('#district_id1').select2();
        $('#pumpstatus').select2();
    });
</script>

<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: 350px;
        min-height: 42px;
        line-height: 42px;
        padding-left: 20px;
        padding-right: 20px;
    }
</style>
