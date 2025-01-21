function list_div()
{

  $('#list_div').html("");
  var sendInfo = {  "action": "retrieve" ,"user_rights_edit_1":user_rights_edit_1,"user_rights_delete_1":user_rights_delete_1};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function (data) {
      $('#list_div').html(data);
      $(function () {
        $('#tableExport').DataTable({
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: [0, 1, 2,3,4 ]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    //text: '<i class="far fa-file-pdf"></i>',
                    exportOptions: {
                        columns: [0, 1, 2,3,4 ]
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: [0, 1, 2,3,4 ]
                    }
                }
            ]
        });
    });
    }
  });
}
$(function () {
  find_state();
  list_div();

});
function open_model(title,id)
{

    var countryId=$("#country_id").val();
  $('#bd-example-modal-lg1 #model_main_content').html("...");
  var sendInfo={};
  if(id==""){sendInfo={"action":"create_form"};}
  else{sendInfo={"country_id":countryId,"action":"update_form","id":id};}
  $.ajax({
    type: "GET",
    url:$("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#bd-example-modal-lg1').modal('show');
      $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
      $('#bd-example-modal-lg1 #model_main_content').html(data);
      setTimeout(function (){
        $("#country_id").select2();
        $("#state_id").select2();
      }, 500);

    },
    error: function() {
      alert('error handing here');
    }
  });
}




function insert_update_row(id,machine_id,machine_type,machine_name,model_number,purchase_date,description)
{

  if (id == "") {
    if ((machine_id) && (machine_name) && (machine_type)) {
      var sendInfo = {  "action": "insert", "machine_id": machine_id, "machine_name": machine_name, "machine_type": machine_type, "model_number": model_number,"purchase_date": purchase_date,'description': description };
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(response){
            $('#bd-example-modal-lg1').modal('hide');
            if (response.error) {
                swal(response.error, { icon: 'error' });
            } else {
                var message = response.message || ' inserted successfully';
                swal(message, { icon: 'success' });
                list_div();
            }
          },

        error: function () {
          alert('error handing here');
        }
      });
    } else {
      validate_inputs(machine_id,machine_type,machine_name,model_number,purchase_date,description);
    }
  }
  else {
    if ((machine_id) && (machine_name) && (machine_type)) {
      var sendInfo = {  "action": "update", "id": id, "machine_id": machine_id, "machine_name": machine_name, "machine_type": machine_type, "model_number": model_number,"purchase_date": purchase_date,'description': description };
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(response){
            $('#bd-example-modal-lg1').modal('hide');
            if (response.error) {
                swal(response.error, { icon: 'error' });
            } else {
                var message = response.message || ' Update successfully';
                swal(message, { icon: 'success' });
                list_div();
            }
          },
        error: function () {
          alert('error handing here');
        }
      });
    } else {
      validate_inputs(machine_id,machine_type,machine_name,model_number,purchase_date,description);
    }
  }
}
function validate_inputs(machine_id, machine_type, machine_name, model_number, purchase_date, description) {

    // Validate Machine ID
  // Validate Machine ID (Only numbers allowed)
if (machine_id == '') {
    $("#machine_id_validate_div").html("Enter Machine ID");
    $("#machine_id").focus();
    return false;
  } else if (!/^\d+$/.test(machine_id)) {  // Regular expression to check for numbers only
    $("#machine_id_validate_div").html("Machine ID must be a number");
    $("#machine_id").focus();
    return false;
  } else {
    $("#machine_id_validate_div").html("");
  }


    // // Validate Machine Name
    // if (machine_name == '') {
    //   $("#machine_name_validate_div").html("Enter Machine Name");
    //   $("#machine_name").focus();
    //   return false;
    // } else {
    //   $("#machine_name_validate_div").html("");
    // }

    // Validate Machine Type
    if (machine_type == '') {
      $("#machine_type_validate_div").html("Enter Machine Type");
      $("#machine_type").focus();
      return false;
    } else {
      $("#machine_type_validate_div").html("");
    }

    // Validate Model Number
    if (model_number == '') {
      $("#model_number_validate_div").html("Enter Model Number");
      $("#model_number").focus();
      return false;
    } else {
      $("#model_number_validate_div").html("");
    }

    // Validate Purchase Date
    if (purchase_date == '') {
      $("#purchase_date_validate_div").html("Select Purchase Date");
      $("#purchase_date").focus();
      return false;
    } else {
      $("#purchase_date_validate_div").html("");
    }

    // Validate Description
    if (description == '') {
      $("#description").addClass('is-invalid');
      $("#description_validate_div").html("Enter Description");
      return false;
    } else {
      $("#description").removeClass('is-invalid');
      $("#description_validate_div").html("");
    }

    // All fields are valid
    return true;
  }

function delete_row(id)
{
  swal({
    title: 'Are you sure?',
    text: 'To delete User Creation',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {

      var sendInfo={"action":"delete","id":id};
      $.ajax({
        type: "GET",
        url:$("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(data){
          swal('Deleted Successfully', {icon: 'success',});
          list_div();
        },
        error: function() {
          alert('error handing here');
        }
      });
    }
  });
}


function find_state()
{

  var countryId = $("#country_id").val();
  //alert(countryId)

  var sendInfo={"action":"getStates","country_id":countryId};
    if (countryId) {
      $.ajax({
        type: "GET",
        url:$("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#state_id').empty();
          $('#state_id').append('<option value="" readonly>-----select state-----</option>');
          $.each(data, function(key, value) {
            $('#state_id').append('<option value="' + key + '">' + value + '</option>');
          });
        }
      });
    } else {
      $('#state_id').empty();
    }

}
