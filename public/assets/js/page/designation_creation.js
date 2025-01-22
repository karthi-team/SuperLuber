function list_div()
{

  $('#list_div').html("");
  var sendInfo={"action":"retrieve","user_rights_edit_1":user_rights_edit_1,"user_rights_delete_1":user_rights_delete_1};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#list_div').html(data);
      $(function () {
        $('#tableExport').DataTable({
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: [0, 1, 2 ]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    //text: '<i class="far fa-file-pdf"></i>',
                    exportOptions: {
                        columns: [0, 1, 2 ]
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: [0, 1, 2 ]
                    }
                }
            ]
        });
    });
    },
    error: function() {
      alert('error handing here');
    }
  });
}
$(function () {
  list_div();
});
function open_model(title,id)
{

  $('#bd-example-modal-lg1 #model_main_content').html("...");
  var sendInfo={};
  if(id==""){sendInfo={"action":"create_form"};}
  else{sendInfo={"action":"update_form","id":id};}
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#bd-example-modal-lg1').modal('show');
      $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
      $('#bd-example-modal-lg1 #model_main_content').html(data);
    },
    error: function() {
      alert('error handing here');
    }
  });
}
// function insert_update_row(id,alert_title,description,alert_type,status)
// {
//     // alert();

//   if(id=="")
//   {
//     if(alert_title){
//     var sendInfo={"action":"insert","alert_title":alert_title,"description":description,"alert_type":alert_type,"status":status};
//     $.ajax({
//       type: "GET",
//       url: $("#CUR_ACTION").val(),
//       data: sendInfo,
//       success: function(data){
//         if(data ==''){
//         $('#bd-example-modal-lg1').modal('hide');
//         swal('Inserted Successfully', {icon: 'success',});
//         list_div();
//         }else{
//           swal('Already Exit', { icon: 'error', });
//         }
//       },
//       error: function() {
//         alert('error handing here');
//       }
//     });
//   }else{
//     validate_inputs(alert_title,description,alert_type,status);
//   }
//   }
//   else
//   {
//     if(alert_title){
//     var sendInfo={"action":"update","id":id,"alert_title":alert_title,"description":description,"alert_type":alert_type,"status":status};
//     console.log("sendInfo")
//     console.log("sendInfo",sendInfo)
//     $.ajax({
//       type: "GET",
//       url: $("#CUR_ACTION").val(),
//       data: sendInfo,
//       success: function(data){
//         if(data == ''){
//         $('#bd-example-modal-lg1').modal('hide');
//         swal('Updated Successfully', {icon: 'success',});
//         list_div();
//         }else{
//           swal('Already Exit', { icon: 'error', });
//         }
//       },
//       error: function() {
//         alert('error handing here');
//       }
//     });
//   }
//   else{
//     validate_inputs(alert_title,description,alert_type,status);
//   }
//   }
// }

// function validate_inputs(alert_title,description,alert_type,status){
//   if (designation_name == '') { $("#designation_name").addClass('is-invalid'); $("#designation_name_validate_div").html("Enter Designation Name"); return false; } else { $("#designation_name").removeClass('is-invalid'); $("#designation_name_validate_div").html(""); }
// }


function insert_update_row(id, alert_title, description, alert_type, status) {
    // Validate inputs before proceeding
    const inputsValid = validate_inputs(alert_title, description, alert_type, status);

    if (!inputsValid) {
        return; // Stop execution if validation fails
    }

    let sendInfo;
    let actionType;

    if (id === "") {
        // Insert action
        actionType = "insert";
        sendInfo = { action: actionType, alert_title, description, alert_type, status };
    } else {
        // Update action
        actionType = "update";
        sendInfo = { action: actionType, id, alert_title, description, alert_type, status };
    }

    // Perform AJAX request
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            if (data === '') {
                $('#bd-example-modal-lg1').modal('hide');
                const message = id === "" ? 'Inserted Successfully' : 'Updated Successfully';
                swal(message, { icon: 'success' });
                list_div();
            } else {
                swal('Already Exists', { icon: 'error' });
            }
        },
        error: function () {
            alert('Error occurred while processing your request');
        }
    });
}



function validate_inputs(alert_title, description, alert_type, status) {
    let isValid = true;

    // Validate Alert Title
    if (alert_title.trim() === '') {
        $("#alert_title").addClass('is-invalid');
        $("#alert_title_validate_div").html("Enter Alert Title");
        isValid = false;
    } else {
        $("#alert_title").removeClass('is-invalid');
        $("#alert_title_validate_div").html("");
    }

    // Validate Description
    if (description.trim() === '') {
        $("#description").addClass('is-invalid');
        $("#description_validate_div1").html("Enter Description");
        isValid = false;
    } else {
        $("#description").removeClass('is-invalid');
        $("#description_validate_div1").html("");
    }

    // Validate Alert Type
    if (alert_type === '') {
        $("#alert_type").addClass('is-invalid');
        $("#alert_type_validate_div").html("Select Alert Type");
        isValid = false;
    } else {
        $("#alert_type").removeClass('is-invalid');
        $("#alert_type_validate_div").html("");
    }

    // Validate Status
    if (status === '') {
        $("#status").addClass('is-invalid');
        $("#status_validate_div").html("Select Status"); // Optional: Add validation message here
        isValid = false;
    } else {
        $("#status").removeClass('is-invalid');
        $("#status_validate_div").html("");
    }

    return isValid; // Return true if all inputs are valid, else false
}

function delete_row(id)
{
  swal({
    title: 'Are you sure?',
    text: 'To delete User Type',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {

      var sendInfo={"action":"delete","id":id};
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
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
