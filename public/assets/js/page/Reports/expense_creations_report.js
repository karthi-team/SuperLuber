function list_div(from_date_1, to_date_1, expense_no_1, sales_rep_creation_id_1, market_manager_id_1, status_1, mode_of_payment_1) {
    $('#list_div').html("");

    // Get the _token1 value
    var _token1 = $("#_token1").val();

    var sendInfo = {
      "action": "retrieve",
      "from_date_1": from_date_1,
      "to_date_1": to_date_1,
      "expense_no_1": expense_no_1,
      "sales_rep_creation_id_1": sales_rep_creation_id_1,
      "market_manager_id_1": market_manager_id_1,
      "status_1": status_1,
      "mode_of_payment_1": mode_of_payment_1,
      "user_rights_edit_1": user_rights_edit_1,
      "user_rights_delete_1": user_rights_delete_1,
      "_token": _token1  // Add _token1 to the data
    };

    $.ajax({
      type: "GET",
      url: $("#CUR_ACTION").val(),
      data: sendInfo,
      success: function(data) {
        $('#list_div').html(data);
      }
    });
  }

$(function () {
  list_div('','','','','','');
});
function open_model(title, id) {
    $('#bd-example-modal-lg1 #model_main_content').html("...");
    var sendInfo = {};
    if (id == "") {
      sendInfo = { "action": "create_form" };
    } else {
      sendInfo = { "action": "update_form", "id": id };
    }
    var _token1 =$("#_token1").val();

    $.ajax({
      type: "GET",
      url: $("#CUR_ACTION").val(),
      data: sendInfo,
      headers: {
        'X-CSRF-TOKEN': _token1 // Add CSRF token here
      },
      success: function(data) {
        $('#bd-example-modal-lg1').modal('show');
        $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
        $('#bd-example-modal-lg1 #model_main_content').html(data);
      },
      error: function() {
        alert('error handling here');
      }
    });
  }

function load_model_sublist(main_id,sub_id)
{

  $('#bd-example-modal-lg1 #sublist_div').html("...");
  var sendInfo={"action":"form_sublist","main_id":main_id,"sub_id":sub_id,"user_rights_edit_1":user_rights_edit_1,"user_rights_delete_1":user_rights_delete_1};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#bd-example-modal-lg1 #sublist_div').html(data);
      //disbled_columns_upd();

    }
  });
}
function insert_update_row(id, expense_date, sales_rep_creation_id, market_manager_id, expense_no, status, description, mode_of_payment) {
    if ((expense_date) && (expense_no) && (mode_of_payment) && (status)) {
      var _token1 = $("#_token1").val(); // Get the CSRF token value

      if (id == "") {
        var sendInfo = {
          "action": "insert",
          "expense_date": expense_date,
          "sales_rep_creation_id": sales_rep_creation_id,
          "market_manager_id": market_manager_id,
          "expense_no": expense_no,
          "status": status,
          "description": description,
          "mode_of_payment": mode_of_payment,
          "_token1": _token1 // Add CSRF token to the data
        };
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
            alert('error handling here');
          }
        });
      } else {
        var sendInfo = {
          "action": "update",
          "id": id,
          "expense_date": expense_date,
          "sales_rep_creation_id": sales_rep_creation_id,
          "market_manager_id": market_manager_id,
          "expense_no": expense_no,
          "status": status,
          "description": description,
          "mode_of_payment": mode_of_payment,
          "_token": _token1 // Add CSRF token to the data
        };
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
            alert('error handling here');
          }
        });
      }
    } else {
      validate_inputs(expense_date, expense_no, status);
    }
  }

function validate_inputs(expense_date,expense_no,status)
{

  if(expense_date==='') { $("#expense_date").addClass('is-invalid');$("#expense_date_validate_div").html("Select Expense Date"); return false;} else {$("#expense_date").removeClass('is-invalid');$("#expense_date_validate_div").html("");}

  if(expense_no==='') { $("#expense_no").addClass('is-invalid');$("#expense_no_validate_div").html("Select Expense No"); return false;} else {$("#expense_no").removeClass('is-invalid');$("#expense_no_validate_div").html("");}

  if(status==='') { $("#status1").addClass('is-invalid');$("#status1_validate_div").html("Select Status"); return false;} else {$("#status1").removeClass('is-invalid');$("#status1_validate_div").html("");}
}


function insert_update_sub_row(main_id, id, ta_amount, total_amount) {
    // Get the CSRF token
    let _token1 = $("#_token1").val();

    // Collect form data
    let dealer_sub_id = $("#dealer_sub_id").val();
    let visitor_sub_id = $("#visitor_sub_id").val();
    let market_sub_id = $("#market_sub_id").val();
    let travel = $("#travel").val();
    let fuel = $("#fuel").val();
    let da_1 = $("#da_1").val();
    let courier = $("#courier").val();
    let lodging = $("#lodging").val();
    let phone = $("#phone").val();
    let others = $("#others").val();

    if (total_amount) {

        let formData = new FormData();
        formData.append("_token", _token1);
        formData.append("main_id", main_id || "");
        formData.append("ta_amount", ta_amount || "");
        formData.append("total_amount", total_amount || "");
        formData.append("dealer_sub_id", dealer_sub_id || "");
        formData.append("visitor_sub_id", visitor_sub_id || "");
        formData.append("market_sub_id", market_sub_id || "");
        formData.append("travel", travel || "");
        formData.append("fuel", fuel || "");
        formData.append("da_1", da_1 || "");
        formData.append("courier", courier || "");
        formData.append("lodging", lodging || "");
        formData.append("phone", phone || "");
        formData.append("others", others || "");

        let image = $("#image_name")[0].files[0];
        if (image) {
            formData.append("image_name", image);
        }

        if (!id && !main_id) {
            let expense_date = $("#expense_date").val();
            let sales_rep_creation_id = $("#sales_rep_creation_id").val();
            let market_manager_id = $("#market_manager_id").val();
            let expense_no = $("#expense_no").val();
            let status = $("#status1").val();
            let description = $("#description").val();
            if (expense_date && expense_no && status) {
                formData.append("action", "insert_sub");
                formData.append("expense_date", expense_date);
                formData.append("sales_rep_creation_id", sales_rep_creation_id || "");
                formData.append("market_manager_id", market_manager_id || "");
                formData.append("expense_no", expense_no);
                formData.append("status", status);
                formData.append("description", description || "");

                $.ajax({
                    type: "POST",
                    url: $("#CUR_ACTION").val(),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {


                        list_div();
                        $('#bd-example-modal-lg1 #model_main_content').html("...");

                        let updateForm = new FormData();
                        updateForm.append("action", "update_form");
                        updateForm.append("id", data.main_id);
                        updateForm.append("_token", _token1);


                        $.ajax({
                            type: "POST",
                            url: $("#CUR_ACTION").val(),
                            data: updateForm,
                            processData: false,
                            contentType: false,
                            success: function(data) {

                                $('#bd-example-modal-lg1 #model_main_content').html(data);
                            }
                        });
                    },
                    error: function() {
                        alert("Error handling here");
                    }
                });
            } else {

                validate_inputs(expense_date, expense_no, status);
            }
        } else {

            formData.append("action", id ? "update_sub" : "insert_sub");
            if (id) formData.append("id", id);

            $.ajax({
                type: "POST",
                url: $("#CUR_ACTION").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    list_div();
                    load_model_sublist(main_id, "");
                },
                error: function() {
                    alert("Error handling here");
                }
            });
        }
    } else {
        validate_sub_inputs(total_amount);
    }
}





function validate_sub_inputs(total_amount)
{

 /*  let market_manager_id = $("#market_manager_id").val();
  if(market_manager_id == ""){

  if(dealer_sub_id==='') { $("#dealer_sub_id").addClass('is-invalid');$("#dealer_sub_id_validate_div").html("Select Dealer"); return false;} else {$("#dealer_sub_id").removeClass('is-invalid');$("#dealer_sub_id_validate_div").html("");}

  if(market_sub_id==='') { $("#market_sub_id").addClass('is-invalid');$("#market_sub_id_validate_div").html("Select Market Name"); return false;} else {$("#market_sub_id").removeClass('is-invalid');$("#market_sub_id_validate_div").html("");}
  }else{
    $("#dealer_sub_id").removeClass('is-invalid');$("#dealer_sub_id_validate_div").html("");
    $("#market_sub_id").removeClass('is-invalid');$("#market_sub_id_validate_div").html("");
  }*/






  if(total_amount==='') { $("#total_amount").addClass('is-invalid');$("#total_amount_validate_div").html("Total Amount Empty"); return false;} else {$("#total_amount").removeClass('is-invalid');$("#total_amount_validate_div").html("");}
}
function delete_row(id) {
    swal({
      title: 'Are you sure?',
      text: 'To delete Expense Entry',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        var _token1 = $("#_token1").val(); // Get the CSRF token value

        var sendInfo = {
          "action": "delete",
          "id": id,
          "_token": _token1 // Add CSRF token to the data
        };

        $.ajax({
          type: "GET",
          url: $("#CUR_ACTION").val(),
          data: sendInfo,
          success: function(data) {
            swal('Deleted Successfully', { icon: 'success' });
            list_div();
          },
          error: function() {
            alert('error handling here');
          }
        });
      }
    });
  }

  function delete_sublist_row(main_id, id) {
    swal({
      title: 'Are you sure?',
      text: 'To delete Product',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        var _token1 = $("#_token1").val(); // Get the CSRF token value

        var sendInfo = {
          "action": "delete_sub",
          "id": id,
          "_token": _token1 // Add CSRF token to the data
        };

        $.ajax({
          type: "GET",
          url: $("#CUR_ACTION").val(),
          data: sendInfo,
          success: function(data) {
            list_div();
            load_model_sublist(main_id, '');
          },
          error: function() {
            alert('error handling here');
          }
        });
      }
    });
  }


//new works

function getstaff() {
    var entry_date = $("#expense_date").val();
    var _token1 = $("#_token1").val(); // Get the CSRF token value

    var sendInfo = {
        action: "getstaff",
        entry_date: entry_date,
        _token: _token1 // Add CSRF token to the data
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
          $("#sales_rep_creation_id").empty();
          $('#sales_rep_creation_id').append('<option value="">Select Sales Rep Name</option>');

          for (let i1 = 0; i1 < data.length; i1++) {
              $('#sales_rep_creation_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
          }
        },
        error: function() {
            alert('Error fetching Shop Name');
        }
    });
  }



  function get_dealer() {
    //alert("hai1");
    $("#visitor_sub_id").empty();
    var sales_rep_creation_id = $("#sales_rep_creation_id").val();
    var expense_date = $("#expense_date").val();
    var _token1 = $("#_token1").val(); // Get the CSRF token value

    var sendInfo = {
        action: "get_dealer",
        "sales_rep_creation_id": sales_rep_creation_id,
        "expense_date": expense_date,
        "_token": _token1 // Add CSRF token to the data
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
          $("#dealer_sub_id").empty();
          $('#dealer_sub_id').append('<option value="">Select Dealer Name</option>');

          for (let i1 = 0; i1 < data.length; i1++) {
              $('#dealer_sub_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
          }
        },
        error: function() {
            alert('Error fetching Shop Name');
        }
    });
  }


  function get_visitor() {
    //alert("hai1");
    var sales_rep_creation_id = $("#sales_rep_creation_id").val();
    var expense_date = $("#expense_date").val();
    var _token1 = $("#_token1").val(); // Get the CSRF token value

    var sendInfo = {
        action: "get_visitor",
        "sales_rep_creation_id": sales_rep_creation_id,
        "expense_date": expense_date,
        "_token": _token1 // Add CSRF token to the data
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
          $("#visitor_sub_id").empty();
          $('#visitor_sub_id').append('<option value="">Select Visitor Name</option>');

          for (let i1 = 0; i1 < data.length; i1++) {
              $('#visitor_sub_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['visitor_name'] + '</option>');
          }
        },
        error: function() {
            alert('Error fetching Visitor Name');
        }
    });
  }






  function get_market() {
    var entry_date = $("#expense_date").val();
    var sales_rep_creation_id = $("#sales_rep_creation_id").val();
    var dealer_sub_id = $("#dealer_sub_id").val();
    var _token1 = $("#_token1").val(); // Get the CSRF token value

    var sendInfo = {
        action: "get_market",
        dealer_sub_id: dealer_sub_id,
        sales_rep_creation_id: sales_rep_creation_id,
        entry_date: entry_date,
        "_token": _token1 // Add CSRF token to the data
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
          $("#market_sub_id").empty();
          $('#market_sub_id').append('<option value="">Select Sales Market Name</option>');

          for (let i1 = 0; i1 < data.length; i1++) {
              $('#market_sub_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['area_name'] + '</option>');
          }
        },
        error: function() {
            alert('Error fetching Shop Name');
        }
    });
  }


  function get_sub_expense() {
    var _token1 = $("#_token1").val(); // Get the CSRF token value

    var sendInfo = {
        action: "get_sub_expense",
        "_token": _token1 // Add CSRF token to the data
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
          // Handle the response here
          // alert(data);
        },
        error: function() {
            alert('Error fetching Shop Name');
        }
    });
  }


function disbled_columns() {
  var market_manager_id = $("#market_manager_id").val();
  //alert(market_manager_id);
  var sales_rep_creation_id = $('#sales_rep_creation_id');
  var dealer_sub_id = $('#dealer_sub_id');
  var market_sub_id = $('#market_sub_id');
  if ((market_manager_id != "") || (sales_rep_creation_id == "")) {
    sales_rep_creation_id.prop('disabled', true);

    dealer_sub_id.prop('disabled', true);
    market_sub_id.prop('disabled', true);
  } else {
    sales_rep_creation_id.prop('disabled', false);
    dealer_sub_id.prop('disabled', false);
    market_sub_id.prop('disabled', false);

  }
}


function disbled_column() {
  var sales_rep_creation_id = $("#sales_rep_creation_id").val();
  var market_manager_id = $('#market_manager_id');

  if (sales_rep_creation_id != "") {
    market_manager_id.prop('disabled', true);

  } else {
    market_manager_id.prop('disabled', false);
  }
}
function disbled_columns_upd() {
  var market_manager_id = $("#market_manager_id").val();
  //alert(market_manager_id);
  var sales_rep_creation_id = $('#sales_rep_creation_id');
  var dealer_sub_id = $('#dealer_sub_id');
  var market_sub_id = $('#market_sub_id');
  if ((market_manager_id != "") || (sales_rep_creation_id == "")) {
    sales_rep_creation_id.prop('disabled', true);
    dealer_sub_id.prop('disabled', true);
    market_sub_id.prop('disabled', true);
  } else {
    dealer_sub_id.prop('disabled', false);
    market_sub_id.prop('disabled', false);
  }
}

