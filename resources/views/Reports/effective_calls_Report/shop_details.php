<!-- resources/views/view_shop.blade.php -->

<h1>Shop Details</h1>

<table>
    <tr>
        <th>Order Date</th>
        <th>Manager Name</th>
        <th>Sales Ref Name</th>
        <th>Dealer Name</th>
        <th>Area Name</th>
    </tr>
    <tr>
        <td>{{ $shop->order_date }}</td>
        <td>{{ $shop->manager_name }}</td>
        <td>{{ $shop->sales_ref_name }}</td>
        <td>{{ $shop->dealer_name }}</td>
        <td>{{ $shop->area_name }}</td>
    </tr>
</table>

<div>
    <button class="btn btn-secondary" onclick="printShop()">Print</button>
    <button class="btn btn-secondary" onclick="exportShopToCSV()">Export to CSV</button>
    <button class="btn btn-secondary" onclick="generateShopPDF()">Generate PDF</button>
</div>

<script>
    function printShop() {
        // Implement the logic to print the shop details
    }

    function exportShopToCSV() {
        // Implement the logic to export shop details to CSV
    }

    function generateShopPDF() {
        // Implement the logic to generate a PDF for shop details
    }
</script>
