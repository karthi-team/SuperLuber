<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport" onclick="exportToCSV()">Excel</button>
<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport" onclick="generatePDF()">PDF</button>
<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport" onclick="printTable()">Print</button>


<style>
    .green-background {
        background-color: #ADD8E6;
        /* Optional: To ensure text is readable on the green background */
    }
</style>

<br><br>
<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;" border="1">


        <tbody>
            <tr>
                <td colspan="16">
                    <h6><b>New Visitor Table</b></h6>
                </td>
            </tr>
            @if(count($sales_exeIdArrays1) > 0)
                <tr>
                    <th>S.No</th>
                    <th>Sales Ref Name</th>
                    <th>New Visitor Entry Date: Visitor Count Per Day</th>
                </tr>
                <?php $vk = 1; ?>
                <?php $foundAnyData = false; ?>
                @foreach($sales_exeIdArrays1 as $sales_exeIdArrays1s)
                    <?php $foundData = false; ?>
                    @foreach($arr2 as $arr2s)
                        @if($arr2s['sales_exec1_t'] == $sales_exeIdArrays1s->sales_rep_id)
                            <tr>
                                <td>{{ $vk++ }}</td>
                                <td>{{ $sales_exeIdArrays1s->sales_ref_name }}</td>
                                <td>
                                    <?php $total_count = 0; ?>
                                    @foreach($arr2 as $arr2s)
                                        @if($arr2s['sales_exec1_t'] == $sales_exeIdArrays1s->sales_rep_id && $arr2s['total_status_count_2_t'] != '0')
                                            {{ $arr2s['entry_date'] }} : {{ $arr2s['total_status_count_2_t'] }}
                                            <?php $total_count += $arr2s['total_status_count_2_t']; ?>
                                            <br>
                                        @endif
                                    @endforeach
                                    <b>&emsp;&emsp;Total Count&nbsp;: {{ $total_count }}</b>
                                </td>
                            </tr>
                            <?php $foundData = true; $foundAnyData = true; ?>
                            @break
                        @endif
                    @endforeach
                    @if (!$foundData)
                        <?php $foundAnyData = true; ?>
                    @endif
                @endforeach
            @endif
            @if (!$foundAnyData)
                <tr>
                    <td colspan="3">
                        <h3><center>No Data Available..</center></h3>
                    </td>
                </tr>
            @endif
        </tbody>
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
