<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport" onclick="exportToCSV()">Excel</button>
<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport" onclick="generatePDF()">PDF</button>
<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport" onclick="printTable()">Print</button>


<style>
    .green-background {
        background-color: #ADD8E6;
        /* Optional: To ensure text is readable on the green background */
    }
    .green-color {
        font-weight: bold;
        background-color: #ADD8E6;
        color: red;
        
    }
    .green-colors {
        font-weight: bold;
        background-color: #ADD8E6;
        color: black;
        
    }
    .green-colorss {
        font-weight: bold;
        font-size: 21px;
        background-color: #ADD8E6;
        color: red;
        
    }
    .label2 {
    color: blue; /* Set the color to blue or any other color you prefer */
}
    .label1 {
    color: green; /* Set the color to blue or any other color you prefer */
}
</style>
<br> <br>

<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;" border="1">
        <thead>
            <tr>
                <th>Place</th>
                <th>S.No</th>
                <th>Dealer Name</th>
                <?php foreach ($item_creation as $item_creation1) { ?>
                    <th hidden></th>
                    <th colspan="2" style="border-bottom: 1px solid black">
                        <center><?php echo $item_creation1->group_name; ?></center>
                    </th>
                <?php } ?>
                <th>No.Of Secondary Visit</th>
                <th width="50%">Other Visit</th>
                {{-- <th>No.Of Other visit</th> --}}
                <th>Total Secondary Sales</th>
                <th width="20%">Date of Last Visit</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <?php foreach ($item_creation as $item_creation1) { ?>
                    <th colspan="2" style="border-bottom: 1px solid black">
                        <center><?php echo $item_creation1->short_code; ?></center>
                    </th>
                <?php } ?>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr style="border-bottom: 1px solid black">
                <th></th>
                <th></th>
                <th></th>
                <?php foreach ($item_creation as $item_creation1) { ?>
                    <th>Secondary <?php echo $item_creation1->short_code; ?></th>
                    <th>Closing <?php echo $item_creation1->short_code; ?></th>
                <?php } ?>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $dealerCount = count($arr);
            $dealerIndex = 0;
            $dealerCount_2 = count($arr);
            $dealerIndex_2 = 0;
            $dealerCount_3 = count($arr);
            $dealerIndex_3 = 0;
            $dealerCount_4 = count($arr);
            $dealerIndex_4 = 0;
            $dealerCount_5 = count($arr);
            $dealerIndex_5= 0;
            $dealerIndex_6 = count($arr);
            $dealerIndex_6= 0;
            $i1 = 1; // Initialize counter
            $total_a = [];
            $t_sum_total_status_check = 0;
            $t_status_count = 0;
            $t_no_count = 0;
            $t_visit_count = 0;

            foreach ($market_creation as $market_creation1) {
                $test_market_id = "";
            ?>

                @foreach($opening_stock_rep as $sales_box_report_gats)
                    @if($sales_box_report_gats->t_order_qty !='')
                        <?php    ?>
                        <tr>
                            <td>{{ $arr[$dealerIndex]['place'] }}</td>
                            @php
                            $dealerIndex = ($dealerIndex + 1) % $dealerCount;
                            @endphp
                            <td><?php echo $i1;
                                $i1++; ?></td>
                            <td>{{ $arr[$dealerIndex_2]['dealer_names'] }}</td>
                            @php
                            $dealerIndex_2 = ($dealerIndex_2 + 1) % $dealerCount_2;
                            @endphp
                            <?php
                            foreach ($item_creation as $item_creation1) {
                                $found = false;
                                $Tsum_order_quantity = 0;
                                foreach ($sales_box_report_2 as $sales_box_report_2_1) {
                                    if ($sales_box_report_2_1->dealer_creation_id == $market_creation1->dealer_id && $item_creation1->group_id == $sales_box_report_2_1->group_creation_id) {
                                        $sum_order_quantity = $sales_box_report_2_1->total_order_quantity;
                                        $sum_status_count = $sales_box_report_2_1->total_status_check;
                                        $sum_balance_quantity = $sales_box_report_2_1->total_current_stock;
                                        $balance_pending = $sum_balance_quantity - $sum_order_quantity;
                            ?>
                                        <td <?php echo !empty($sum_order_quantity) ? 'class="green-background"' : ''; ?>><?php echo empty($sum_order_quantity) ? 0 : $sum_order_quantity;
                                                                                                                                $Tsum_order_quantity += $sum_order_quantity; ?></td>
                                    <?php
                                        $found = true;
                                        break; // Stop searching after finding the first matching item
                                    }
                                }
                                $get = false;
                                if (!$found) {
                                    ?>
                                    <td>0</td>
                                <?php
                                }
                                foreach ($opening_stock_rep as $opening_stock1) {
                                    $dealer_creation_id = $opening_stock1->dealer_creation_id;
                                    $group_creation_id = $opening_stock1->group_creation_id;
                                    $t_order_qty = $opening_stock1->t_order_qty;
                                    if ($dealer_creation_id == $market_creation1->dealer_id && $group_creation_id == $item_creation1->group_id) {
                                ?>
                                        <td class="green-background"><?php echo $t_order_qty; ?></td>
                            <?php
                                        $get = true;
                                    }
                                }
                                if (!$get) {
                            ?>
                                    <td>0</td>
                            <?php
                                }
                            }
                            ?>
                            @if(isset($arr[$dealerIndex_3]['total_status_count']))
                                @php
                                $t_status_count += $arr[$dealerIndex_3]['total_status_count'];
                                @endphp
                                <td <?php echo !empty($arr[$dealerIndex_3]['total_status_count']) ? 'class="green-background"' : ''; ?>>{{ $arr[$dealerIndex_3]['total_status_count'] }}</td>
                            @else
                                <td>0</td>
                            @endif




                            <?php
                            $dealer_last_count = $get_last_count_3->where('dealer_creation_id', $market_creation1->dealer_id)->first();
                            $sales_rep_id = $market_creation1->sales_rep_id;

                            $sales_last_count = $total_visitor_dis->where('dealer_creation_id', $market_creation1->dealer_id);



                            if ($dealer_last_count) {
                                $t_no_count += $dealer_last_count->total_status_count_3;
                            }

                            ?>
                         {{--  --}}
                            <td>
                                <strong class="label2">Visits :</strong>
                                @if($dealer_last_count && $dealer_last_count->total_status_count_3)
                                    {{ $dealer_last_count->total_status_count_3 }}
                                @else
                                    -
                                @endif

                                <br>
                                <strong class="label1">{{ 'Date & Purpose of Visits' }}:</strong>
                               <br>
                                @if($sales_last_count->isNotEmpty())
                                    @foreach ($sales_last_count as $visitor_dis_item)
                                    {{ $visitor_dis_item->order_date }} =>
                                        {{ $visitor_dis_item->visitor_dis }},
                                        <br>
                                    @endforeach
                                @endif
                            </td>

                          {{--  --}}



                            <?php
                            $found_cunt = false;
                            foreach ($sales_box_report_count as $sales_box_report_count1) {
                                if ($sales_box_report_count1->dealer_id == $market_creation1->dealer_id) {
                                    $sum_total_status_check = $sales_box_report_count1->total_status_count;
                                    $sum_total_order_quantity = $sales_box_report_count1->total_order_quantity;
                            ?>
                                    <td <?php echo !empty($sum_order_quantity) ? 'class="green-background"' : ''; ?>><?php echo empty($sum_total_order_quantity) ? 0 : $sum_total_order_quantity; ?></td>
                                <?php
                                    $found_cunt = true;
                                    break;
                                }
                            }
                            if (!$found_cunt) {
                                ?>
                                <td>0</td>
                            <?php
                            }
                            ?>
                            @if(isset($arr[$dealerIndex_3]['entry_date']))
                                <td <?php echo !empty($arr[$dealerIndex_3]['entry_date']) ? 'class="green-background"' : ''; ?>>{{ $arr[$dealerIndex_3]['entry_date'] ?? 0}}</td>
                            @else
                                <td>0</td>
                            @endif
                            @php
                            $dealerIndex_3 = ($dealerIndex_3 + 1) % $dealerCount_3; // Rotate through the dealers in $arr
                            @endphp
                        </tr>
                    @else
                        <tr>
                            <td colspan="3">
                                <center>No Data Available..</center>
                            </td>
                        </tr>
                    @endif
                    @break
                @endforeach
            <?php
            }
            ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td class="green-color">Secondary Total </td>
                    <?php
                    $get_total_visit_count = 0;
                    $grandTotalSupplied = 0;
                    $grandTotalPending = 0;
                    foreach ($item_creation as $item_creation1) {
                        $itemOrderTotal = 0;
                        $itemPendingTotal = 0;
                        $breake_loop = false;
                        foreach ($market_creation as $market_creation1) {
                            foreach ($sales_box_report_2 as $sales_box_report_2_1) {
                                if ($sales_box_report_2_1->dealer_creation_id == $market_creation1->dealer_id && $item_creation1->group_id == $sales_box_report_2_1->group_creation_id) {
                                    $itemOrderTotal += $sales_box_report_2_1->total_order_quantity;
                                    $breake_loop = true;
                                }
                            }
                            foreach ($opening_stock_rep as $opening_stock_rep_1) {
                                if ($opening_stock_rep_1->dealer_creation_id == $market_creation1->dealer_id && $item_creation1->group_id == $opening_stock_rep_1->group_creation_id) {
                                    $itemPendingTotal += $opening_stock_rep_1->t_order_qty;
                                }
                            }
                        }
                    ?>
                        <td <?php echo !empty($itemOrderTotal) ? 'class="green-colors"' : ''; ?>><?php echo $itemOrderTotal; ?></td>
                        <td <?php echo  !empty($itemPendingTotal) ? 'class="green-colors"' : ''; ?>><?php echo $itemPendingTotal; ?></td>
                        <?php
                        $grandTotalSupplied += $itemOrderTotal;
                        $grandTotalPending += $itemPendingTotal;
                        ?>
                    <?php } ?>


                   <td <?php echo !empty($grandTotalSupplied) ? 'class="green-colorss"' : ''; ?>><?php echo $t_status_count; ?></td>


                    <td <?php echo !empty($grandTotalSupplied) ? 'class="green-colorss"' : ''; ?>>
                        <?php echo $t_no_count; ?>
                    </td>


                    <td <?php echo !empty($grandTotalPending) ? 'class="green-colorss"' : ''; ?>><?php echo $grandTotalSupplied; ?></td>
                    <td>0</td>
                </tr>

   {{-- Not Taken Dealer --}}

        <?php $vk = 1; ?>

      

    {{-- end --}}
                </tbody>


{{-- other visitors --}}
                <tbody>
                   
                   
                        <tr>
                            <th></th>
                            <th>S.No</th>
                            <th>Sales Ref Name</th>
                            <th></th>
                            <?php foreach ($item_creation as $item_creation1) { ?>
                                <th colspan="2">
                                    
                                </th>
                            <?php } ?>
                            <th style="color:blue">New Party Visits Details</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <?php $vk = 1; 
                        $total_count11 = 0; ?>
                      
                        @foreach($get_new_visitor as $get_new_visitor)
                           
                                <tr>
                                    <td></td>
                                    <td>{{ $vk++ }}</td>
                                    <td>{{ $get_new_visitor->sales_ref_name }}</td>
                                    <td>0</td>
                                    <?php foreach ($item_creation as $item_creation1) { ?>
                                        <td colspan="2">
                                            0
                                        </td>
                                    <?php } ?>
                                    <td>      
                                        <?php 
                                            $total_count1 = 0;
                                            $visit_details = explode(',', $get_new_visitor->visitor_dis);
                                            $visit_dates = explode(',', $get_new_visitor->order_date);
                                            $num_visits = count($visit_details);
                                         ?>                     
                                            <br>
                                            <strong>{{ 'Count' }}:</strong>   {{ $get_new_visitor->total_count }}
      
                                                <?php $total_count1 += $get_new_visitor->total_count; ?>
                                                <br>
                                                <strong class="label1">{{ 'Date and Purpose of Visits' }}:</strong> <br>
                                                @for ($i = 0; $i < $num_visits; $i++)
                                                    {{ $visit_dates[$i] }} => {{ $visit_details[$i] }}
                                                    @if ($i < $num_visits - 1)
                                                        <br>
                                                    @endif
                                                @endfor
                                                <br>
                                                -------------------
                                              <br>
                                        <b>&emsp;&emsp;Total Count&nbsp;: {{ $total_count1 }}</b>
                                    </td>
                                    <td>0</td>
                                    <td></td>
                                </tr>
                                        <?php 
                                   
                                    $total_count11 += $total_count1; 
                                    $get_total_visit_count = $t_status_count + $t_no_count + $total_count11;
                                    ?>

                                    @endforeach

                               <tr>
                               <td></td>
                               <td></td> 
                               <td></td>
                               <td></td>
                               <?php foreach ($item_creation as $item_creation1) { ?>
                                        <td colspan="2">          
                                        </td>
                                <?php } ?>
                               <td style="color:black"><b>&emsp;&emsp;New Party Visits Total Count&nbsp;:<span style="font-size:20px; color:red;">{{ $total_count11 }}</b></span></td>
                                </tr>

                                <tr>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <?php foreach ($item_creation as $item_creation1) { ?>
                                        <td colspan="2">          
                                        </td>
                                <?php } ?>
                               <td style="color:black"><b>&emsp;&emsp;OverAll Visits Count&nbsp;: <span style="font-size:20px; color:red;">{{ $get_total_visit_count }}</b></span></td>
                                </tr>
                            
                       
                        
                    {{-- @if (!$foundAnyData)
                        <tr>
                            <td colspan="3">
                                <h3><center>No Data Available..</center></h3>
                            </td>
                        </tr>
                    @endif --}}
                </tbody>
 {{--end  --}}
    </table>



</div>






</div>



<script>
    function exportToCSV() {
        const table = document.getElementById('tableExport');
        const rows = table.querySelectorAll('tr');
        const csvData = [];


        csvData.push('\uFEFF');
        const headerRow1 = ['', '', '', '', '', '', '', 'SuperLuber', '', '', '', '', '', '', ''];
        csvData.push(headerRow1.join(','));

        const headerRow2 = ['', '', '', '', '', '', '', 'Sales Box Report', '', '', '', '', '', '', ''];
        csvData.push(headerRow2.join(','));

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const rowData = [];
            const cells = row.querySelectorAll('td');

            for (let j = 0; j < cells.length; j++) {
                const cellData = cells[j].textContent.trim();
                rowData.push(`"${cellData.replace(/"/g, '""')}"`);
            }

            csvData.push(rowData.join(','));
        }

        const csvContent = csvData.join('\n');
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'Sales Box Report.csv';

        document.body.appendChild(a);
        a.click();

        window.URL.revokeObjectURL(url);
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function generatePDF() {
        const {
            jsPDF
        } = window.jspdf;

        // Adjust page size to fit more columns
        var doc = new jsPDF('l', 'mm', [2500, 2050]);

        doc.setFontSize(30);
        doc.text("Sales Box Report-{{$from_date}}", 15, 10);

        var pdfjs = document.querySelector('#tableExport');

        doc.html(pdfjs, {
            callback: function(doc) {
                doc.save("Sales Box Report.pdf");
            },
            x: 10,
            y: 50
        });
    }

    function printTable() {
        const table = document.getElementById('tableExport');
        const printWindow = window.open('', '', 'width=1800,height=900');

        const headerRow1 = ['SuperLuber'];
        const headerRow2 = ['SALES BOX REPORT'];

        printWindow.document.open();
        printWindow.document.write('<html><head><title>Print</title></head><body>');
        printWindow.document.write('<h1>Sales Box Reports </h1>');

        printWindow.document.write('<div style="display:flex;">');
        for (const header of headerRow1) {
            printWindow.document.write('<div style="flex:1; text-align:center;">' + header + '</div>');
        }
        printWindow.document.write('</div>');

        printWindow.document.write('<div style="display:flex;">');
        for (const header of headerRow2) {
            printWindow.document.write('<div style="flex:1; text-align:center;">' + header + '</div>');
        }
        printWindow.document.write('</div>');


        printWindow.document.write('</div>');

        printWindow.document.write(table.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }
</script>
