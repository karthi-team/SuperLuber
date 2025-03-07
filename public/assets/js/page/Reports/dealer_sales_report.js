function list_div_1(from_date, to_date, dealer_id,group_id,item_id,manager_na,rep_id) {
    $('#list_div').html("");

   
    if(!isEmpty(manager_na) || !isEmpty(rep_id) || !isEmpty(dealer_id)){
    if (!validate_dealer_id(manager_na, rep_id, dealer_id)) {
        return; 
    }
    }

    var sendInfo = {
        "action": "retrieve",
        "from_date": from_date,
        "to_date": to_date,
        //"rep_id": rep_id,
        "dealer_id": dealer_id,
        "group_id":group_id,
        "item_id":item_id,
        //'manager_na': manager_na,
        "user_rights_edit_1": user_rights_edit_1,
        "user_rights_delete_1": user_rights_delete_1
    };

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#list_div').html(data);
        }
    });
}

function isEmpty(value) {
    return (
        value === null || 
        value === undefined || 
        value === '' || 
        (Array.isArray(value) && value.length === 0) || 
        (typeof value === 'object' && Object.keys(value).length === 0)
    );
}

function validate_dealer_id(manager_na, rep_id, dealer_id) {
    if (isEmpty(manager_na) || isEmpty(rep_id) || isEmpty(dealer_id)) {
        alert('Please fill in all required fields: Manager Name, Sales Excutive Name, and Dealer Name.');
        return false;
    }
    return true;
}

// function list_div(from_date, to_date, rep_id, dealer_id, manager_na) {
//     $('#list_div').html("");

//     var sendInfo = {
//         "action": "retrieve",
//         "from_date": from_date,
//         "to_date": to_date,
//         "rep_id": rep_id,
//         "dealer_id": dealer_id,
//         'manager_na': manager_na,
//         "user_rights_edit_1": user_rights_edit_1,
//         "user_rights_delete_1": user_rights_delete_1
//     };

//     $.ajax({
//         type: "GET",
//         url: $("#CUR_ACTION").val(), // Replace with your server endpoint URL
//         data: sendInfo,
//         success: function (data) {
//             $('#list_div').html(data);
//         }
//     });
// }

$(function () {

    list_div_1('', '', '', '', '','','','','');
});


function sales_mang(){
    var manager_na = $("#manager_na").val();
    var _token=$("#_token").val();
    $("#excutive_id").empty();
    $("#excutive_id").append('<option value="">Select Excecutive Name</option>');
    $("#dealer_id").empty();
    // $("#dealer_id").append('<option value="">Select Dealer Name</option>');

    var sendInfo = {
        _token:_token,
        action: "getsalesexec",
        manager_na: manager_na,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#excutive_id').empty();
            $('#excutive_id').append('<option value="">Select Executive Name</option>'); 
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#excutive_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}
function getdearlername() {

    //alert();

    var sales_exec = $("#excutive_id").val();
    var _token=$("#_token").val();
    $("#dealer_id").empty();
    $("#dealer_id").append('<option value="">Select Dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "getdearlername",
        sales_exec: sales_exec,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#dealer_id').empty();
            $('#dealer_id').append('<option value="">Select Dealer Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#dealer_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}















function get_item()
{

  var group_id = $("#group_id").val();

  //alert(countryId)

  var sendInfo={"action":"getitemName","group_id":group_id};


      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#item_id').empty();

          $('#item_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#item_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['item_name'] + '</option>');
       }
        }
      });

}
