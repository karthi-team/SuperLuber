function list_div(from_date_1, to_date_1, order_shop) {
    $("#list_div").html("");
    var _token = $("#_token").val();
    var sendInfo = {
        _token: _token,
        "action": "retrieve",
        "from_date_1": from_date_1,
        "to_date_1": to_date_1,
        'order_shop': order_shop,
        "user_rights_edit_1": user_rights_edit_1,
        "user_rights_delete_1": user_rights_delete_1
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

    list_div("", "", "");
});




function showDetails(td) {
    var row = td.closest('tr');
    $("#bd-example-modal-lg1 #model_main_content").html("...");

    var orderDate = row.cells[1].textContent;
    var managerName = row.cells[2].textContent;
    var salesExecutive = row.cells[3].textContent;
    var dealerName = row.cells[4].textContent;
    var areaName = row.cells[5].textContent;
    var orderNotTaken = row.cells[6].textContent;
    var orderTakenShop = row.cells[7].textContent;

    var modalContent = `<div class="table-responsive">

        <table class="table table-hover" id="rights_tableExport" style="width:100%;" border="1">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Manager Name</th>
                    <th>Sales Executive</th>
                    <th>Dealer Name</th>
                    <th>Area Name</th>
                    <th>Order Not Taken Shop</th>
                    <th>Order Taken Shop</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>${orderDate}</td>
                    <td>${managerName}</td>
                    <td>${salesExecutive}</td>
                    <td>${dealerName}</td>
                    <td>${areaName}</td>
                    <td>${formatShopList(orderNotTaken)}</td>
                    <td>${formatShopList(orderTakenShop)}</td>

                </tr>
            </tbody>
        </table>
    </div>`;


    document.querySelector('#bd-example-modal-lg1 .modal-body').innerHTML = modalContent;


    $('#rights_tableExport').DataTable({
        "dom": 'lBfrtip',
        "buttons": [
            'excel', 'pdf', 'print'
        ]
    });


    $('#bd-example-modal-lg1').modal('show');
    $("#bd-example-modal-lg1 #myLargeModalLabel").html("Shop View");
}

function formatShopList(shopList) {
    // Split the comma-separated list of shops
    const shopsArray = shopList.split(',');

    // Create a numbered list
    const numberedList = shopsArray.map((shop, index) => `${index + 1}. ${shop.trim()}`).join('<br>');

    return numberedList;
}


function sales_mang(){

    var manager_na = $("#manager_na").val();
    var _token=$("#_token").val();
    $("#sales_exec").empty();
    $("#sales_exec").append('<option value="">Select dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "getsalesexec",
        manager_na: manager_na,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data){

            $('#sales_exec').empty();
            $('#sales_exec').append('<option value="">Select dealer Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#sales_exec').append('<option value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}
function getdearlername() {


    var sales_exec = $("#sales_exec").val();
    var _token=$("#_token").val();
    $("#dealer_creation_id_1").empty();
    $("#dealer_creation_id_1").append('<option value="">Select dealer Name</option>');
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

function find_item_id()
{
  var group_id = $("#group_id").val();
  var sendInfo={"action":"getitemname","group_id":group_id};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    dataType: "json",
    success: function(data) {
      $('#item_name_1').empty();
      $('#item_name_1').append('<option  value="">Select</option>');
      for(let i1=0;i1 < data.length;i1++){
        $('#item_name_1').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['item_name'] + '</option>');
      }
    },
    error: function () {
        alert("Error fetching Group Name");
    },
  });
}
