<style>
	.main-sidebar .sidebar-menu li ul.dropdown-menu li a.has-dropdown_changed:before {
		content:"";
	}
	.main-sidebar .sidebar-menu li ul.dropdown-menu li a.has-dropdown_changed {
		padding-left:20px;
	}
</style>
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/swal_fun.css') }}">
<li class="menu-header">Main</li>
<?php if(in_array("",$perm_view_details['1'])){ ?>
<li class="dropdown <?php if($cur_pg_uri=="Dashboard"){echo " active";} ?>"><a href="{{ route('Dashboard') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a></li>
<?php } ?>

<!--<li class="dropdown"><a href="{{ route('Gods_View') }}" class="nav-link"><i data-feather="map-pin"></i><span>Gods View(Testing)</span></a></li>-->

<!--<li class="dropdown"><a href="{{ route('Timeline') }}" class="nav-link"><i data-feather="navigation"></i><span>Timeline</span></a></li>-->

<?php if(in_array("",$perm_view_details['2'])){ ?>
<li class="dropdown <?php if(in_array($cur_pg_uri, array("User_Type","User_Creation","User_Screen_Main","User_Screen_Sub","User_Permission"))){echo " active";} ?>">
    <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="user-check"></i><span>Admin</span></a>
    <ul class="dropdown-menu">
        <li <?php if($cur_pg_uri=="User_Type"){echo " class='active'";}echo $perm_view_details['2']['2']; ?>><a class="nav-link" href="{{ route('User_Type') }}">User Type</a></li>
        <li <?php if($cur_pg_uri=="User_Creation"){echo " class='active'";}echo $perm_view_details['2']['3']; ?>><a class="nav-link" href="{{ route('User_Creation') }}">User Creation</a></li>
        <li <?php if($cur_pg_uri=="User_Screen_Main"){echo " class='active'";}echo $perm_view_details['2']['4']; ?>><a class="nav-link" href="{{ route('User_Screen_Main') }}">User Screen Main</a></li>
        <li <?php if($cur_pg_uri=="User_Screen_Sub"){echo " class='active'";}echo $perm_view_details['2']['5']; ?>><a class="nav-link" href="{{ route('User_Screen_Sub') }}">User Screen Sub</a></li>
        <li <?php if($cur_pg_uri=="User_Permission"){echo " class='active'";}echo $perm_view_details['2']['6']; ?>><a class="nav-link" href="{{ route('User_Permission') }}">User Permission</a></li>
    </ul>
</li>
<?php } ?>
<?php if(in_array("",$perm_view_details['3'])): ?>
<li class="dropdown <?php if(in_array($cur_pg_uri, array("Company_Creations","Country_Creation","State_Creation","Machine_Creation","Supplier_Creation","Dealer_Creation","Alert_Creation","Sales_Rep_Creation","Customer_Creation","Employee_Creation","advance_creation","expense_type_creation","Market_Manager_Creation","sub_expense_type_creation","Category_Creation","Group_Creation","Tax_Creation","Item_Liters_Type","Item_Properties_Type","Item_Creation","account_ledger_creation","Working_Days","return_creation"))): ?> active<?php endif; ?>">
    <a href="#" class="has-dropdown has-dropdown_changed">
        <i data-feather="briefcase"></i>
        <span>Masters</span>
    </a>
    <ul class="dropdown-menu">
        <!-- Masters Section Only -->
        <li <?php echo ($cur_pg_uri == "Company_Creations" ? " class='active'" : "") . $perm_view_details['3']['7']; ?>><a href="{{ route('Company_Creations') }}">Company Creation</a></li>
        <li <?php echo ($cur_pg_uri == "Country_Creation" ? " class='active'" : "") . $perm_view_details['3']['8']; ?>><a href="{{ route('Country_Creation') }}">Country Creation</a></li>
        <li <?php echo ($cur_pg_uri == "State_Creation" ? " class='active'" : "") . $perm_view_details['3']['9']; ?>><a href="{{ route('State_Creation') }}">State Creation</a></li>
        <li <?php echo ($cur_pg_uri == "District_Creation" ? " class='active'" : "") . $perm_view_details['3']['10']; ?>><a href="{{ route('Machine_Creation') }}">Machine Entry</a></li>
        <li <?php echo ($cur_pg_uri == "Pump_Creation" ? " class='active'" : "") . $perm_view_details['3']['11']; ?>><a href="{{ route('Pump_Creation') }}">Pump Status</a></li>
        <li <?php echo ($cur_pg_uri == "Customer_Creation" ? " class='active'" : "") . $perm_view_details['3']['25']; ?>><a href="{{ route('Customer_Creation') }}">Customer Creation</a></li>
        <li <?php echo ($cur_pg_uri == "Alert_Creation" ? " class='active'" : "") . $perm_view_details['3']['19']; ?>><a href="{{ route('Alert_Creation') }}">Alert Notification</a></li>
        <li <?php echo ($cur_pg_uri == "Supplier_Creation" ? " class='active'" : "") . $perm_view_details['3']['17']; ?>><a href="{{ route('Supplier_Creation') }}">Supplier Creation</a></li>
    </ul>
</li>
<?php endif; ?>

<?php if(in_array("", array($perm_view_details['3']['18'],$perm_view_details['3']['19'],$perm_view_details['3']['12'],$perm_view_details['3']['25'],$perm_view_details['3']['26']))): ?>
<!-- Add relevant menu items for the second section -->
<?php endif; ?>

<?php if(in_array("", array($perm_view_details['3']['13'],$perm_view_details['3']['14'],$perm_view_details['3']['15'],$perm_view_details['3']['51']))): ?>
<!-- Add relevant menu items for the third section -->
<?php endif; ?>

<?php if(in_array("", array($perm_view_details['3']['20'],$perm_view_details['3']['21'],$perm_view_details['3']['22'],$perm_view_details['3']['48'],$perm_view_details['3']['49'],$perm_view_details['3']['23']))): ?>
<!-- Add relevant menu items for the fourth section -->
<?php endif; ?>

<?php if(in_array("", array($perm_view_details['3']['24'],$perm_view_details['3']['53']))): ?>
<!-- Add relevant menu items for the fifth section -->
<?php endif; ?>

{{-- <li <?php if($cur_pg_uri=="Dealer_Creation"){echo " class='active'";}echo $perm_view_details['3']['18']; ?>><a href="{{ route('Dealer_Creation') }}"><i data-feather="users"></i>Dealer Creation</a></li> --}}

{{-- <?php if(in_array("",$perm_view_details['4'])){ ?>
<li class="dropdown <?php if(in_array($cur_pg_uri, array("Attendance_Entry","Sales_Order-COMPANY_TO_DEALER","Sales_Order-DEALER_TO_SHOP","Sales_Order_Stock","Sales_Order_Delivery","Expense_creations","Receipt_Entry","Sales_Return-DEALER_TO_COMPANY"))){echo " active";} ?>">
    <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="check-square"></i><span>Entry &nbsp; &nbsp; &nbsp; <img src="{{ asset('assets/img/info.png') }}" width="20" height="20" title="Entry Info" onclick="entry_info2()" alt="Info Image"></span></a>
    <ul class="dropdown-menu">
        <?php if(in_array("",array($perm_view_details['4']['27'],$perm_view_details['4']['28'],$perm_view_details['4']['29'],$perm_view_details['4']['50'],$perm_view_details['4']['30'],$perm_view_details['4']['52'],$perm_view_details['4']['33']))){ ?>

		  <li <?php if($cur_pg_uri=="Attendance_Entry"){echo " class='active'";}echo $perm_view_details['4']['27']; ?>><a class="nav-link" href="{{ route('Attendance_Entry') }}">Attendance Entry</a></li>

		  <li <?php if($cur_pg_uri=="Sales_Order_Stock"){echo "class='active'";}echo $perm_view_details['4']['50']; ?>><a class="nav-link" href="{{ route('Sales_Order_Stock') }}">Party Opening Stock</a></li>

		  <li <?php if($cur_pg_uri=="Sales_Order-DEALER_TO_SHOP"){echo "class='active'";}echo $perm_view_details['4']['29']; ?>><a class="nav-link" href="{{ route('Sales_Order_D2S') }}">Secondary Sales</a></li>

		  <li <?php if($cur_pg_uri=="Sales_Order-COMPANY_TO_DEALER"){echo " class='active'";}echo $perm_view_details['4']['28']; ?>><a class="nav-link" href="{{ route('Sales_Order_C2D') }}">Order Recipt</a>
        </li>

		  <li <?php if($cur_pg_uri=="Sales_Order_Delivery"){echo " class='active'";}echo $perm_view_details['4']['30']; ?>><a class="nav-link" href="{{ route('Sales_Order_Delivery') }}">Order Dispatch</a></li>

		  <li <?php if($cur_pg_uri=="Sales_Return-DEALER_TO_COMPANY"){echo " class='active'";}echo $perm_view_details['4']['32']; ?>><a class="nav-link" href="{{ route('Sales_Return_D2C') }}">Return Entry</a></li>

          <li <?php if($cur_pg_uri=="Expense_creations"){echo "class='active'";}echo $perm_view_details['4']['52']; ?>><a class="nav-link" href="{{ route('Expense_Creations') }}">Expense Creation</a></li>

		  <li <?php if($cur_pg_uri=="Receipt_Entry"){echo "class='active'";}echo $perm_view_details['4']['33']; ?>><a class="nav-link" href="{{ route('Receipt_Entry') }}">Receipt Entry</a></li>

		  <li <?php if($cur_pg_uri=="Attendance_Entry"){}echo $perm_view_details['4']['27']; ?>><a class="nav-link" href="{{ route('Order_Target') }}">Order Target</a></li>
        <?php }?>
    </ul>
</li>
<?php }?> --}}
<?php if(in_array("",$perm_view_details['5'])){ ?>
    <li class="dropdown <?php if(in_array($cur_pg_uri, array("Beats_Wise_Report","Sales_Order_Report_c2d","Dealer_Order_Report","Effective_Calls_Report","visit_shop_Report","dealer_sales_report","Item_Sales_pending_Month_Wise_Report","e_c_report","Sales_Box_Report","Receipt_Report","excutive_sales_report","Shop_Report"))){echo " active";} ?>">
    <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="book"></i><span>Reports</span></a>
    <ul class="dropdown-menu">
        <?php if(in_array("",array($perm_view_details['5']['34'],$perm_view_details['5']['35'],$perm_view_details['5']['36'],$perm_view_details['5']['37'],$perm_view_details['5']['39'],$perm_view_details['5']['54'],$perm_view_details['5']['55'],$perm_view_details['5']['59'],$perm_view_details['5']['60']))){ ?>

        <li <?php if($cur_pg_uri=="Beats_Wise_Report"){echo "class='active'";}echo $perm_view_details['5']['34']; ?>><a class="nav-link" href="{{ route('Beats_Wise_Report') }}">Beats Wise Report</a></li>


        <li <?php if($cur_pg_uri=="Expense_Creations_Report"){echo "class='active'";}echo $perm_view_details['5']['34']; ?>><a class="nav-link" href="{{ route('Expense_Creations_Report') }}">Expense Creation Report</a></li>







        <!--<li <?php if($cur_pg_uri=="Sales_Order_Report_c2d"){echo "class='active'";}echo $perm_view_details['5']['35']; ?>><a class="nav-link" href="{{ route('Sales_Order_report_C2D') }}">Sales Order Report (C->D)</a></li>-->


        <li <?php if($cur_pg_uri=="Dealer_Order_Report"){echo "class='active'";}echo $perm_view_details['5']['46']; ?>><a class="nav-link" href="{{ route('d_o_report') }}">Secondary Sales Report</a></li>

        <li <?php if($cur_pg_uri=="excutive_sales_report"){echo " class='active'";}echo $perm_view_details['5']['36']; ?>><a class="nav-link" href="{{ route('excutive_sales_report') }}">Executive Ltrs Sales Report</a></li>

        <li <?php if($cur_pg_uri=="dealer_sales_report"){echo " class='active'";}echo $perm_view_details['5']['39']; ?>><a class="nav-link" href="{{ route('dealer_sales_report_1') }}">Dealer Sales  Report</a></li>



		  <li <?php if($cur_pg_uri=="Item_Sales_pending_Month_Wise_Report"){echo " class='active'";}echo $perm_view_details['5']['54']; ?>><a class="nav-link" href="{{ route('Item_Sales_Pending_Month_Wise_Report') }}">Primary Orders Report</a></li>

          <li <?php if($cur_pg_uri=="Effective_Calls_Report"){echo " class='active'";}echo $perm_view_details['5']['44']; ?>><a class="nav-link" href="{{ route('e_c_report') }}">Effective Calls</a></li>

           <!--<li <?php if($cur_pg_uri=="visit_shop_Report"){echo " class='active'";}echo $perm_view_details['5']['44']; ?>><a class="nav-link" href="{{ route('visit_shop_Report') }}">Visit & Not Visit Shop</a></li>-->

		<li <?php if($cur_pg_uri=="Sales_Box_Report"){echo " class='active'";}echo $perm_view_details['5']['55']; ?>><a class="nav-link" href="{{ route('Sales_Box_Report') }}">Sales Box Report</a></li>

        <li <?php if($cur_pg_uri=="Receipt_Report"){echo " class='active'";}echo $perm_view_details['5']['59']; ?>><a class="nav-link" href="{{ route('receipt_report') }}">Receipt Entry Report</a></li>

        <li <?php if($cur_pg_uri=="Shop_Report"){echo " class='active'";}echo $perm_view_details['5']['60']; ?>><a class="nav-link" href="{{ route('Shop_Report') }}">Shop Report</a></li>

        <?php } ?>



    </ul>
</li>
<?php } ?>


<?php if(in_array("",$perm_view_details['8'])){ ?>

	<li class="dropdown <?php if(in_array($cur_pg_uri, array("Gods_View"))){echo " active";} ?>">
	<li <?php if($cur_pg_uri=="Gods_View"){echo " class='active'";}echo $perm_view_details['8']['62']; ?>><a href="{{ route('Gods_View') }}"><i data-feather="map-pin"></i>Gods View</a></li>

	<?php } ?>

	<?php if(in_array("",$perm_view_details['9'])){ ?>

	<li class="dropdown <?php if(in_array($cur_pg_uri, array("Timeline"))){echo " active";} ?>">
	<li <?php if($cur_pg_uri=="Timeline"){echo " class='active'";}echo $perm_view_details['9']['63']; ?>><a href="{{ route('Timeline') }}"><i data-feather="navigation"></i>Timeline</a></li>

	<?php } ?>

    <?php if(in_array("",$perm_view_details['8'])){ ?>

    <li class="dropdown <?php if(in_array($cur_pg_uri, array("Market_View"))){echo " active";} ?>">
    <li <?php if($cur_pg_uri=="Market_View"){echo " class='active'";}echo $perm_view_details['8']['62']; ?>><a href="{{ route('Market_View') }}"><i data-feather="map"></i>Market View</a></li>

    <?php } ?>


<?php if(in_array("",$perm_view_details['7'])){ ?>

	<li class="dropdown <?php if(in_array($cur_pg_uri, array("Notification_for_Sales_Ref"))){echo " active";} ?>">
	<li <?php if($cur_pg_uri=="Notification_for_Sales_Ref"){echo " class='active'";}echo $perm_view_details['7']['61']; ?>><a href="{{ route('Notification_for_Sales_Ref') }}"><i data-feather="feather"></i>Notification For Sales Ref</a></li>

	<?php } ?>

<script src="{{ asset('assets/js/swal_fun.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
