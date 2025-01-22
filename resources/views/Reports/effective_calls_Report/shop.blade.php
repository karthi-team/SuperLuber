




<div class="table-responsive"><br>
    <table class="table table-hover" id="tableExport" style="width:100%;" border='1'>
        <?php
$grandTotal = 0;
?>
        <div class="table-responsive">
            <thead>
               <tr>
                    <th style="width: 5%;">Sno</th>
                    <th style="width: 8%;">Order Date</th>
                    <th style="width: 8%;">Manager Name</th>
                    <th style="width: 10%;">Sales Executive</th>
                    <th style="width: 10%;">Dealer Name</th>
                    <th style="width: 10%;">Area Name</th>
                    <th style="width: 8%;">Visit Shop</th>
                    <th style="width: 8%;">Order Shop</th>
                    <th style="width: 8%;">View Shop Name</th>
                    {{-- <th style="width: 8%;">Shop Name</th> --}}

                    <?php
                     $total_count_1 = [];
                     foreach ($item_creation_short as $item_creation_short1) {
                        $group_2=$item_creation_short1['group_name']
                        // $total_count_1[$group_2] = 0;
                        ?>
                        <th style="border-bottom: 1px solid black"><?php echo $item_creation_short1['group_name']; ?></th>
                    <?php } ?>
                    <th style="width: 8%;font-size:100%">Total</th>
                </tr>
                <tr style="border-bottom: 1px solid black">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>

                    <?php
                     $total_count_1 = [];
                     foreach ($item_creation_short as $item_creation_short1) {
                        // $total_count_1[$item_creation_short1->short_code] = 0;
                        ?>
                        <th><?php echo $item_creation_short1['short_code']; ?></th>
                    <?php } ?>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i1 = 1;
                $totalRows = $i1 - 1;
                $rowWiseStatusCheckCount = [];
                $rowWiseShortCode = [];
                $totalStatusCheckCount = 0;
                $totalStatusCheckCount_1 = 0;

                foreach ($sales_order_d2s_main as $sales_order_d2s_main1) {
                    $rowWiseStatusCheckCount[] = $sales_order_d2s_main1->status_check_count;
                    $rowWiseShortCode[] = $sales_order_d2s_main1->{$item_creation_short1['short_code']};
                    $totalStatusCheckCount += $sales_order_d2s_main1->status_check_count;
                    $totalStatusCheckCount_1 += $sales_order_d2s_main1->status_check_count_1;
                    $total = 0;

                    if (is_numeric($sales_order_d2s_main1->status_check_count)) {
                        $total += $sales_order_d2s_main1->status_check_count;
                    }
                    ?>
                    <tr>
                        <td><?php echo $i1;
                            $i1++; ?></td>

                        <td><?php echo $sales_order_d2s_main1->order_date; ?></td>
                        <td><?php echo $sales_order_d2s_main1->manager_name; ?></td>
                        <td><?php echo $sales_order_d2s_main1->sales_ref_name; ?></td>
                        <td><?php echo $sales_order_d2s_main1->dealer_name; ?></td>
                        <td><?php echo $sales_order_d2s_main1->area_name; ?></td>
                        <td><?php echo $visit = $sales_order_d2s_main1->status_check_count; ?>
                    </td>
                        <td>
                            <?php echo $order = ($sales_order_d2s_main1->status_check_count_1 > 0) ? $sales_order_d2s_main1->status_check_count_1 : '0'; ?>
                        </td>
                        <td><a href="{{ route('visit_shop_Report') }}" onclick="shop_view()"><i class="fas fa-eye"></i></a></td>

                        {{-- <td><?php echo $sales_order_d2s_main1->shop_name; ?></td> --}}
                        {{-- <td><i class="fas fa-eye"></i></td> --}}

                        <?php
                        //$total_item_qty = 0;

                        $itemTotals = [];
foreach ($item_creation_short as $item_creation_short1) {
    $itemTotals[$item_creation_short1['short_code']] = 0;
}


foreach ($item_creation_short as $item_creation_short1) {

    $shortCode = $item_creation_short1['short_code'];
    echo '<td>' . $sales_order_d2s_main1->$shortCode . '</td>';

    $itemTotals[$shortCode] += $sales_order_d2s_main1->$shortCode;

   // $itemTotals[$shortCode] += $sales_order_d2s_main1->$shortCode;

    //$total_item_qty += floatval($sales_order_d2s_main1->$shortCode);

    /* if ($shortCode == $sales_order_d2s_main1->$shortCode) {
        echo '<td>' . $sales_order_d2s_main1->order_1 . '</td>';
    } */
}
?>

                        <td><?php echo $total = $sales_order_d2s_main1->order_1; ?></td>
                        <?php $grandTotal += $total;?>
                    </tr>
                <?php } ?>
            </tbody>


            <tr >
                <td hidden></td>
                <td hidden></td>
                <td hidden></td>
                <td hidden></td>
                <td hidden></td>
                <td colspan="6" style="color:MediumSeaGreen;font-size:150%"><center><b>Total</b>
            </center></td>
                <td style="background-color:#f3f9f5f9"><?php echo $totalStatusCheckCount; ?></td>
                <td style="background-color:#f3f9f5f9"><?php echo $totalStatusCheckCount_1; ?></td>

             <?php
             $shortCode = '';

             foreach ($item_creation_short as $item_creation_short1) {
                $total_val = 0;
             foreach ($sales_order_d2s_main as $sales_order_d2s_main1) {

                    $shortCode = $item_creation_short1['short_code'];
                    $total_val +=floatval($sales_order_d2s_main1->$shortCode) ;


                }
                echo '<td style="background-color: MediumSeaGreen; color: aliceblue">' .$total_val . '</td>';
            }

?>



               <td style="background-color: yellow;color:red;font-size:150%"><?php echo $grandTotal; ?></td>
            </tr>


        </div>
    </table>
</div>
