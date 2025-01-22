<style>
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: 128px;
    }

    .green-background {
        background-color: #ADD8E6;
    }
    table thead tr th,
    table thead tr td,
    table tbody tr th,
    table tbody tr td,
    table tfoot tr th,
    table tfoot tr td {
        text-align: center;
    }
</style>
<div style="width: 100%;">
    <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport"
        onclick="exportToCSV()">Excel</button>
    <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport"
        onclick="generatePDF()">PDF</button>
    <button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport"
        onclick="printTable()">Print</button>
</div>
<br>
<link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">

<div class="table-responsive" style="width:100%;overflow-x:auto;">
    <table class="table table-hover" id="tableExport" border="1">
        <thead>
            <tr>
                <th rowspan="2" style="width:50px;border-bottom: 1px solid black;background-color: #cfcfcfad;">S.NO
                </th>
                <th rowspan="2" style="width:150px;border-bottom: 1px solid black;background-color: #cfcfcfad;">Sales
                    Ref Name</th>
                @if ($manager_id != '')
                    @foreach ($group_list as $i1 => $group_list1)
                        @if (count($group_list1['item_list']) > 0)
                            <th colspan="{{ $ch_dispatch == 1 && $ch_pendings == 1 ? 2 : 1 }}"
                                style="border-bottom: 1px solid black;background-color: #fff8a3ad;text-align: center;">
                                {{ $group_list1['group_name'] }}
                            </th>
                        @endif
                    @endforeach
                    @if ($ch_dispatch == 1)
                        <th style="width:200px;border-bottom: 1px solid black;background-color: #cfcfcfad;">
                        </th>
                    @endif
                    @if ($ch_pendings == 1)
                        <th style="width:200px;border-bottom: 1px solid black;background-color: #cfcfcfad;">
                        </th>
                    @endif
                @endif
            </tr>
            <?php $total_values = [];
            $col = 0; ?>
            @if ($manager_id != '')
                <tr>
                    @foreach ($group_list as $i1 => $group_list1)
                        @if (count($group_list1['item_list']) > 0)
                            @if ($ch_dispatch == 1)
                                <th class="dispatched-column"
                                    style="border-bottom: 1px solid black;background-color: #4dffe6ad;">
                                    Dispatched</th>
                                <?php $total_values[$col] = 0;
                                $col++; ?>
                            @endif
                            @if ($ch_pendings == 1)
                                <th class="pending-column"
                                    style="border-bottom: 1px solid black;background-color: #e98ed185;">
                                    Pending</th>
                                <?php $total_values[$col] = 0;
                                $col++; ?>
                            @endif
                        @endif
                    @endforeach
                    @if ($ch_dispatch == 1)
                        <th class="dispatched-column"
                            style="border-bottom: 1px solid black;background-color: #cfcfcfad;">
                            Dispatched Total</th>
                        <?php $total_values[$col] = 0;
                        $col++; ?>
                    @endif
                    @if ($ch_pendings == 1)
                        <th class="pending-column" style="border-bottom: 1px solid black;background-color: #cfcfcfad;">
                            Pending Total</th>
                        <?php $total_values[$col] = 0;
                        $col++; ?>
                    @endif
                </tr>
            @endif
        </thead>

        @if ($manager_id != '')
            <tbody>
                @php
                    $ind1 = 1;
                @endphp
                @foreach ($sales_ref_list as $index1 => $sales_ref_list1)
                    <?php
                    $col_values = [];
                    $col = 0;
                    foreach ($group_list as $i1 => $group_list1) {
                        if (count($group_list1['item_list']) > 0) {
                            if ($ch_dispatch == 1) {
                                $col_values[$col] = 0;
                                $col++;
                            }
                            if ($ch_pendings == 1) {
                                $col_values[$col] = 0;
                                $col++;
                            }
                        }
                    }
                    if ($ch_dispatch == 1) {
                        $col_values[$col] = 0;
                        $col++;
                    }
                    if ($ch_pendings == 1) {
                        $col_values[$col] = 0;
                        $col++;
                    }
                    ?>
                    @foreach ($sales_ref_list1['dealer_list'] as $index2 => $dealer_list1)
                        <?php
                        if ($dealer_list1['dealer_order_list'] != null) {
                            foreach ($dealer_list1['dealer_order_list'] as $key => $dealer_order_list1) {
                                $total_desp = 0;
                                $total_pend = 0;
                                $col = 0;
                                foreach ($group_list as $i1 => $group_list1) {
                                    if (count($group_list1['item_list']) > 0) {
                                        $temp_disp = 0;
                                        $temp_pend = 0;
                                        foreach ($group_list1['item_list'] as $i2 => $item_list1) {
                                            if (isset($dealer_order_list1['all_cnt'][$item_list1['id']])) {
                                                $all_cnt = floatval($dealer_order_list1['all_cnt'][$item_list1['id']]);
                                            } else {
                                                $all_cnt = 0;
                                            }
                                            if (isset($dealer_order_list1['dispatch'][$item_list1['id']])) {
                                                $disp = floatval($dealer_order_list1['dispatch'][$item_list1['id']]);
                                            } else {
                                                $disp = 0;
                                            }
                                            $pend = $all_cnt > $disp ? $all_cnt - $disp : 0;
                                            if ($ch_dispatch == 1) {
                                                $total_desp += $disp;
                                                $temp_disp += $disp;
                                            }
                                            if ($ch_pendings == 1) {
                                                $total_pend += $pend;
                                                $temp_pend += $pend;
                                            }
                                        }
                                        if ($ch_dispatch == 1) {
                                            $total_values[$col] += $temp_disp;
                                            $col_values[$col] += $temp_disp;
                                            $col++;
                                        }
                                        if ($ch_pendings == 1) {
                                            $total_values[$col] += $temp_pend;
                                            $col_values[$col] += $temp_pend;
                                            $col++;
                                        }
                                    }
                                }
                                if ($ch_dispatch == 1) {
                                    $total_values[$col] += $total_desp;
                                    $col_values[$col] += $total_desp;
                                    $col++;
                                }
                                if ($ch_pendings == 1) {
                                    $total_values[$col] += $total_pend;
                                    $col_values[$col] += $total_pend;
                                    $col++;
                                }
                            }
                        } ?>
                    @endforeach
                    <?php
                    $string = '<tr>';
                    $string .= "<td style=\"text-align:left;width:50px;\">$ind1</td>";
                    $string .= "<td style=\"text-align:left;width:150px;\">$sales_ref_list1[sales_ref_name]</td>";
                    $col = 0;
                    foreach ($group_list as $i1 => $group_list1) {
                        if (count($group_list1['item_list']) > 0) {
                            if ($ch_dispatch == 1) {
                                $string .= "<td class=\"dispatched-column\" style=\"border-bottom: 1px solid black;background-color: rgba(77, 255, 231,0.25)\">" . ($col_values[$col] > 0 ? $col_values[$col] : '-') . '</td>';
                                $col++;
                            }
                            if ($ch_pendings == 1) {
                                $string .= "<td class=\"pending-column\" style=\"border-bottom: 1px solid black;background-color: rgba(233, 142, 209,0.25);\">" . ($col_values[$col] > 0 ? $col_values[$col] : '-') . '</td>';
                                $col++;
                            }
                        }
                    }
                    if ($ch_dispatch == 1) {
                        $string .= "<th class=\"dispatched-column\" style=\"border-bottom: 1px solid black;background-color: rgba(207, 207, 207,0.25);\">" . ($col_values[$col] > 0 ? $col_values[$col] : '-') . '</th>';
                        $col++;
                    }
                    if ($ch_pendings == 1) {
                        $string .= "<th class=\"pending-column\" style=\"border-bottom: 1px solid black;background-color: rgba(207, 207, 207,0.25);\">" . ($col_values[$col] > 0 ? $col_values[$col] : '-') . '</th>';
                        $col++;
                    }
                    $string .= '</tr>';
                    echo $string;
                    $ind1++;
                    ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <center>Total:</center>
                    </td>
                    @if ($manager_id != '')
                        <?php $col = 0; ?>
                        @foreach ($group_list as $i1 => $group_list1)
                            @if (count($group_list1['item_list']) > 0)
                                @if ($ch_dispatch == 1)
                                    <th class="dispatched-column"
                                        style="border-bottom: 1px solid black;background-color: #4dffe6ad;">
                                        {{ $total_values[$col] > 0 ? $total_values[$col] : '-' }}</th>
                                    <?php $col++; ?>
                                @endif
                                @if ($ch_pendings == 1)
                                    <th class="pending-column"
                                        style="border-bottom: 1px solid black;background-color: #e98ed185;">
                                        {{ $total_values[$col] > 0 ? $total_values[$col] : '-' }}</th>
                                    <?php $col++; ?>
                                @endif
                            @endif
                        @endforeach
                        @if ($ch_dispatch == 1)
                            <th class="dispatched-column"
                                style="border-bottom: 1px solid black;background-color: #cfcfcfad;">
                                {{ $total_values[$col] > 0 ? $total_values[$col] : '-' }}</th>
                            <?php $col++; ?>
                        @endif
                        @if ($ch_pendings == 1)
                            <th class="pending-column"
                                style="border-bottom: 1px solid black;background-color: #cfcfcfad;">
                                {{ $total_values[$col] > 0 ? $total_values[$col] : '-' }}</th>
                            <?php $col++; ?>
                        @endif
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
        const headerRow1 = ['', '', 'SuperLuber', '', '', '', '', '', '', ''];
        csvData.push(headerRow1.join(','));

        const headerRow2 = ['', '', 'Item Sales Pending Month Wise REPORT', '', '', '', '', '', '', ''];
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
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
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
        const {
            jsPDF
        } = window.jspdf;

        // Adjust page size to fit more columns
        var doc = new jsPDF('l', 'mm', [2500, 2050]);

        doc.setFontSize(30);
        doc.text("Item Sales Pending Month Wise Report Report-{{ $from_date }}", 15, 10);

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
