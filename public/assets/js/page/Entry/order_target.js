function list_div(from_date_1, to_date_1, sales_executive_id_1) {
    $('#list_div').html("");
    var sendInfo = { "action": "retrieve", "from_date_1": from_date_1, "to_date_1": to_date_1, "sales_executive_id_1": sales_executive_id_1 };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#list_div').html(data);
        },
        error: function () {
            alert('error handing here');
        }
    });
}
$(function () {
    list_div("", "", "");
});

function open_model(title, id) {
    $('#bd-example-modal-lg1 #model_main_content').html("...");
    var sendInfo = {};
    sendInfo = id == "" ? { "action": "create_form" } : { "action": "update_form", "id": id };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            if (id == ""){
                $('#bd-example-modal-lg1').modal('show');
                $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
                $('#bd-example-modal-lg1 #model_main_content').html(data);
            } else {
                load_model_sublist(id, '');
                $('#bd-example-modal-lg1').modal('show');
                $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
                $('#bd-example-modal-lg1 #model_main_content').html(data);
            }
        },
        error: function () {
            alert('error handing here');
        }
    });
}
function open_print(title, id, category_type) {
    $('#bd-example-modal-lg1 #model_main_content').html("...");
    var sendInfo = {};
    sendInfo = { "action": "view_sublist_form", "id": id, "category_type": category_type };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#bd-example-modal-lg1').modal('show');
            $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
            $('#bd-example-modal-lg1 #model_main_content').html(data);
        },
        error: function () {
            alert('error handing here');
        }
    });
}

function load_model_sublist(main_id, sales_executive_id) {
    if (main_id == '') {
        $('#bd-example-modal-lg1 #sublist_div').html("...");
        var sendInfo = { "action": "sublist_form", "main_id": main_id, "sales_executive_id": sales_executive_id, "user_rights_edit_1": user_rights_edit_1, "user_rights_delete_1": user_rights_delete_1 };
        $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            success: function (data) {
                $('#bd-example-modal-lg1 #sublist_div').html(data);
            }
        });
    } else {
        $('#bd-example-modal-lg1 #sublist_div').html("...");
        var sendInfo = { "action": "update_sublist_form", "main_id": main_id, "sales_executive_id": sales_executive_id, "user_rights_edit_1": user_rights_edit_1, "user_rights_delete_1": user_rights_delete_1 };
        $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            success: function (data) {
                $('#bd-example-modal-lg1 #sublist_div').html(data);
            }
        });
    }
}
function category_type_id(category_type) {
    $('#bd-example-modal-lg1 #sublist_div').html("...");
    var sendInfo = { "action": "sublist_form", "category_type": category_type };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#bd-example-modal-lg1 #sublist_div').html(data);
        }
    });
}

function category_type_id1(id, category_type) {
    $('#bd-example-modal-lg1 #sublist_div').html("...");
    var sendInfo = { "action": "update_sublist_form", "id": id, "category_type": category_type };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#bd-example-modal-lg1 #sublist_div').html(data);
        }
    });
}

function insert_update_row(id, entry_date, target_number, sales_executive_id, description) {
    var _token1 = $("#_token1").val();
    var row_value = parseInt($('#row_value').val());
    var col_value = parseInt($('#col_value').val());

    var jsonArray = [];

    for (let i1 = 1; i1 <= row_value; i1++) {
        var dataObject = {
            row: i1,
            dealer_id: null,
            groupDetails: []
        };

        var dealer_elements = document.getElementsByClassName('dealer_id_' + i1);
        if (dealer_elements.length > 0) {
            dataObject.dealer_id = dealer_elements[0].value;
        }

        var groupDetailsMap = {};

        for (let i2 = 1; i2 <= col_value; i2++) {
            var group_id = null;
            var itemDetail = {
                col: i2,
                item_id: null,
                target_quantity: null
            };

            var group_elements = document.getElementsByClassName('group_id_' + i1 + '_' + i2);
            if (group_elements.length > 0) {
                group_id = group_elements[0].value;
            }

            var item_elements = document.getElementsByClassName('item_id_' + i1 + '_' + i2);
            if (item_elements.length > 0) {
                itemDetail.item_id = item_elements[0].value;
            }

            var target_elements = document.getElementsByClassName('target_quantity_' + i1 + '_' + i2);
            if (target_elements.length > 0) {
                itemDetail.target_quantity = target_elements[0].value;
            }

            if (group_id) {
                if (!groupDetailsMap[group_id]) {
                    groupDetailsMap[group_id] = {
                        row: i1,
                        col: i2,
                        group_id: group_id,
                        itemDetails: []
                    };
                }

                if (itemDetail.item_id || itemDetail.target_quantity) {
                    groupDetailsMap[group_id].itemDetails.push(itemDetail);
                }
            }
        }

        dataObject.groupDetails = Object.values(groupDetailsMap).sort((a, b) => a.col - b.col);

        if (dataObject.dealer_id || dataObject.groupDetails.length > 0) {
            jsonArray.push(dataObject);
        }
    }

    var order_target = JSON.stringify(jsonArray);

    if(id == ''){
        var sendInfo = {
            "_token": _token1,
            "action": "insert",
            "entry_date": entry_date,
            "target_number": target_number,
            "sales_executive_id": sales_executive_id,
            "description": description,
            "order_target": order_target
        };

        $.ajax({
            type: "POST",
            url: $("#CUR_ACTION1").val(),
            data: sendInfo,
            success: function (data) {
                $('#bd-example-modal-lg1').modal('hide');
                swal('Inserted Successfully', { icon: 'success' });
                list_div();
            },
            error: function () {
                alert('Error handling here');
            }
        });
    } else {
        var sendInfo = {
            "action": "update",
            "id": id,
            "entry_date": entry_date,
            "target_number": target_number,
            "sales_executive_id": sales_executive_id,
            "description": description,
            "order_target": order_target
        };

        $.ajax({
            type: "POST",
            url: $("#CUR_ACTION1").val(),
            data: sendInfo,
            success: function (data) {
                $('#bd-example-modal-lg1').modal('hide');
                swal('Updated Successfully', { icon: 'success' });
                list_div();
            },
            error: function () {
                alert('Error handling here');
            }
        });
    }

}

function validate_inputs(entry_date, shift_type) {
    if (entry_date === '') { $("#entry_date").addClass('is-invalid'); $("#entry_date_validate_div").html("Enter User Type"); return false; } else { $("#entry_date").removeClass('is-invalid'); $("#entry_date_validate_div").html(""); }
    if (shift_type === '') { $("#shift_type1").addClass('is-invalid'); $("#shift_type1_validate_div").html("Select shift_type"); return false; } else { $("#shift_type1").removeClass('is-invalid'); $("#shift_type1_validate_div").html(""); }
}

function delete_row(id) {
    swal({
        title: 'Are you sure?',
        text: 'To delete Order Target',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {

                var sendInfo = { "action": "delete", "id": id };
                $.ajax({
                    type: "GET",
                    url: $("#CUR_ACTION").val(),
                    data: sendInfo,
                    success: function (data) {
                        swal('Deleted Successfully', { icon: 'success', });
                        list_div();
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }
        });
}
