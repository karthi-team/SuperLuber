<style>
table.dataTable.no-footer {
    border-bottom: 1px solid !important;
}
    </style>


<!--<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport" onclick="exportToCSV()">Excel</button>-->
<!--<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport" onclick="generatePDF()">PDF</button>-->
<!--<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport" onclick="printTable()">Print</button>-->


 <div class="table-responsive"><br>
            <table class="table table-hover" id="tableExport" style="width:100%;" border="1">
        <thead>
            <tr>
            <th colspan="6" ><center>Dealer Sale</center></th>
            </tr>
            <tr style="border-bottom: 1px solid black !important;">
                <th colspan="6"><center>Dealer test - {{$name_deal}} {{$adress}}</center></th>
            </tr>
        <tr>
                <th>Date</th>
                <th>Opening</th>
                <th>Secondary</th>
                <th>Stock Receipt</th>
                <th>Closing</th>
                <th>Dealer Sale</th>
            </tr>
        </thead>
        <tbody>
            
            @php
                $openCount = 0;
                $orderCount = 0;
                $stockCount = 0;
                $closing_value_1 = 0;
                $closing_value_len1 = 0;
                $i1 = 0;
            @endphp

            @foreach ($datt as $index => $dattes)
                @php
                    $openning_date = $dattes->stock_entry_date;
                    $get_sto = 0;
                    $i1++;
                    ${"opening_".$i1} = null; // Initialize the opening variable
                    $totalForRow = 0; // Initialize $totalForRow here
                @endphp

                <tr>
                    <td>{{ $openning_date }}</td>

                    {{-- Opening Value --}}
                    <td id="targetTd">
                        @if ($closing_value_len1 == 0)
                            @foreach ($open as $opens)
                                @if ($openning_date == $opens->open_ent)
                                    {{ $opens->total_opening_stock }}
                                    @php
                                        $openCount++;
                                        $totalForRow += $opens->total_opening_stock;
                                        ${"opening_".$i1} = $opens->total_opening_stock;
                                    @endphp
                                @endif
                            @endforeach
                        @else
                            {{ $closing_value_1 }}
                            @php
                                $openCount++;
                                $totalForRow += $closing_value_1;
                                ${"opening_".$i1} = $closing_value_1;
                            @endphp
                        @endif
                    </td>

                    <td>
                        @php
                            $totalOrderQuantityForRow = 0;
                        @endphp

                        @foreach ($order as $orders)
                            @if ($openning_date == $orders->entry_date)
                                {{ $orders->total_order_quantity }}
                                @php
                                    $orderCount++;
                                    $totalOrderQuantityForRow += $orders->total_order_quantity;
                                @endphp
                            @endif
                        @endforeach
                    </td>

                    <td>
                        @foreach ($open as $opens_1)
                            @if ($openning_date == $opens_1->open_ent)
                                @if ($openCount = 1)
                                    @foreach($dispatch as $dispatchs)
                                        @if ($openning_date == $dispatchs->dispatch_date)
                                            {{ $dispatchs->total_quantity }}
                                            @php
                                                $get_sto = 0 + $dispatchs->total_quantity;
                                            @endphp
                                        @endif
                                    @endforeach
                                @else
                                    @php
                                        echo "0";
                                    @endphp
                                @endif
                                @php
                                    $stockCount++;
                                    $totalForRow += $get_sto;
                                @endphp
                            @endif
                        @endforeach
                    </td>

                    <td id="sourceTd">
                        {{-- Closing Value --}}
                        <input type="hidden" class="closing-value" value="{{ $totalForRow - $totalOrderQuantityForRow }}">
                        {{ $totalForRow - $totalOrderQuantityForRow }}
                        @php
                            $closing_value_1 = ($totalForRow - $totalOrderQuantityForRow);
                            ${"closing_".$i1} = $closing_value_1;
                        @endphp
                        {{-- <input type="text" id="closing_{{ $i1 }}" value="{{ $totalForRow - $totalOrderQuantityForRow }}"> --}}
                    </td>

                    <td>
                        @php
                            $closing_0 = 0;
                        @endphp
                        @if (${"closing_".$i1 - 1} != $closing_0)
                            {{ ${"closing_".$i1 - 1} - ${"opening_".($i1)} ?? 0 }}
                        @else
                            @php
                                echo "0";
                            @endphp
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(function () {
    $('#tableExport').DataTable({
        "dom": 'lBfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                //text: '<i class="far fa-file-pdf"></i>',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5]
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5]
                }
            }
        ]
    });
});
</script>

<!--<script>-->
<!--   function exportToCSV() {-->
<!--    var sales_rep_name = '{{ $rep_name ? $rep_name : "ALL" }}';-->
<!--    var dealer_name = '{{$dea_name ? $dea_name : "ALL" }}';-->
<!--    var manager_name = '{{$mana_name ? $mana_name : "ALL" }}';-->
<!--    var from_date = '{{$from_date}}';-->
<!--    var to_date = '{{$to_date}}';-->
<!--    const table = document.getElementById('tableExport');-->
<!--    const rows = table.querySelectorAll('tr');-->
<!--    const csvData = [];-->
<!--    csvData.push('\uFEFF');-->
<!--    const headerRow1 = ['', '', '', '', '', '', '', 'SuperLuber', '', '', '', '', '', '', ''];-->
<!--    csvData.push(headerRow1.join(','));-->

<!--    const headerRow2 = ['', '', '', '', '', '', '', 'OVERALL OIL SALES', '', '', '', '', '', '', ''];-->
<!--    csvData.push(headerRow2.join(','));-->

<!--    const headerRow3 = ['FROM DATE', from_date, '', 'TO DATE', to_date, '','MANAGER NAME', manager_name ,'','SALES PERSON', sales_rep_name, '', 'DISTRIBUTOR', dealer_name];-->
<!--    csvData.push(headerRow3.join(','));-->

<!--    const headerRow4 = [' '];-->
<!--    csvData.push(headerRow4.join(','));-->

<!--    for (let i = 0; i < rows.length; i++) {-->
<!--        const row = rows[i];-->
<!--        const rowData = [];-->
<!--        const cells = row.querySelectorAll('td, th');-->

<!--        for (let j = 0; j < cells.length; j++) {-->
<!--            const cellData = cells[j].textContent.trim();-->
<!--            rowData.push(`"${cellData.replace(/"/g, '""')}"`);-->
<!--        }-->

<!--        csvData.push(rowData.join(','));-->
<!--    }-->

<!--    const csvContent = csvData.join('\n');-->
<!--    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });-->
<!--    const url = window.URL.createObjectURL(blob);-->

<!--    const a = document.createElement('a');-->
<!--    a.style.display = 'none';-->
<!--    a.href = url;-->
<!--    a.download = 'Effective_Calls_Reports.csv';-->

<!--    document.body.appendChild(a);-->
<!--    a.click();-->

<!--    window.URL.revokeObjectURL(url);-->
<!--}-->
<!--    </script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>-->

<!--<script>-->
<!--    function generatePDF() {-->
<!--    const { jsPDF } = window.jspdf;-->

<!--    var doc = new jsPDF('l', 'mm', [1500, 1350]);-->
<!--    var pdfjs = document.querySelector('#tableExport');-->

<!--    doc.setFontSize(30);-->
<!--    doc.text("Dealer Orders Report-{{$from_date}} to {{$to_date}}", 15, 10);-->

<!--    doc.html(pdfjs, {-->
<!--        callback: function(doc) {-->
<!--            doc.save("Dealer_Orders.pdf");-->
<!--        },-->
<!--        x: 15,-->
<!--        y: 20-->
<!--    });-->
<!--}-->
<!--function printTable() {-->
<!--    const table = document.getElementById('tableExport');-->
<!--    const printWindow = window.open('', '', 'width=1500,height=900');-->

<!--    printWindow.document.open();-->
<!--    printWindow.document.write('<html><head><title>Print</title></head><body>');-->
<!--    printWindow.document.write('<h1>Dealor Orders [{{$from_date}} to {{$to_date}}]</h1>');-->
<!--    printWindow.document.write(table.outerHTML);-->
<!--    printWindow.document.write('</body></html>');-->

<!--    printWindow.document.close();-->
<!--    printWindow.print();-->
<!--    printWindow.close();-->
<!--}-->
<!--</script>-->
