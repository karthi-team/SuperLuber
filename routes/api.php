<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login
use App\Http\Controllers\Api\LoginApiController;

// Comman
use App\Http\Controllers\Api\CommanApiController;

// Dealer Creation
use App\Http\Controllers\Api\DealerCreationApiController;

// Party Opening Stock
use App\Http\Controllers\Api\PartyOpeningStockApiController;

// Secondary Sales
use App\Http\Controllers\Api\SecondarySalesApiController;

// Primary Ordere Sales
use App\Http\Controllers\Api\PrimaryOrderSalesApiController;

// Expense Creation
use App\Http\Controllers\Api\ExpenseEntryApiController;

// Sales Executive
use App\Http\Controllers\Api\SalesExecutiveApiController;

// Report
use App\Http\Controllers\Api\DailySalesReportApiController;
use App\Http\Controllers\Api\OrderTargetReportApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Login
Route::post('login_application', [LoginApiController::class, 'login_application_api']);
Route::post('logout_application', [LoginApiController::class, 'logout_application_api']);
Route::post('forgot_password_application', [LoginApiController::class, 'forgot_password_application_api']);

// Location Update
Route::post('update_current_location', [LoginApiController::class, 'update_current_location_api']);

// Comman
Route::post('dashboard', [CommanApiController::class, 'dashboard_api']);
Route::post('notification_list', [CommanApiController::class, 'notification_list_api']);
Route::post('notification_before_login', [CommanApiController::class, 'notification_before_login_api']);
Route::post('notification_after_login', [CommanApiController::class, 'notification_after_login_api']);
Route::post('notification_close_after_login', [CommanApiController::class, 'notification_close_after_login_api']);
Route::post('market_manager_list', [CommanApiController::class, 'market_manager_list_api']);
Route::post('sales_executive_list', [CommanApiController::class, 'sales_executive_list_api']);
Route::post('dealer_list', [CommanApiController::class, 'dealer_list_api']);
Route::post('dealer_address', [CommanApiController::class, 'dealer_address_api']);
Route::post('group_name_list', [CommanApiController::class, 'group_name_list_api']);
Route::post('item_list', [CommanApiController::class, 'item_list_api']);
Route::post('item_name_list', [CommanApiController::class, 'item_name_list_api']);
Route::post('item_short_code_list', [CommanApiController::class, 'item_short_code_list_api']);
Route::post('item_packing_type_list', [CommanApiController::class, 'item_packing_type_list_api']);
Route::post('item_uom_list', [CommanApiController::class, 'item_uom_list_api']);
Route::post('market_name_list', [CommanApiController::class, 'market_name_list_api']);
Route::post('shop_name_list', [CommanApiController::class, 'shop_name_list_api']);
Route::post('shop_type_list', [CommanApiController::class, 'shop_type_list_api']);
Route::post('item_pieces_and_prices', [CommanApiController::class, 'item_pieces_prices_api']);
Route::post('expense_creation', [CommanApiController::class, 'expense_creation_api']);
Route::post('sub_expense_creation', [CommanApiController::class, 'sub_expense_creation_api']);

// Dealer Creation
Route::post('sales_executive_assigned_dealers', [DealerCreationApiController::class, 'sales_executive_assigned_dealers_api']);
Route::post('dealer_assigned_market', [DealerCreationApiController::class, 'dealer_assigned_market_api']);
Route::post('shops_list', [DealerCreationApiController::class, 'shops_list_api']);
Route::post('shops_insert', [DealerCreationApiController::class, 'shops_insert_api']);
Route::post('shops_edit', [DealerCreationApiController::class, 'shops_edit_api']);
Route::post('shops_update', [DealerCreationApiController::class, 'shops_update_api']);
Route::post('shops_location_find', [DealerCreationApiController::class, 'shops_location_find_api']);

// Party Opening Stock
Route::post('party_opening_stock_number_list', [PartyOpeningStockApiController::class, 'party_opening_stock_number_list_api']);
Route::post('party_opening_stock_list', [PartyOpeningStockApiController::class, 'party_opening_stock_list_api']);
Route::post('party_opening_stock_main_stock_number', [PartyOpeningStockApiController::class, 'party_opening_stock_main_stock_number_api']);
Route::post('party_opening_stock_sublist_stock_count', [PartyOpeningStockApiController::class, 'party_opening_stock_sublist_stock_count_api']);
Route::post('party_opening_stock_sublist_pieces_count', [PartyOpeningStockApiController::class, 'party_opening_stock_sublist_pieces_count_api']);
Route::post('party_opening_stock_main_insert', [PartyOpeningStockApiController::class, 'party_opening_stock_main_insert_api']);
Route::post('party_opening_stock_main_update', [PartyOpeningStockApiController::class, 'party_opening_stock_main_update_api']);
Route::post('party_opening_stock_sublist_insert', [PartyOpeningStockApiController::class, 'party_opening_stock_sublist_insert_api']);
Route::post('party_opening_stock_sublist_update', [PartyOpeningStockApiController::class, 'party_opening_stock_sublist_update_api']);
Route::post('party_opening_stock_form_main', [PartyOpeningStockApiController::class, 'party_opening_stock_form_main_api']);
Route::post('party_opening_stock_form_sublist', [PartyOpeningStockApiController::class, 'party_opening_stock_form_sublist_api']);
Route::post('party_opening_stock_form_sublist_edit', [PartyOpeningStockApiController::class, 'party_opening_stock_form_sublist_edit_api']);
Route::post('party_opening_item_list', [PartyOpeningStockApiController::class, 'party_opening_item_list_api']);
Route::post('party_opening_sublist_delete', [PartyOpeningStockApiController::class, 'party_opening_sublist_delete_api']);
Route::post('party_opening_main_delete', [PartyOpeningStockApiController::class, 'party_opening_main_delete_api']);
Route::post('party_opening_stock_form_sublist_pdf', [PartyOpeningStockApiController::class, 'party_opening_stock_form_sublist_pdf_api']);

// Secondary Sales
Route::post('seconadary_sales_order_number_list', [SecondarySalesApiController::class, 'seconadary_sales_order_number_list_api']);
Route::post('seconadary_sales_list', [SecondarySalesApiController::class, 'seconadary_sales_list_api']);
Route::post('seconadary_sales_main_order_number', [SecondarySalesApiController::class, 'seconadary_sales_main_order_number_api']);
Route::post('seconadary_sales_current_stock', [SecondarySalesApiController::class, 'seconadary_sales_stock_api']);
Route::post('seconadary_sales_main_insert', [SecondarySalesApiController::class, 'seconadary_sales_main_insert_api']);
Route::post('seconadary_sales_sublist_insert', [SecondarySalesApiController::class, 'seconadary_sales_sublist_insert_api']);
Route::post('seconadary_sales_sublist_insert_2', [SecondarySalesApiController::class, 'seconadary_sales_sublist_insert_api_2']);
Route::post('secondary_sales_form_main', [SecondarySalesApiController::class, 'secondary_sales_form_main_api']);
Route::post('secondary_sales_form_sublist', [SecondarySalesApiController::class, 'secondary_sales_form_sublist_api']);
Route::post('secondary_sales_form_sublist_pdf', [SecondarySalesApiController::class, 'secondary_sales_form_sublist_pdf_api']);
Route::post('secondary_sales_form_sublist_1', [SecondarySalesApiController::class, 'secondary_sales_form_sublist_1_api']);
Route::post('secondary_sales_sublist_edit', [SecondarySalesApiController::class, 'secondary_sales_sublist_edit_api']);
Route::post('secondary_sales_sublist_update', [SecondarySalesApiController::class, 'secondary_sales_sublist_update_api']);
Route::post('seconadary_sales_main_update', [SecondarySalesApiController::class, 'seconadary_sales_main_update_api']);
Route::post('seconadary_sales_visitor_insert', [SecondarySalesApiController::class, 'seconadary_sales_visitor_insert_api']);
Route::post('seconadary_sales_visitor_sublist', [SecondarySalesApiController::class, 'seconadary_sales_visitor_sublist_api']);
Route::post('seconadary_sales_visitor_edit', [SecondarySalesApiController::class, 'seconadary_sales_visitor_edit_api']);
Route::post('seconadary_sales_visitor_update', [SecondarySalesApiController::class, 'seconadary_sales_visitor_update_api']);
Route::post('seconadary_sales_goto_visitor', [SecondarySalesApiController::class, 'seconadary_sales_goto_visitor_api']);
Route::post('attendence_report', [SecondarySalesApiController::class, 'attendence_report_api']);
Route::post('seconadary_sales_main_order_number_vistor', [SecondarySalesApiController::class, 'seconadary_sales_main_order_number_vistor_api']);

// Primary Order Sales
Route::post('primary_sales_main_order_number', [PrimaryOrderSalesApiController::class, 'primary_sales_main_order_number_api']);
Route::post('primary_sales_main_insert', [PrimaryOrderSalesApiController::class, 'primary_sales_main_insert_api']);
Route::post('primary_sales_sublist_insert', [PrimaryOrderSalesApiController::class, 'primary_sales_sublist_insert_api']);
Route::post('primary_sales_form_main', [PrimaryOrderSalesApiController::class, 'primary_sales_form_main_api']);
Route::post('primary_sales_form_sublist', [PrimaryOrderSalesApiController::class, 'primary_sales_form_sublist_api']);
Route::post('primary_sales_sublist_edit', [PrimaryOrderSalesApiController::class, 'primary_sales_sublist_edit_api']);
Route::post('primary_sales_sublist_update', [PrimaryOrderSalesApiController::class, 'primary_sales_sublist_update_api']);
Route::post('primary_sales_main_update', [PrimaryOrderSalesApiController::class, 'primary_sales_main_update_api']);
Route::post('primary_sales_order_number_list', [PrimaryOrderSalesApiController::class, 'primary_sales_order_number_list_api']);
Route::post('primary_sales_list', [PrimaryOrderSalesApiController::class, 'primary_sales_list_api']);
Route::post('primary_sales_sublist_delete', [PrimaryOrderSalesApiController::class, 'primary_sales_sublist_delete_api']);
Route::post('primary_sales_main_delete', [PrimaryOrderSalesApiController::class, 'primary_sales_main_delete_api']);
Route::post('primary_sales_form_sublist_pdf', [PrimaryOrderSalesApiController::class, 'primary_sales_form_sublist_pdf_api']);

//  Expense Creation
Route::post('expense_order_number', [ExpenseEntryApiController::class, 'expense_order_number_api']);
Route::post('expense_get_sales_executive', [ExpenseEntryApiController::class, 'expense_get_sales_executive_api']);
Route::post('expense_main_insert', [ExpenseEntryApiController::class, 'expense_main_insert_api']);
Route::post('expense_sublist_insert', [ExpenseEntryApiController::class, 'expense_sublist_insert_api']);
Route::post('expense_get_dealer', [ExpenseEntryApiController::class, 'expense_get_dealer_api']);
Route::post('expense_get_visitor', [ExpenseEntryApiController::class, 'expense_get_visitor_api']);
Route::post('expense_main_edit', [ExpenseEntryApiController::class, 'expense_main_edit_api']);
Route::post('expense_sublist', [ExpenseEntryApiController::class, 'expense_sublist_api']);
Route::post('expense_sublist_edit', [ExpenseEntryApiController::class, 'expense_sublist_edit_api']);
Route::post('expense_sublist_update', [ExpenseEntryApiController::class, 'expense_sublist_update_api']);
Route::post('expense_main_list_update', [ExpenseEntryApiController::class, 'expense_main_list_update_api']);
Route::post('expense_order_number_list', [ExpenseEntryApiController::class, 'expense_order_number_list_api']);
Route::post('expense_list', [ExpenseEntryApiController::class, 'expense_list_api']);

//  Sales Executive Profile

Route::post('sales_exe_profile', [SalesExecutiveApiController::class, 'sales_exe_profile_api']);
Route::post('sales_exe_profile_update', [SalesExecutiveApiController::class, 'sales_exe_profile_update_api']);

// Report
Route::post('daily_sales_report', [DailySalesReportApiController::class, 'daily_sales_report_api']);
Route::post('daily_sales_report_pdf', [DailySalesReportApiController::class, 'daily_sales_report_pdf_api']);
Route::post('daily_sales_report_shop_pdf', [DailySalesReportApiController::class, 'daily_sales_report_shop_pdf_api']);
Route::post('daily_sales_report_dealer_wise_market', [DailySalesReportApiController::class, 'daily_sales_report_dealer_wise_market_api']);
Route::post('daily_sales_report_date_wise_dealer', [DailySalesReportApiController::class, 'daily_sales_report_date_wise_dealer_api']);

Route::post('order_target_report', [OrderTargetReportApiController::class, 'order_target_report_api']);
