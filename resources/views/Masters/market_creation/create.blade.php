<div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
<form action="javascript:insert_update_row('',operator.value,description.value,pumpstatus.value,datetime.value,duration.value)">

        <div class="form-group">
            {{-- oprtator --}}
            <div class="form-group">
                    <label class="stl">Operator Name</label><label style="color: red">*</label>
                <input type="text" id="operator" class="form-control"  placeholder="Enter Operator Name">
                <div id="operator_validate_div" class="mark_label_red"></div>
            </div>
            {{-- description --}}
             <div class="form-group">
                   <label for="description" class="stl">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Description" rows="4"></textarea>
                 <div id="description_validate_div" class="mark_label_red"></div>
            </div>
            {{-- status --}}
            <div class="form-group">
           <label class="stl">Status</label><label style="color: red">*</label>
            <select class="select2-search__field form-control" id="pumpstatus" name="pumpstatus" onchange="getDistricts()">
                <option value="">select</option>
                <option value="running">running</option>
                <option value="stoped">stoped</option>
                <option value="ideal">ideal</option>
                {{-- @foreach ($state_name as $state)
                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                @endforeach --}}
            </select>
            <div id="pumpstatus_validate_div" class="mark_label_red"></div>
            </div>
              
        </div>

       {{-- date and time --}}
         <div class="form-group">
                    <label class="stl">Date/Time</label><label style="color: red">*</label>
                <input type="datetime-local" id="datetime" class="form-control"  placeholder="Enter Date and Time">
                <div id="datetime_validate_div" class="mark_label_red"></div>
            </div>
              {{-- duration --}}
                <div class="form-group">
                    <label class="stl">Duration(hours)</label><label style="color: red">*</label>
                <input type="number" id="duration" class="form-control"  placeholder="Enter duration">
                <div id="duration_validate_div" class="mark_label_red"></div>
            </div>
            <div class="row">
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
$(document).ready(function() {
    $('#state_id').select2();
    $('#district_id1').select2();
});
</script>

<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered{
            width: 350px;
            min-height: 42px;
            line-height: 42px;
            padding-left: 20px;
            padding-right: 20px;
            }
                </style>


