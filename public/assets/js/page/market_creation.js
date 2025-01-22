
function list_div() {
    $("#list_div").html("");
    var sendInfo = {
        action: "retrieve",
        user_rights_edit_1: user_rights_edit_1,
        user_rights_delete_1: user_rights_delete_1,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $("#list_div").html(data);
            $(function () {
                $("#tableExport").DataTable({
                    dom: "lBfrtip",
                    buttons: [
                        {
                            extend: "excel",
                            text: "Excel",
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4],
                            },
                        },
                        {
                            extend: "pdf",
                            text: "PDF",
                            //text: '<i class="far fa-file-pdf"></i>',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4],
                            },
                        },
                        {
                            extend: "print",
                            text: "Print",
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4],
                            },
                        },
                    ],
                });
            });
        },
        error: function () {
            alert("error handing here");
        },
    });
}
$(function () {
    list_div();
});
function open_model(title, id) {
    $("#bd-example-modal-lg1 #model_main_content").html("...");
    var sendInfo = {};
    if (id == "") {
        sendInfo = { action: "create_form" };
    }
    
    else {
        console.log(sendInfo,id);
        sendInfo = { action: "update_form", id: id };
        console.log(sendInfo);
        
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
function insert_update_row(
    id,
    operator,
    description,
    pumpstatus,
    datetime,
    duration
) {
    // First, validate the inputs
    if (
        !validate_inputs(operator, description, pumpstatus, datetime, duration)
    ) {
        return; 
    }

    var sendInfo = {};

    // action to insert
    if (id === "") {
        console.log(id);
        
        sendInfo = {
            action: "insert",
            operator: operator,
            description: description,
            pumpstatus: pumpstatus,
            datetime: datetime,
            duration: duration,
        };

            console.log(sendInfo);
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
                alert("Error handling insert.");
            },
        });
    } else {
        // Update case
        console.log(sendInfo);
        
        sendInfo = {
            action: "update",
            id: id,
            operator: operator,
            description: description,
            pumpstatus: pumpstatus,
            datetime: datetime,
            duration: duration,
        };
        
        console.log(sendInfo);
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
                alert("Error handling update.");
            },
        });
    }
}
function delete_row(id) {
    swal({
        title: "Are you sure?",
        text: "To delete Market Creation",
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

function getDistricts() {
    var stateId = $("#state_id").val();

    var sendInfo = { action: "getDistricts", state_id: stateId };

    if (stateId) {
        $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            dataType: "json",
            success: function (data) {
                $("#district_id1").empty();
                $("#district_id1").append(
                    '<option value="" readonly>-----select district-----</option>'
                );
                $.each(data, function (index, district) {
                    $("#district_id1").append(
                        '<option value="' +
                            district.id +
                            '">' +
                            district.district_name +
                            "</option>"
                    );
                });
            },
        });
    } else {
        $("#district_id1").empty();
    }
}



function validate_inputs(
    operator,
    description,
    pumpstatus,
    datetime,
    duration
) {
    let isValid = true;

    // Validate operator
    if (operator === "") {
        $("#operator").addClass("is-invalid");
        $("#operator_validate_div").html("Enter the operator name");
        isValid = false;
    } else {
        $("#operator").removeClass("is-invalid");
        $("#operator_validate_div").html("");
    }

    // Validate description
    if (description === "") {
        $("#description").addClass("is-invalid");
        $("#description_validate_div").html("Enter description");
        isValid = false;
    } else {
        $("#description").removeClass("is-invalid");
        $("#description_validate_div").html("");
    }

    // Validate pumpstatus
    if (pumpstatus === "") {
        $("#pumpstatus").addClass("is-invalid");
        $("#pumpstatus_validate_div").html("Select Status");
        isValid = false;
    } else {
        $("#pumpstatus").removeClass("is-invalid");
        $("#pumpstatus_validate_div").html("");
    }

    // Validate datetime
    if (datetime === "") {
        $("#datetime").addClass("is-invalid");
        $("#datetime_validate_div").html("Enter the Date and Time");
        isValid = false;
    } else {
        $("#datetime").removeClass("is-invalid");
        $("#datetime_validate_div").html("");
    }

    // Validate duration
    if (duration === "") {
        $("#duration").addClass("is-invalid");
        $("#duration_validate_div").html("Enter Duration");
        isValid = false;
    } else {
        $("#duration").removeClass("is-invalid");
        $("#duration_validate_div").html("");
    }

    return isValid;
}




