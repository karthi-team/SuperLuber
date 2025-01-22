<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport" onclick="exportToCSV()">Excel</button>
<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport" onclick="generatePDF()">PDF</button>
<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport" onclick="printTable()">Print</button>
<style>
    .select2-container .select2-selection--single .select2-selection__rendered {
    padding-right: 128px;
}
.green-background {
    background-color: #ADD8E6;
}

</style>
<br>  <br>  <br>
<link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">

  <div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;" border="1">
        <thead>
            <tr>
                <th style="border-bottom: 1px solid black;background-color: #cfcfcfad;">S.NO</th>
                <th style="border-bottom: 1px solid black;background-color: #cfcfcfad;">Sales Ref Name</th>
                <th style="border-bottom: 1px solid black;background-color: #cfcfcfad;">Dealer Name</th>
                <th style="border-bottom: 1px solid black;background-color: #cfcfcfad;">Dealer Address</th>
                <th style="border-bottom: 1px solid black;background-color: #cfcfcfad;">Order Description</th>
                <?php
                $idArray = [];
                if($manager!=''){
                    foreach ($item_creation as $item_creation1) {
                        $idArray[] = $item_creation1->id;
                        ?>
                        {{-- <th hidden></th> --}}
                        <th colspan="2" style="border-bottom: 1px solid black;background-color: #fff8a3ad;">
                            <center><?php echo $item_creation1->group_name; ?></center>
                        </th>
                        <?php if($sales_det == 'pending'){

                            }elseif ($sales_det == 'dispatch') {

                            }else{?>
                            <th hidden></th>
                            <?php }
                       
                    }
                }
                ?>
            </tr>

            @if($manager!='')
            <tr>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <?php
                $idArray = [];
                foreach ($item_creation as $item_creation1) {
                    $idArray[] = $item_creation1->id;
                    ?>
                    {{-- <th hidden></th> --}}
                    <th colspan="2" style="border-bottom: 1px solid black;background-color: #fff8a3ad;">
                        <center><?php echo $item_creation1->short_code; ?></center>
                    </th>
                    <?php if($sales_det == 'pending'){

                    }elseif ($sales_det == 'dispatch') {

                    }else{?>
                    <th hidden></th>
                    <?php }
                }
                ?>
            </tr>

            <tr style="border-bottom: 1px solid black">
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <th style="background-color: #cfcfcfad;"></th>
                <?php
                $idArray = [];
                foreach ($item_creation as $item_creation1) {
                    if($sales_det == 'pending'){

                        ?>
                    <td class="pending-column" style="border-bottom: 1px solid black;background-color: #e98ed185;">Pending</td>
                        <?php
                    }elseif ($sales_det == 'dispatch') {

                        ?>
                    <td class="dispatched-column" style="border-bottom: 1px solid black;background-color: #4dffe6ad;">Dispatched</td>

                        <?php
                        # code...
                    }else{
                    ?>
                {{-- changed --}}

                   <td class="dispatched-column" style="border-bottom: 1px solid black;background-color: #4dffe6ad;">Dispatched</td>

                   <td class="pending-column" style="border-bottom: 1px solid black;background-color: #e98ed185;">Pending</td>

                <?php } }
                if($sales_det == 'pending'){?>
                <th class="pending-column" style="background-color: #cfcfcfad;">Pending Total</th>
                <?php
                    }elseif ($sales_det == 'dispatch') {?>

                <th class="dispatched-column" style="background-color: #cfcfcfad;">Dispatched Total</th>

            <?php }else{?>

                <th class="dispatched-column" style="background-color: #cfcfcfad;">Dispatched Total</th>
                <th class="pending-column" style="background-color: #cfcfcfad;">Pending Total</th>

                <?php } ?>
            </tr>

        </thead>

       <tbody>
    <?php
    $i1 = 1;
    ?>

    @php
    $dealerCount = count($arr);
    $dealerIndex = 0;
    $columnTotals = array();
    $cnt_bal = 0;
    @endphp

    @foreach ($sales_order_deli as $sales_order_deli1)
    <tr>
        <td><?php echo $i1; $i1++; ?></td>
        <td>{{ $arr[$dealerIndex]['sales_ref'] }}</td>
        <td>{{ $arr[$dealerIndex]['dealer_names'] }}</td>
        <td>{{ isset($sales_order_deli1->place) ? $sales_order_deli1->place : '' }}</td>
        <td>{{ isset($sales_order_deli1->description) ? $sales_order_deli1->description : '' }}</td>

        @php
        $dealerIndex = ($dealerIndex + 1) % $dealerCount;
        @endphp

        @foreach ($item_creation as $item_creations)
        @php
        $dealer = $sales_order_deli1->dealer_creation_id;
        $get_item = $item_creations->id;
        $found = false;
        $get = false;
        @endphp

        @php
        $orderCount = count($arr_c2d);
        $orderIndex_c2d = 0;
        @endphp
        @foreach ($order_delivery as $order_deliverys)
        @php
        $get_another_dealer = $order_deliverys->dealer_creation_id;
        @endphp

        @if ($dealer == $get_another_dealer && $get_item == $order_deliverys->item_creation_id)
        @php
        $balance_qty = $order_deliverys->sodsc_balance_quantity;
        $cnt_bal += $balance_qty;
        @endphp
        {{-- changed --}}
        @if($sales_det == 'pending')

        <td class="pending-column" class="green-background">{{ $arr_c2d[$orderIndex_c2d]['soc2ds_order_quantity'] - $balance_qty }}</td>

        @elseif ($sales_det == 'dispatch')

        <td class="dispatched-column" class="green-background">{{ $balance_qty }}</td>

        @else

        <td class="dispatched-column" class="green-background">{{ $balance_qty }}</td>

        <td class="pending-column" class="green-background">{{ $arr_c2d[$orderIndex_c2d]['soc2ds_order_quantity'] - $balance_qty }}</td>

        @endif

      
        @if (!empty($balance_qty))
        @php
        $found = true;
        @endphp
        @else
        @php
        $found = false;


        @endphp
        @endif
        @endif
        @php
        $orderIndex_c2d = ($orderIndex_c2d + 1) % $orderCount;
        @endphp
        @endforeach
        

        @foreach ($sub_value as $sub_value_s)
        @php
        $get_deal = $sub_value_s->dealer_creation_id;
        @endphp

        @if ($dealer == $get_deal && $get_item == $sub_value_s->item_creation_id)
        @php
        $jer_balen = $sub_value_s->t_order_qty;
        $cnt_bal += $jer_balen;
       
        @endphp

        @if (!$found)
        @if($sales_det == 'pending')

        <td  class="pending-column" bgcolor="#59e1d5">{{ $jer_balen}}</td>

        @elseif ($sales_det == 'dispatch')

        <td class="dispatched-column">0</td>

        @else

        <td class="dispatched-column">0</td>
        <td  class="pending-column" bgcolor="#59e1d5">{{ $jer_balen}}</td>

        @endif

        @endif

        @php
        $get = true;
        @endphp
        @endif
        @endforeach
        {{-- changed --}}
        @if (!$get && !$found)
        @if($sales_det == 'pending')

        <td class="pending-column">0</td>

            @elseif ($sales_det == 'dispatch')

            <td class="dispatched-column">0</td>

            @else

            <td class="dispatched-column">0</td>
            <td class="pending-column">0</td>

            @endif
        @endif
        @endforeach

        {{-- @foreach($over_all_val as $over)
        @if($sales_order_deli1->dealer_creation_id == $over->dealer_creation_id)
        <td>{{$over->over_al_qty}}</td>
        @endif
        @endforeach --}}
        @if($sales_det == 'pending')

        <td class="pending-column"></td>

            @elseif ($sales_det == 'dispatch')

            <td class="dispatched-column"></td>

            @else

            <td class="dispatched-column"></td>
            <td class="pending-column"></td>

            @endif
    </tr>
    @endforeach
</tbody>

{{-- changed --}}
        <tfoot>
            <tr>
                <td colspan="5"><center>Total:</center></td>
                <?php
                $grandTotalDispatched = 0;
                $grandTotalPending = 0;
                ?>
                @foreach ($item_creation as $item_creations)
                    <?php
                    $totalDispatched = 0;
                    $totalPending = 0;
                    $get_pend_tot = 0;
                    ?>
                    @foreach ($order_delivery as $order_deliverys)
                        @if ($order_deliverys->item_creation_id == $item_creations->id)
                            <?php
                            $totalDispatched += $order_deliverys->sodsc_balance_quantity;
                            $totalPending += ($order_deliverys->soc2ds_order_quantity - $order_deliverys->sodsc_balance_quantity);
                            ?>
                        @endif
                    @endforeach
                    
                     @foreach ($pending_total as $pending_totals)
                        @if ($pending_totals->item_creation_id == $item_creations->id)
                            <?php
                            $get_pend_tot += $pending_totals->total_pending_qty;
                            ?>
                        @endif
                    @endforeach

                    @if($sales_det == 'pending')

                    <td class="pending-column" style="background-color: #cfcfcfad;">{{ $get_pend_tot }}</td>

                    @elseif ($sales_det == 'dispatch')

                    <td class="dispatched-column" style="background-color: #cfcfcfad;"> {{ $totalDispatched }}</td>

                    @else
                    
                    <td class="dispatched-column" style="background-color: #cfcfcfad;"> {{ $totalDispatched }}</td>
                    <td class="pending-column" style="background-color: #cfcfcfad;">{{ $get_pend_tot }}</td>

                    @endif
                    <?php
                    $grandTotalDispatched += $totalDispatched;
                    $grandTotalPending += $get_pend_tot;
                    ?>
                @endforeach
                {{-- <td>{{ $grandTotalDispatched }}</td>
                <td>{{ $grandTotalPending }}</td> --}}
                <!--<td style="background-color: #40c36ead;"> {{ $grandTotalDispatched + $grandTotalPending }}</td>-->
                
                 @if($sales_det == 'pending')

                <td class="pending-column" style="background-color: #40c36ead;"> {{ $grandTotalPending }}</td>

                @elseif ($sales_det == 'dispatch')

                <td class="dispatched-column" style="background-color: #40c36ead;"> {{ $grandTotalDispatched }}</td>

                @else

                <td class="dispatched-column" style="background-color: #40c36ead;"> {{ $grandTotalDispatched }}</td>
                <td class="pending-column" style="background-color: #40c36ead;"> {{ $grandTotalPending }}</td>

                @endif           
                
            </tr>
        </tfoot>
    @else
        <tr>
             <td colspan="3">
                 <center>No Data Available..</center>
             </td>
        </tr>
    @endif
    </table>
</div>



<script>
    function exportToCSV() {
    const table = document.getElementById('tableExport');
    const rows = table.querySelectorAll('tr');
    const csvData = [];

    csvData.push('\uFEFF');
    const headerRow1 = [ '', '', 'SuperLuber', '', '', '', '', '', '', ''];
    csvData.push(headerRow1.join(','));

    const headerRow2 = [ '', '', 'Item Sales Pending Month Wise REPORT', '', '', '', '', '', '', ''];
    csvData.push(headerRow2.join(','));

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const rowData = [];
        const cells = row.querySelectorAll('td, th');

        if (row.parentNode.tagName.toLowerCase() !== 'tfoot') {
        for (let j = 0; j < cells.length; j++) {
            const cellData = cells[j].textContent.trim();
            rowData.push(`"${cellData.replace(/"/g, '""')}"`);
        }

        csvData.push(rowData.join(','));
    }
    }

    // Process tfoot separately
    const tfoot = table.querySelector('tfoot');
    if (tfoot) {
        const tfootData = [];
        const cells = tfoot.querySelectorAll('td, th');

        for (let j = 0; j < cells.length; j++) {
            const cellData = cells[j].textContent.trim();
            tfootData.push(`"${cellData.replace(/"/g, '""')}"`);
        }

        const headerRow2 = ['', '', '', ''];
        csvData.push(headerRow2.concat(tfootData).join(','));
    }

    const csvContent = csvData.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = 'Item Sales Pending Month Wise Report.csv';

    document.body.appendChild(a);
    a.click();

    window.URL.revokeObjectURL(url);
}


</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function generatePDF() {
    const { jsPDF } = window.jspdf;

    // Adjust page size to fit more columns
    var doc = new jsPDF('l', 'mm', [2500, 2050]);

    doc.setFontSize(30);
    doc.text("Item Sales Pending Month Wise Report Report-{{$from_date}}", 15, 10);

    var pdfjs = document.querySelector('#tableExport');

    doc.html(pdfjs, {
        callback: function(doc) {
            doc.save("Item Sales Pending Month Wise Report Report.pdf");
        },
        x: 10,
        y: 50
    });
}
function printTable() {
    const table = document.getElementById('tableExport');
    const printWindow = window.open('', '', 'width=1800,height=900');
    const headerRow1 = ['SuperLuber'];
    const headerRow2 = ['ITEM SALES PENDING MONTH WISE REPORT'];

    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print</title></head><body>');
    printWindow.document.write('<h1>Item Sales Pending Monthwise Reports </h1>');

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- changed --}}
<script>
    $(document).ready(function() {
        $('#sales_det').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue === 'pending') {
                $('.dispatched-column').hide();
                $('.pending-column').show().attr('colspan', '2');
            } else if (selectedValue === 'dispatch') {
                $('.pending-column').hide();
                $('.dispatched-column').show().attr('colspan', '2');
            } else {
                $('.pending-column').show().attr('colspan', '');
                $('.dispatched-column').show().attr('colspan', '');
            }
        });

        $('#sales_det').trigger('change');
    });
</script>




