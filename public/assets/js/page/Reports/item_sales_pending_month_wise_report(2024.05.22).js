// function list_div(from_date,to_date,sales_ref_id,item_id,manager_id,dealer_id)
// {
//     var dealer_ids = $('#dealer_id').val();
//     var group_id = $('#group_id').val();
//     // alert(group_id)
//   $('#list_div').html("");
//   var sendInfo={"action":"retrieve","from_date":from_date,"to_date":to_date,"sales_ref_id":sales_ref_id,"item_id":item_id,"manager_id":manager_id,"dealer_id":dealer_ids,"group_id":group_id};
//   $.ajax({
//     type: "GET",
//     url: $("#CUR_ACTION").val(),
//     data: sendInfo,
//     success: function(data){
//       $('#list_div').html(data);

//     }
//   });
// }
// $(function () {
//   list_div('','','','','');
// });


// function list_div(from_date, to_date, sales_ref_id, item_id, manager_id, dealer_id, sales_det) {
//     var dealer_ids = $('#dealer_id').val();
//     var group_id = $('#group_id').val();
//     var group_by = $('#group_by').val();

//     $('#list_div').html("");

//     var sendInfo;
//     var action;

//     if (group_by === 'con') {

//         action = 'concatenate_action';
//         sendInfo = {
//             "action": action,
//             "from_date": from_date,
//             "to_date": to_date,
//             "manager_id": manager_id,
//             "group_by": group_by
//         };console.log(sendInfo);
//     } else {

//         action = 'retrieve';
//         sendInfo = {
//             "action": action,
//             "from_date": from_date,
//             "to_date": to_date,
//             "sales_ref_id": sales_ref_id,
//             "item_id": item_id,
//             "manager_id": manager_id,
//             "dealer_id": dealer_ids,
//             "group_id": group_id,
//             "sales_det": sales_det
//         };
//     }

//     $.ajax({
//         type: "GET",
//         url: $("#CUR_ACTION").val(),
//         data: sendInfo,
//         success: function (data) {
//             $('#list_div').html(data);
//         }
//     });
// }

// $(function () {
//     list_div('', '', '', '', '', '');
// });


function list_div(from_date, to_date, sales_ref_id, item_id, manager_id, dealer_id, sales_det,ref_by) {
    var dealer_ids = $('#dealer_id').val();
    var group_id = $('#group_id').val();
    var group_by = $('#group_by').val();
    var ref_by = $('#ref_by').val();

    $('#list_div').html("");

    var sendInfo;
    var action;

    if (group_by === 'con') {

        action = 'concatenate_action';
        sendInfo = {
            "action": action,
            "from_date": from_date,
            "to_date": to_date,
            "manager_id": manager_id,
            "group_by": group_by
        }
    } else if(ref_by === 'ref') {

        action = 'ref_action';
        sendInfo = {
            "action": action,
            "from_date": from_date,
            "to_date": to_date,
            "sales_ref_id": sales_ref_id,
            "ref_by": ref_by,

      };
    }
    else {

        action = 'retrieve';
        sendInfo = {
            "action": action,
            "from_date": from_date,
            "to_date": to_date,
            "sales_ref_id": sales_ref_id,
            "item_id": item_id,
            "manager_id": manager_id,
            "dealer_id": dealer_ids,
            "group_id": group_id,
            "sales_det": sales_det
        };
    }

    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#list_div').html(data);
        }
    });
}

$(function () {
    list_div('', '', '', '', '', '');
});

function disableFields() {
    var groupBySelect = document.getElementById('group_by');
    var managerSelect = document.getElementById('manager_id');
    var dealerSelect = document.getElementById('dealer_id');
    var groupSelect = document.getElementById('group_id');
    var itemSelect = document.getElementById('item_id');
    var salesSelect = document.getElementById('sales_ref_id');
    var refSelect = document.getElementById('ref_by');
    var salesdetSelect = document.getElementById('sales_det');

    var isManagerSelected = managerSelect.value !== "";

    groupBySelect.disabled = !isManagerSelected;

    var isConcatenateSelected = groupBySelect.value === "con";

    dealerSelect.disabled = isConcatenateSelected;
    groupSelect.disabled = isConcatenateSelected;
    itemSelect.disabled = isConcatenateSelected;
    salesSelect.disabled = isConcatenateSelected;
    refSelect.disabled = isConcatenateSelected;
    salesdetSelect.disabled = isConcatenateSelected;
}

function disableFields1() {
    var groupBySelect = document.getElementById('group_by');
    var managerSelect = document.getElementById('manager_id');
    var dealerSelect = document.getElementById('dealer_id');
    var groupSelect = document.getElementById('group_id');
    var itemSelect = document.getElementById('item_id');
    var salesSelect = document.getElementById('sales_ref_id');
    var refSelect = document.getElementById('ref_by');
    var salesdetSelect = document.getElementById('sales_det');

    var isRefBySelected = refSelect.value !== "";

    groupBySelect.disabled = isRefBySelected;
    managerSelect.disabled = isRefBySelected;
    dealerSelect.disabled = isRefBySelected;
    groupSelect.disabled = isRefBySelected;
    itemSelect.disabled = isRefBySelected;
    salesdetSelect.disabled = isRefBySelected;
    salesSelect.disabled = false;
}

disableFields();
disableFields1();

function find_sales_ref()
{

  var manager_id = $("#manager_id").val();
  //alert(countryId)

  var sendInfo={"action":"getSalesRef","manager_id":manager_id};
    if (manager_id) {
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#sales_ref_id').empty();
          $('#sales_ref_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#sales_ref_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
       }
        }
      });
    } else {
      $('#sales_ref_id').empty();

    }

}

function find_dealer_name()
{

  var sales_ref_id = $("#sales_ref_id").val();

  //alert(countryId)

  var sendInfo={"action":"getDealerName","sales_ref_id":sales_ref_id};
    if ( sales_ref_id ) {

      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#dealer_id').empty();

          $('#dealer_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#dealer_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
       }
        }
      });
    } else {
      $('#dealer_id').empty();

    }

}
function get_item_name()
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
            $('#item_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['short_code'] + '</option>');
       }
        }
      });

}