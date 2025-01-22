<div class="table-responsive">

    <table class="table table-sm table-hover" style="width:100%;">
        <thead>
            <tr class="text-center">
                <th scope="col" style="width:4%;">S.No</th>
                <th scope="col" style="width:10%;">Visitor Name</th>
                <th scope="col" style="width:8%;">Dealer Name</th>
                <th scope="col">Market Name test</th>
                <th scope="col">Travel</th>
                <th scope="col">Fuel</th>
                <th scope="col">DA</th>
                <th scope="col">Courier</th>
                <th scope="col">Lodging</th>
                <th scope="col">Phone</th>
                <th scope="col">Others</th>
                <th scope="col" style="width:16%;">TA Amount</th>
                <th scope="col" style="width:16%;">T Amount</th>
                <th scope="col" style="width:16%;">Image Upload</th>
                <th scope="col">View</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php $dealer_sub_id = '';
                $visitor_sub_id = '';
                $market_sub_id = '';
                $total_amount = '';
                $ta_amount = '';
                $image_name = '';
                if ($expense_creations_sub != null) {
                    $dealer_sub_id = $expense_creations_sub['dealer_sub_id'];
                    $visitor_sub_id = $expense_creations_sub['visitor_sub_id'];
                    $market_sub_id = $expense_creations_sub['market_sub_id'];
                    $travel = $expense_creations_sub['travel'];
                    $fuel = $expense_creations_sub['fuel'];
                    $da_1 = $expense_creations_sub['da_1'];
                    $courier = $expense_creations_sub['courier'];
                    $lodging = $expense_creations_sub['lodging'];
                    $phone = $expense_creations_sub['phone'];
                    $others = $expense_creations_sub['others'];
                    $image_name = $expense_creations_sub['image_name'];
                    $ta_amount = $expense_creations_sub['ta_amount'];
                    $total_amount = $expense_creations_sub['total_amount'];
                } ?>
                <td>#</td>

                <td>
                    <select class="form-control select2_comp1" id="visitor_sub_id" style="width:100%;">

                        <option value="">Select</option>
                        <?php foreach($visitor_creation as $visitor_creation1){ ?>
                        <option value="<?php echo $visitor_creation1['id']; ?>" <?php if ($visitor_creation1['id'] == $visitor_sub_id) {
                            echo ' selected';
                        } ?>><?php echo $visitor_creation1['visitor_name']; ?></option>
                        <?php } ?>

                    </select>
                    <div id="visitor_sub_id_validate_div" class="mark_label_red"></div>
                </td>


                <td>
                    <select class="form-control select2_comp1" id="dealer_sub_id" style="width:100%;"
                        onchange="get_market()">

                        <option value="">Select</option>
                        <?php foreach($dealer_creation as $dealer_creation1){ ?>
                        <option value="<?php echo $dealer_creation1['id']; ?>" <?php if ($dealer_sub_id != '') {
                            if ($dealer_creation1['id'] == $dealer_sub_id) {
                                echo ' selected';
                            }
                        } else {
                            if ($main_id != '') {
                                if ($dealer_creation1['id'] == $last_id_sub_expense) {
                                    echo ' selected';
                                }
                            }
                        } ?>><?php echo $dealer_creation1['dealer_name']; ?></option>
                        <?php } ?>

                    </select>
                    <div id="dealer_sub_id_validate_div" class="mark_label_red"></div>
                </td>
                <td>
                    <select class="form-control select2_comp1" id="market_sub_id" style="width:100%;">

                        <option value="">Select</option>
                        <?php foreach($market_creation as $market_creation1){ ?>
                        <option value="<?php echo $market_creation1['id']; ?>" <?php if ($market_sub_id != '') {
                            if ($market_creation1['id'] == $market_sub_id) {
                                echo ' selected';
                            }
                        } else {
                            if ($main_id != '') {
                                if ($market_creation1['id'] == $last_id_market) {
                                    echo ' selected';
                                }
                            }
                        } ?>><?php echo $market_creation1['area_name']; ?></option>
                        <?php } ?>



                    </select>
                    <div id="market_sub_id_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="travel" style="width: 150px;"
                        value="<?php echo isset($travel) ? $travel : '0.00'; ?>" placeholder="0.00" />
                    <div id="travel_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="fuel" style="width: 150px;"
                        value="<?php echo isset($fuel) ? $fuel : '0.00'; ?>" placeholder="0.00" />
                    <div id="fuel_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="da_1" style="width: 150px;"
                        value="<?php echo isset($da_1) ? $da_1 : '0.00'; ?>" />
                    <div id="da_1_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="courier" style="width: 150px;"
                        value="<?php echo isset($courier) ? $courier : '0.00'; ?>" placeholder="0.00" />
                    <div id="courier_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="lodging" style="width: 150px;"
                        value="<?php echo isset($lodging) ? $lodging : '0.00'; ?>" placeholder="0.00" />
                    <div id="lodging_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="phone" style="width: 150px;"
                        value="<?php echo isset($phone) ? $phone : '0.00'; ?>" placeholder="0.00" />
                    <div id="phone_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="others" style="width: 150px;"
                        value="<?php echo isset($others) ? $others : '0.00'; ?>" placeholder="0.00" />
                    <div id="others_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="text" class="form-control" id="ta_amount" style="width: 150px;"
                        value="<?php echo $ta_amount; ?>" placeholder="0.00" />
                    <div id="total_amount_validate_div" class="mark_label_red"></div>
                </td>
                <td>
                    <input type="text" class="form-control" id="total_amount" style="width: 150px;"
                        value="<?php echo $total_amount; ?>" placeholder="Amount" readonly />
                    <div id="total_amount_validate_div" class="mark_label_red"></div>
                </td>

                <td>
                    <input type="file" class="form-control" id="image_name" name="image_name"
                        onchange="previewImage(event)" style="width:150px;">
                </td>
                <td>

                    <img id="image_preview"
                        src="{{ $image_name != '' ? asset('storage/expense_img/' . $image_name) : asset('storage/default/default_image.png') }}"
                        width="35" height="35" alt="Image Preview" data-toggle="popover"
                        data-trigger="hover" data-html="true"
                        data-content='<img src="{{ $image_name != '' ? asset('storage/expense_img/' . $image_name) : asset('storage/default/default_image.png') }}" width="100" height="100" alt="Image Preview">' />

                </td>


                <td class="text-center">
                    <?php if($sub_id==""){ ?>
                    <button type="button" class="btn btn-success btn-action" id="sublist_edit"
                        onclick="insert_update_sub_row('<?php echo $main_id; ?>','<?php echo $sub_id; ?>',ta_amount.value,total_amount.value)">Add</button>
                    <?php }else{ ?>
                    <button type="button" class="btn btn-primary btn-action" id="sublist_edit"
                        onclick="insert_update_sub_row('<?php echo $main_id; ?>','<?php echo $sub_id; ?>',ta_amount.value,total_amount.value)">Edit</button>
                    <?php } ?>
                </td>
            </tr>
            <?php
        $i1=1;$Total_Amount=0;

        foreach($expense_creations_sub_list as $expense_creations_sub_list1)
        { ?>

            <tr>

                <td><?php echo $i1;
                $i1++; ?></td>
                <td><?php echo $expense_creations_sub_list1['visitor_sub_id']; ?></td>
                <td><?php echo $expense_creations_sub_list1['dealer_sub_id']; ?></td>
                <td><?php echo $expense_creations_sub_list1['market_sub_id']; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['travel']) ? $expense_creations_sub_list1['travel'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['fuel']) ? $expense_creations_sub_list1['fuel'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['da_1']) ? $expense_creations_sub_list1['da_1'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['courier']) ? $expense_creations_sub_list1['courier'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['lodging']) ? $expense_creations_sub_list1['lodging'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['phone']) ? $expense_creations_sub_list1['phone'] : '0.00'; ?></td>
                <td><?php echo isset($expense_creations_sub_list1['others']) ? $expense_creations_sub_list1['others'] : '0.00';
                $image_name = $expense_creations_sub_list1['image_name']; ?></td>
                <td><?php echo $expense_creations_sub_list1['ta_amount']; ?></td>
                <td class="text-right"><?php $Total_Amount1 = doubleval($expense_creations_sub_list1['total_amount']);
                echo number_format($Total_Amount1, 2);
                $Total_Amount += $Total_Amount1; ?></td>
                <td></td>
                <td>
                    <img id="image_preview" src="<?php echo isset($image_name) && $image_name != '' ? asset('storage/expense_img/' . $image_name) : asset('storage/default/default_image.png'); ?>" width="35" height="35"
                        alt="Image Preview" data-toggle="popover" data-trigger="hover" data-html="true"
                        data-content="
             <img src='<?php echo isset($image_name) && $image_name != '' ? asset('storage/expense_img/' . $image_name) : asset('storage/default/default_image.png'); ?>'
                  width='100' height='100' alt='Image Preview'>
         " />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-action"
                        onclick="load_model_sublist('<?php echo $main_id; ?>','<?php echo $expense_creations_sub_list1['id']; ?>');"><i
                            class="fas fa-pencil-alt">

                        </i></button>
                    <button type="button" class="btn btn-danger btn-action"
                        onclick="delete_sublist_row('<?php echo $main_id; ?>','<?php echo $expense_creations_sub_list1['id']; ?>')"><i
                            class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col" class="text-right">Total :</th>
                <th scope="col" class="text-right"><?php echo number_format($Total_Amount, 2); ?></th>
                <th scope="col"></th>
            </tr>
        </tfoot>
    </table>

</div>
<script>
    var expense_creation_list = {
        <?php foreach ($expense_creation as $expense_creation1) {
            echo '"' . $expense_creation1['id'] . '":"' . $expense_creation1['distributor_rate'] . '",';
        } ?>
    };

    $(function() {
        $(".select2_comp1").select2();

    });


    $(document).ready(function() {

        $('#travel,#fuel,#da_1,#courier,#lodging,#phone,#others').on('input', function() {
            calculateTotalAmount();
        });

 function calculateTotalAmount() {
            var ta_amount = 0;
            var travel = 0;
            var fuel = 0;
            var da_1 = 0;
            var courier = 0;
            var lodging = 0;
            var phone = 0;
            var others = 0;
            var total_amount = 0;
            var ta_amount = parseFloat($('#ta_amount').val()) || 0;
            var travel = parseFloat($('#travel').val()) || 0;
            var fuel = parseFloat($('#fuel').val()) || 0;
            var da_1 = parseFloat($('#da_1').val()) || 0;
            var courier = parseFloat($('#courier').val()) || 0;
            var lodging = parseFloat($('#lodging').val()) || 0;
            var phone = parseFloat($('#phone').val()) || 0;
            var others = parseFloat($('#others').val()) || 0;
            var total_amount = travel + fuel + da_1 + courier + lodging + phone + others;
            $('#ta_amount').val(total_amount.toFixed(2));
            $('#total_amount').val(total_amount.toFixed(2));
        }
        calculateTotalAmount();
    });

    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imageUrl = event.target.result;
                const imagePreview = document.getElementById('image_preview');
                imagePreview.src = imageUrl;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
