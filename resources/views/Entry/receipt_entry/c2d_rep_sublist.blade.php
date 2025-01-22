{{-- <style>
    .hidden-td {
        display: none;
    }
</style> --}}

<div style="width:100%;">

</div><br><br>
<table class="table table-sm table-hover" style="width:100%;">
    <thead>
        <tr class="text-center">
            <th scope="col">S.No</th>
            <th scope="col">Date</th>
            <th scope="col">Total Amount</th>
            <th scope="col">Paid Amount</th>
            <th scope="col">Balance Amount</th>
            <th scope="col">Pay Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i1 = 1;
        $return_quantity = 0;
        $Total_Amount = 0;
        foreach ($sales_order_delivery_sub_list as $sales_order_delivery_sub_list1) {
        ?>
                <tr>
                    <td><?php echo $i1;
                        $i1++; ?></td>
                    <td>
                        <input type="date" id="order_date_sub" name="order_date_sub" class="form-control order_date_sub" value="<?php echo date("Y-m-d"); ?>" readonly>
                    </td>
                    <td>
                        <input type="text" id="total_amount" class="form-control total_amount" data-i="<?php echo $i1; ?>" value="<?php echo isset($sales_order_delivery_sub_list1['total_amount'])? $sales_order_delivery_sub_list1['total_amount'] : 0; ?>" placeholder="Amount" readonly />
                    </td>
                    <td>
                        <input type="text" id="paid_amount" class="form-control paid_amount" data-i="<?php echo $i1; ?>" value="<?php echo isset($sales_order_delivery_sub_list1['paid_amount'])? $sales_order_delivery_sub_list1['paid_amount'] : 0; ?>" placeholder="Amount" readonly/>

                    </td>
                    <td>
                        <input type="text" id="bal_amount" class="form-control bal_amount" data-i="<?php echo $i1; ?>" value="<?php  echo isset($sales_order_delivery_sub_list1['bal_amount'])? $sales_order_delivery_sub_list1['bal_amount'] : 0;?>" placeholder="Amount" readonly/>
                    </td>
                    <td>
                        <input type="text" id="pay_amount" class="form-control pay_amount" data-i="<?php echo $i1; ?>" value="<?php if (isset($sales_order_delivery_sub_list1['pay_amount'])) { echo $sales_order_delivery_sub_list1['pay_amount']; } ?>" placeholder="Amount" onblur="checkTotalAmount(this)" oninput="add_bal_amount();add_paid_amount();" onkeyup = "calculateBalance(this)" /> <span class="error_message" data-i="<?php echo $i1; ?>" style="color: red;"></span>
                    </td>
                    <td>
                        <input type="hidden" id="check_amount" class="form-control check_amount" data-i="<?php echo $i1; ?>" value="<?php  echo isset($sales_order_delivery_sub_list1['paid_amount'])? $sales_order_delivery_sub_list1['paid_amount'] : 0;?>" placeholder="Amount" readonly/>
                    </td>
                </tr>
        <?php
            }
        ?>
    </tbody>
</table>
<script>
$(function () {
    $(".select2_comp1").select2();
});

function add_bal_amount(){
    var total_amount_val_1 = parseFloat($("#total_amount").val());
    var pay_amount_val_1 = parseFloat($("#pay_amount").val());
    var check_amount = parseFloat($("#check_amount").val());
    total_bal_amount = total_amount_val_1 - (pay_amount_val_1 + check_amount);
    $('#bal_amount').val(total_bal_amount);
}

function add_paid_amount(){
    var total_amount_val_1 = parseFloat($("#total_amount").val());
    var pay_amount_val_1 = parseFloat($("#pay_amount").val());
    var check_amount = parseFloat($("#check_amount").val());
    total_bal_amount = total_amount_val_1 - (pay_amount_val_1 + check_amount);
    total_paid_amount = total_amount_val_1 - total_bal_amount;
    $('#paid_amount').val(total_paid_amount);
}

function perm1_function(checkbox, rowIndex) {
    var checked = checkbox.checked;
    document.querySelectorAll('.customcheckbox_div_checkbox').forEach(function(checkbox, index) {
        if (index === rowIndex) return;
        checkbox.checked = checked;
        var dispatchStatusSelect = document.getElementById('dispatch_status_' + (index - 1));
        if (dispatchStatusSelect) {
            dispatchStatusSelect.value = checked ? "1" : "0";
        }

    });
}
function perm_function(checkbox, rowIndex) {
    var checked = checkbox.checked;
    var row = checkbox.closest('tr');
    var checkboxesInRow = row.querySelectorAll('.customcheckbox_div_checkbox');
    checkboxesInRow.forEach(function(checkbox) {
        checkbox.checked = checked;
    });
    var dispatchStatusSelect = document.getElementById('dispatch_status_' + rowIndex);
    dispatchStatusSelect.value = checked ? "1" : "0";
}
function updateSelectAllValue(checkbox) {
    if (checkbox.checked) {
        checkbox.value = 1;
    } else {
        checkbox.value = 0;
    }
}
    function checkTotalAmount(input) {
    var i = input.getAttribute('data-i');
    var totalAmount = parseFloat(document.querySelector('.total_amount_1[data-i="' + i + '"]').value);
    var totalAmount1 = parseFloat(document.querySelector('.pay_amount[data-i="' + i + '"]').value);
    var errorMessage = document.querySelector('.error_message[data-i="' + i + '"]');

    if (isNaN(totalAmount) || isNaN(totalAmount1)) {
        errorMessage.textContent = "Please enter valid numbers.";
    } else if (totalAmount1 > totalAmount) {
        errorMessage.textContent = "Paid Amount should not be greater than Amount";
    } else {
        errorMessage.textContent = "";
    }
}

// function calculateBalance(input) {

//     var i = input.getAttribute('data-i');
//     var totalAmount = parseFloat(document.querySelector('.total_amount_1[data-i="' + i + '"]').value);
//     var totalAmount1 = parseFloat(document.querySelector('.pay_amount[data-i="' + i + '"]').value);
//     var balAmountField = document.querySelector('.check_amount[data-i="' + i + '"]');

//     if (!isNaN(totalAmount) && !isNaN(totalAmount1)) {
//         var balanceAmount = totalAmount - totalAmount1;
//         balAmountField.value = Math.abs(balanceAmount);
//     } else {
//         balAmountField.value = '';
//     }
// }
</script>
