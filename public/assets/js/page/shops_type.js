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


// function insert_update_row(id,shops_type,status1,description)
// {

//   if(id=="")
//   {
//     console.log("shops_type");
//     if((shops_type)){
//     var sendInfo={"action":"insert","shops_type":shops_type,"status1":status1,"description":description};
//     $.ajax({
//       type: "GET",
//       url: $("#CUR_ACTION").val(),
//       data: sendInfo,
//       success: function(data){
//         $('#bd-example-modal-lg1').modal('hide');
//         swal('Inserted Successfully', {icon: 'success',});
//         list_div();
//       },
//       error: function() {
//         alert('error handing here');
//       }
//     });
//   }
//   else{validate_inputs(shops_type);}
//   }
//   else
//   {
//     if((shops_type)){
//     var sendInfo={"action":"update","id":id,"shops_type":shops_type,"status1":status1,"description":description};
//     $.ajax({
//       type: "GET",
//       url: $("#CUR_ACTION").val(),
//       data: sendInfo,
//       success: function(data){
//         $('#bd-example-modal-lg1').modal('hide');
//         swal('Updated Successfully', {icon: 'success',});
//         list_div();
//       },
//       error: function() {
//         alert('error handing here');
//       }
//     });
//   }
//   else{validate_inputs(shops_type);}
//   }
// }

function insert_update_row(id, supplier_name, supplier_id, contact_person, contact_number, email_id, address, gst_number, creation_time, next_review_date, status1,description) {

    if (id == "") {
      // Insert case
      if (supplier_name && supplier_id && contact_person && contact_number && email_id && address && gst_number && creation_time && next_review_date,description) {
        var sendInfo = {
          "action": "insert",
          "supplier_name": supplier_name,
          "supplier_id": supplier_id,
          "contact_person": contact_person,
          "contact_number": contact_number,
          "email_id": email_id,
          "address": address,
          "gst_number": gst_number,
          "creation_time": creation_time,
          "next_review_date": next_review_date,
          "status": status1,
          "description":description
        };
        console.log("sendInfo",sendInfo)
        $.ajax({
          type: "GET",
          url: $("#CUR_ACTION").val(),
          data: sendInfo,
          success: function(data) {
            $('#bd-example-modal-lg1').modal('hide');
            swal('Inserted Successfully', { icon: 'success' });
            list_div();
          },
          error: function() {
            alert('Error handling here');
          }
        });
      } else {
        validate_inputs(supplier_name, supplier_id, contact_person, contact_number, email_id, address, gst_number, creation_time, next_review_date, status1,description); // add validation for all inputs
      }
    } else {
      // Update case
      if (supplier_name && supplier_id && contact_person && contact_number && email_id && address ) {
        console.group("update")
        var sendInfo = {
          "action": "update",
          "id": id,
          "supplier_name": supplier_name,
          "supplier_id": supplier_id,
          "contact_person": contact_person,
          "contact_number": contact_number,
          "email_id": email_id,
          "address": address,
          "gst_number": gst_number,
          "creation_time": creation_time,
          "next_review_date": next_review_date,
          "status": status1,
          "description":description
        };
        console.log("sendInfo update",sendInfo)
        $.ajax({
          type: "GET",
          url: $("#CUR_ACTION").val(),
          data: sendInfo,
          success: function(data) {
            $('#bd-example-modal-lg1').modal('hide');
            swal('Updated Successfully', { icon: 'success' });
            list_div();
          },
          error: function() {
            alert('Error handling here');
          }
        });
      } else {
        validate_inputs(supplier_name); // add validation for all inputs
      }
    }
  }


  function validate_inputs(supplier_name, supplier_id, contact_person, contact_number, email_id, address, gst_number, creation_time, next_review_date, status1, description) {
    let isValid = true;

    // Supplier Name Validation
    if (supplier_name.trim() === '') {
        $("#supplier_name").addClass('is-invalid');
        $("#supplier_name_validate_div").html("Supplier Name is required.");
        isValid = false;
    } else {
        $("#supplier_name").removeClass('is-invalid');
        $("#supplier_name_validate_div").html("");
    }

    // Supplier ID Validation
    if (supplier_id.trim() === '') {
        $("#supplier_id").addClass('is-invalid');
        $("#supplier_id_validate_div").html("Supplier ID is required.");
        isValid = false;
    } else {
        $("#supplier_id").removeClass('is-invalid');
        $("#supplier_id_validate_div").html("");
    }

    // Contact Person Validation
    if (contact_person.trim() === '') {
        $("#contact_person").addClass('is-invalid');
        $("#contact_person_validate_div").html("Contact Person is required.");
        isValid = false;
    } else {
        $("#contact_person").removeClass('is-invalid');
        $("#contact_person_validate_div").html("");
    }

    // Contact Number Validation
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(contact_number)) {
        $("#contact_number").addClass('is-invalid');
        $("#contact_number_validate_div").html("Enter a valid 10-digit contact number.");
        isValid = false;
    } else {
        $("#contact_number").removeClass('is-invalid');
        $("#contact_number_validate_div").html("");
    }

    // Email ID Validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email_id)) {
        $("#email_id").addClass('is-invalid');
        $("#email_id_validate_div").html("Enter a valid email address.");
        isValid = false;
    } else {
        $("#email_id").removeClass('is-invalid');
        $("#email_id_validate_div").html("");
    }

    // Address Validation
    if (address.trim() === '') {
        $("#address").addClass('is-invalid');
        $("#address_validate_div").html("Address is required.");
        isValid = false;
    } else {
        $("#address").removeClass('is-invalid');
        $("#address_validate_div").html("");
    }

    // GST Number Validation
    if (gst_number.trim() === '') {
        $("#gst_number").addClass('is-invalid');
        $("#gst_number_validate_div").html("GST Number is required.");
        isValid = false;
    } else {
        $("#gst_number").removeClass('is-invalid');
        $("#gst_number_validate_div").html("");
    }

    // Creation Time Validation
    if (creation_time.trim() === '') {
        $("#creation_time").addClass('is-invalid');
        $("#creation_time_validate_div").html("Creation Time is required.");
        isValid = false;
    } else {
        $("#creation_time").removeClass('is-invalid');
        $("#creation_time_validate_div").html("");
    }

    // Next Review Date Validation
    if (next_review_date.trim() === '') {
        $("#next_review_date").addClass('is-invalid');
        $("#next_review_date_validate_div").html("Next Review Date is required.");
        isValid = false;
    } else {
        $("#next_review_date").removeClass('is-invalid');
        $("#next_review_date_validate_div").html("");
    }

    // Description Validation (Optional)
    if (description.trim() === '') {
        $("#description").addClass('is-invalid');
        $("#description_validate_div").html("Description is optional but should not be empty.");
        isValid = false;
    } else {
        $("#description").removeClass('is-invalid');
        $("#description_validate_div").html("");
    }

    // Status Validation (Optional)
    if (status1.trim() === '') {
        $("#status1").addClass('is-invalid');
        $("#status1_validate_div").html("Please select a status.");
        isValid = false;
    } else {
        $("#status1").removeClass('is-invalid');
        $("#status1_validate_div").html("");
    }

    return isValid;
}


function delete_row(id)
{
  swal({
    title: 'Are you sure?',
    text: 'To delete Shops Type',
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
