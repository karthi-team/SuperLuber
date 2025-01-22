function list_div(
    from_date_1,
    to_date_1,
    sales_exec_1,
    dealer_creation_id_1,
    order_no_1,
) {
    $("#list_div").html("");
    var sendInfo = {
        action: "retrieve",
        from_date_1: from_date_1,
        to_date_1: to_date_1,
        sales_exec_1: sales_exec_1,
        dealer_creation_id_1: dealer_creation_id_1,
        order_no_1: order_no_1,
        user_rights_edit_1: user_rights_edit_1,
        user_rights_delete_1: user_rights_delete_1,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#list_div").html(data);
        },
    });
}
$(function () {
    list_div("", "", "", "", "", "");
});
function open_model(title, id) {
    $("#bd-example-modal-lg1 #model_main_content").html("...");
    var sendInfo = {};
    if (id == "") {
        sendInfo = { action: "create_form" };
    } else {
        sendInfo = { action: "update_form", id: id };
    }
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#bd-example-modal-lg1").modal("show");
            $("#bd-example-modal-lg1 #myLargeModalLabel").html(title);
            $("#bd-example-modal-lg1 #model_main_content").html(data);
        },
        error: function () {
            alert("error handing here");
        },
    });
}
function load_model_sublist(main_id, sub_id) {
    $("#bd-example-modal-lg1 #sublist_div").html("...");
    var sendInfo = {
        action: "form_sublist",
        main_id: main_id,
        sub_id: sub_id,
        user_rights_edit_1: user_rights_edit_1,
        user_rights_delete_1: user_rights_delete_1,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#bd-example-modal-lg1 #sublist_div").html(data);
        },
    });
}

function find_item_id()
{
  var group_creation_id = $("#group_creation_id").val();
  var sendInfo={"action":"getitemname","group_creation_id":group_creation_id};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    dataType: "json",
    success: function(data) {
      $('#item_creation_id').empty();
      $('#item_creation_id').append('<option  value="">Select</option>');
      for(let i1=0;i1 < data.length;i1++){
        $('#item_creation_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['item_name'] + '</option>');
      }
    },
    error: function () {
        alert("Error fetching Group Name");
    },
  });
}

function short_code() {

    var item_creation_id = $("#item_creation_id").val();
    var sendInfo = {
        action: "getshortcode",
        item_creation_id: item_creation_id,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#short_code_id').empty();

            for (let i1 = 0; i1 < data.length; i1++) {
                $('#short_code_id').append('<option value="' + data[i1]['id']+ '">' + data[i1]['short_code'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching here');
        }
    });

    var item_creation_id = $("#item_creation_id").val();
    var sendInfo = {
        action: "getitem_liters_type",
        item_creation_id: item_creation_id,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#item_weights').empty();

            for (let i2 = 0; i2 < data.length; i2++) {
                $('#item_weights').append('<option value="' + data[i2]['id']+ '">' + data[i2]['item_liters_type'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching here');
        }
    });

    var item_creation_id = $("#item_creation_id").val();
    var sendInfo = {
        action: "getitem_property",
        item_creation_id: item_creation_id,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#item_property').empty();


            for (let i3 = 0; i3 < data.length; i3++) {
                $('#item_property').append('<option value="' + data[i3]['id']+ '">' + data[i3]['item_properties_type'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching here');
        }
    });
}

function insert_update_row(
    id,
    order_date,
    dealer_creation_id,
    dealer_address,
    mop,
    order_no,
    status,
    credit_note_no,
    description,
    sales_exec
) {

    if (
        sales_exec && credit_note_no
    ) {
        if (id == "") {
            var sendInfo = {
                action: "insert",
                order_date: order_date,
                dealer_creation_id: dealer_creation_id,
                dealer_address: dealer_address,
                mop: mop,
                order_no: order_no,
                status: status,
                credit_note_no: credit_note_no,
                description: description,
                sales_exec: sales_exec,
            };
            $.ajax({
                type: "GET",
                url: $("#CUR_ACTION").val(),
                data: sendInfo,
                success: function (data) {

                    $("#bd-example-modal-lg1").modal("hide");
                    swal("Inserted Successfully", { icon: "success" });
                    list_div();
                },
                error: function () {
                    alert("error handing here");
                },
            });
        } else {
            var sendInfo = {
                action: "update",
                id: id,
                order_date: order_date,
                dealer_creation_id: dealer_creation_id,
                dealer_creation_id: dealer_creation_id,
                dealer_address: dealer_address,
                mop: mop,
                order_no: order_no,
                status: status,
                credit_note_no: credit_note_no,
                description: description,
                sales_exec: sales_exec,
            };
            $.ajax({
                type: "GET",
                url: $("#CUR_ACTION").val(),
                data: sendInfo,
                success: function (data) {
                    $("#bd-example-modal-lg1").modal("hide");
                    swal("Updated Successfully", { icon: "success" });
                    list_div();
                },
                error: function () {
                    alert("error handing here");
                },
            });
        }
    } else {
        validate_inputs(
            sales_exec,credit_note_no
        );
    }
}
function validate_inputs(
    sales_exec,credit_note_no
) {
    if (sales_exec === "") {
        $("#sales_exec").addClass("is-invalid");
        $("#sales_exec_validate_div").html("Select sales exec");
        return false;
    } else {
        $("#sales_exec").removeClass("is-invalid");
        $("#sales_exec_validate_div").html("");
    }
    if (credit_note_no === "") {
        $("#credit_note_no").addClass("is-invalid");
        $("#credit_note_no_validate_div").html("Select Credit Note No");
        return false;
    } else {
        $("#credit_note_no").removeClass("is-invalid");
        $("#credit_note_no_validate_div").html("");
    }
    // if (tally_no === "") {
    //     $("#tally_no").addClass("is-invalid");
    //     $("#tally_no_validate_div").html("Select Area name");
    //     return false;
    // } else {
    //     $("#tally_no").removeClass("is-invalid");
    //     $("#tally_no_validate_div").html("");
    // }
    // if (dealer_creation_id === "") {
    //     $("#dealer_creation_id").addClass("is-invalid");
    //     $("#dealer_creation_id_validate_div").html("Select Dealer name");
    //     return false;
    // } else {
    //     $("#dealer_creation_id").removeClass("is-invalid");
    //     $("#dealer_creation_id_validate_div").html("");
    // }
    // if (order_no === "") {
    //     $("#order_no").addClass("is-invalid");
    //     $("#order_no_validate_div").html("Select Shop name");
    //     return false;
    // } else {
    //     $("#order_no").removeClass("is-invalid");
    //     $("#order_no_validate_div").html("");
    // }
    // if (status === "") {
    //     $("#status1").addClass("is-invalid");
    //     $("#status1_validate_div").html("Select Status");
    //     return false;
    // } else {
    //     $("#status1").removeClass("is-invalid");
    //     $("#status1_validate_div").html("");
    // }
}


// desc oder recipt

function insert_update_sub_row(
    main_id,
    id,
    order_date_sub,
    time_sub,
    tally_no,
    batch_no,
    return_type_id,
    group_creation_id,
    item_creation_id,
    short_code_id,
    order_quantity,
    pieces_quantity,
    item_property,
    item_weights,
    item_price,
    total_amount,
    desc
) {

    var desc = $('#desc').val();

    if (
        item_creation_id &&
        order_quantity &&
        item_property &&
        item_weights &&
        item_price &&
        total_amount
    ) {
        if (id == "" && main_id == "") {
            var

                order_date = $("#order_date").val(),
                sales_exec = $("#sales_exec").val(),
                dealer_creation_id = $("#dealer_creation_id").val(),
                dealer_address = $("#dealer_address").val(),
                mop = $("#mop").val(),
                order_no = $("#order_no").val(),
                status = $("#status1").val(),
                credit_note_no = $("#credit_note_no").val(),
                description = $("#description").val();
            if (
                sales_exec && credit_note_no
            ) {
                var sendInfo = {
                    action: "insert_sub",
                    main_id: main_id,
                    order_date: order_date,
                    dealer_creation_id: dealer_creation_id,
                    dealer_address: dealer_address,
                    mop: mop,
                    order_no: order_no,
                    status: status,
                    credit_note_no: credit_note_no,
                    description: description,
                    sales_exec: sales_exec,
                    order_date_sub: order_date_sub,
                    time_sub: time_sub,
                    tally_no: tally_no,
                    desc: desc,
                    batch_no: batch_no,
                    return_type_id: return_type_id,
                    group_creation_id: group_creation_id,
                    item_creation_id: item_creation_id,
                    short_code_id:short_code_id,
                    order_quantity: order_quantity,
                    pieces_quantity: pieces_quantity,
                    item_property: item_property,
                    item_weights: item_weights,
                    item_price,
                    total_amount,
                };
                $.ajax({
                    type: "GET",
                    url: $("#CUR_ACTION").val(),
                    data: sendInfo,
                    success: function (data) {
                        list_div();

                        $("#bd-example-modal-lg1 #model_main_content").html(
                            "..."
                        );
                        var sendInfo1 = { action: "update_form", id: data };
                        $.ajax({
                            type: "GET",
                            url: $("#CUR_ACTION").val(),
                            data: sendInfo1,
                            success: function (data) {
                                $(
                                    "#bd-example-modal-lg1 #model_main_content"
                                ).html(data);
                            },
                        });
                    },
                    error: function () {
                        alert("error handing here");
                    },
                });
            } else {
                validate_inputs(
                    sales_exec,credit_note_no
                );
            }
        } else {
            var sendInfo = [];
            if (id == "") {
                sendInfo = {
                    action: "insert_sub",
                    main_id: main_id,
                    order_date_sub: order_date_sub,
                    time_sub: time_sub,
                    tally_no: tally_no,
                    desc: desc,
                    batch_no: batch_no,
                    return_type_id: return_type_id,
                    group_creation_id: group_creation_id,
                    item_creation_id: item_creation_id,
                    short_code_id:short_code_id,
                    order_quantity: order_quantity,
                    pieces_quantity: pieces_quantity,
                    item_property: item_property,
                    item_weights: item_weights,
                    item_price: item_price,
                    total_amount: total_amount,
                };
            } else {
                sendInfo = {
                    action: "update_sub",
                    id: id,
                    order_date_sub: order_date_sub,
                    time_sub: time_sub,
                    tally_no: tally_no,
                    desc: desc,
                    batch_no: batch_no,
                    return_type_id: return_type_id,
                    group_creation_id: group_creation_id,
                    item_creation_id: item_creation_id,
                    short_code_id:short_code_id,
                    order_quantity: order_quantity,
                    pieces_quantity: pieces_quantity,
                    item_property: item_property,
                    item_weights: item_weights,
                    item_price: item_price,
                    total_amount: total_amount,
                };
            }
            $.ajax({
                type: "GET",
                url: $("#CUR_ACTION").val(),
                data: sendInfo,
                success: function (data) {
                    alert(data);
                    list_div();
                    load_model_sublist(main_id, "");
                },
                error: function () {
                    alert("error handing here");
                },
            });
        }
    }
    validate_sub_inputs(
        item_creation_id,
        order_quantity,
        item_property,
        item_weights,
        item_price,
        total_amount
    );
}
function validate_sub_inputs(
    item_creation_id,
    order_quantity,
    item_property,
    item_weights,
    item_price,
    total_amount
) {
    if (item_creation_id === "") {
        $("#item_creation_id").addClass("is-invalid");
        $("#item_creation_id_validate_div").html("Select Product");
        return false;
    } else {
        $("#item_creation_id").removeClass("is-invalid");
        $("#item_creation_id_validate_div").html("");
    }
    if (order_quantity === "") {
        $("#order_quantity").addClass("is-invalid");
        $("#order_quantity_validate_div").html("Enter Quantity");
        return false;
    } else {
        $("#order_quantity").removeClass("is-invalid");
        $("#order_quantity_validate_div").html("");
    }
    if (item_property === "") {
        $("#item_property").addClass("is-invalid");
        $("#item_property_validate_div").html("Select Item Property");
        return false;
    } else {
        $("#item_property").removeClass("is-invalid");
        $("#item_property_validate_div").html("");
    }
    if (item_weights === "") {
        $("#item_weights").addClass("is-invalid");
        $("#item_weights_validate_div").html("Enter Item Weight");
        return false;
    } else {
        $("#item_weights").removeClass("is-invalid");
        $("#item_weights_validate_div").html("");
    }
    if (item_price === "") {
        $("#item_price").addClass("is-invalid");
        $("#item_price_validate_div").html("Price not exist");
        return false;
    } else {
        $("#item_price").removeClass("is-invalid");
        $("#item_price_validate_div").html("");
    }
    if (total_amount === "") {
        $("#total_amount").addClass("is-invalid");
        $("#total_amount_validate_div").html("Total Amount Empty");
        return false;
    } else {
        $("#total_amount").removeClass("is-invalid");
        $("#total_amount_validate_div").html("");
    }
}
function delete_row(id) {
    swal({
        title: "Are you sure?",
        text: "To delete company to dealer",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            var sendInfo = { action: "delete", id: id };
            $.ajax({
                type: "GET",
                url: $("#CUR_ACTION").val(),
                data: sendInfo,
                success: function (data) {
                    swal("Deleted Successfully", { icon: "success" });
                    list_div();
                },
                error: function () {
                    alert("error handing here");
                },
            });
        }
    });
}
function delete_sublist_row(main_id, id) {
    swal({
        title: "Are you sure?",
        text: "To delete Product",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            var sendInfo = { action: "delete_sub", id: id };
            $.ajax({
                type: "GET",
                url: $("#CUR_ACTION").val(),
                data: sendInfo,
                success: function (data) {
                    list_div();
                    load_model_sublist(main_id, "");
                },
                error: function () {
                    alert("error handing here");
                },
            });
        }
    });
}
function getdearlername1() {

    var sales_exec = $("#sales_exec_1").val();
    var _token=$("#_token").val();
    $("#dealer_creation_id_1").empty();
    $("#dealer_creation_id_1").append('<option value="">Select dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "getdearlername1",
        sales_exec: sales_exec,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#dealer_creation_id_1').empty();
            $('#dealer_creation_id_1').append('<option value="">Select dealer Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#dealer_creation_id_1').append('<option value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}

function marketname() {

    var dealer_creation_id_1 = $("#dealer_creation_id_1").val();
    var _token=$("#_token").val();
    $("#order_no_1").empty();
    $("#order_no_1").append('<option value="">Select dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "odernum",
        dealer_creation_id_1: dealer_creation_id_1,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#order_no_1').empty();
            $('#order_no_1').append('<option value="">Select Delivery Number </option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#order_no_1').append('<option value="' + data[i1]['order_no'] + '">' + data[i1]['order_no'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}

function getdearlername() {
    var sales_exec = $("#sales_exec").val();

    $("#dealer_creation_id").empty();
    $("#dealer_creation_id").append(
        '<option value="">Select dealer Name</option>'
    );
    var sendInfo = {
        action: "getdearlername",
        sales_exec: sales_exec,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#dealer_creation_id").empty();
            $("#dealer_creation_id").append(
                '<option value="">Select dealer Name</option>'
            );
            for (let i1 = 0; i1 < data.length; i1++) {
                $("#dealer_creation_id").append(
                    '<option value="' +
                        data[i1]["id"] +
                        '">' +
                        data[i1]["dealer_name"] +
                        "</option>"
                );
            }
        },
        error: function () {
            alert("Error fetching Shop Name");
        },
    });
}

function gettally_no() {
    var sales_exec = $("#sales_exec").val();
    var dealer_creation_id = $("#dealer_creation_id").val();

    var sendInfo = {
        action: "gettally_no",
        sales_exec: sales_exec,
        dealer_creation_id: dealer_creation_id,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#tally_no").empty();
            $("#tally_no").append('<option value="">Select Tally No</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $("#tally_no").append('<option value="' + data[i1]["id"] + '">' + data[i1]["tally_no"] + "</option>");
            }
        },
        error: function () {
            alert("Error fetching Tally No");
        },
    });
}

function getmarket() {

    var dealer_creation_id = $("#dealer_creation_id").val();

    var sendInfo = {
        action: "getmarket",
        dealer_creation_id: dealer_creation_id,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            if (data.hasOwnProperty('dealer_address') && data.dealer_address.length > 0) {
                const dealerAddress = data.dealer_address[0].address;
                $('#dealer_address').val(dealerAddress);
            }
        },
        error: function () {
            alert('Error fetching data');
        }
    });
}

function getshop() {

    var tally_no = $("#tally_no").val();

    $("#batch_no").empty();
    //   $("#batch_no").append('<option value="">Select Shop Name</option>');
    var sendInfo = {
        action: "getshop",
        tally_no: tally_no,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#batch_no').empty();
            //   $('#batch_no').append('<option value="">Select Shop Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#batch_no').append('<option value="' + data[i1]['id'] + '">' + data[i1]['shop_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching Shop Name');
        }
    });
}

// oder receipt
function descs() {
    var tally_no = $("#tally_no").val();

    var sendInfo = {
        action: "gettallydescno",
        tally_no: tally_no,
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#desc').empty();

            for (let i1 = 0; i1 < data.length; i1++) {
                $('#desc').append('<option value="' + data[i1]['id'] + '">' + data[i1]['description'] + '</option>');
            }
        },
        error: function () {
            alert('Error fetching data');
        }
    });
}
