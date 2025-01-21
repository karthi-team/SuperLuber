<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifications\NotificationSalesRef;
use App\Models\MarketManagerCreation;
use App\Models\SalesRepCreation;
use App\Models\DealerCreation;
use App\Models\MarketCreation;
use App\Models\GroupCreation;
use App\Models\ItemCreation;
use App\Models\ItemLitersType;
use App\Models\ItemPropertiesType;
use App\Models\ShopsType;
use App\Models\ShopCreation;
use App\Models\ExpenseTypeCreation;
use App\Models\SubExpenseTypeCreation;
use App\Models\Entry\SalesOrderStockMain;
use App\Models\Entry\SalesOrderC2DMain;
use App\Models\Entry\SalesOrderD2SMain;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommanApiController extends Controller
{
    public function dashboard_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $dealers_count = DealerCreation::where(function($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->where('sales_rep_id', $sales_executive_id)->count();

        $party_opening_count = SalesOrderStockMain::where(function($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->where('sales_exec', $sales_executive_id)->count();

        $secondary_sales_count = SalesOrderD2SMain::where(function($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->where('sales_exec', $sales_executive_id)->count();

        $primary_order_count = SalesOrderC2DMain::where(function($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })->where('sales_exec', $sales_executive_id)->count();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Dashboard Count Showed Successfully', 'dealers_count' => $dealers_count, 'party_opening_count' => $party_opening_count, 'secondary_sales_count' => $secondary_sales_count, 'primary_order_count' => $primary_order_count], 200);
    }

    public function market_manager_list_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $market_manager_list = MarketManagerCreation::select('market_manager_creation.id as market_manager_id', 'market_manager_creation.manager_name as market_manager_name')
            ->join('sales_ref_creation as src', 'src.manager_id', '=', 'market_manager_creation.id')
            ->where('market_manager_creation.status1', '0')
            ->where('src.id', $sales_executive_id)
            ->where(function ($query) {
                $query->where('market_manager_creation.delete_status', '0')->orWhereNull('market_manager_creation.delete_status');
            })
            ->orderBy('market_manager_creation.manager_name')
            ->get();

        if (!$market_manager_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Market Manager List Showed Successfully', 'market_manager_list' => $market_manager_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Manager List Not Found'], 404);
        }
    }

    public function sales_executive_list_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $sales_executive_list = SalesRepCreation::select('id as sales_executive_id', 'sales_ref_name as sales_executive_name')
        ->where('status', '0')
        ->where('id', $sales_executive_id)
        ->where(function ($query) {
            $query->where('delete_status', '0')->orWhereNull('delete_status');
        })
        ->orderBy('sales_ref_name')
        ->get();

        if (!$sales_executive_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Sales Executive List Showed Successfully', 'market_manager_list' => $sales_executive_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive List Not Found'], 404);
        }
    }

    public function dealer_list_api(Request $request)
    {
        $sales_executive_id = $request->input('sales_executive_id');

        if (empty($sales_executive_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sales Executive Id Not Found'], 404);
        }

        $dealer_list = DealerCreation::select('id as dealer_id','dealer_name')
            ->where('status', '1')
            ->where('sales_rep_id', $sales_executive_id)
            ->where(function($query){
                $query->where('delete_status', '0')->orWhereNull('delete_status');})
            ->orderBy('dealer_name')
            ->get();

        if (!$dealer_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Dealer List Showed Successfully', 'dealer_list' => $dealer_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer List Not Found'], 404);
        }
    }

    public function dealer_address_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $dealer_address = DealerCreation::select('id as dealer_id', 'address as dealer_address')
            ->where('id', $dealer_id)
            ->where('status', '1')
            ->where(function($query){
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->get();

        if (!$dealer_address->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Dealer Address Showed Successfully', 'dealer_address' => $dealer_address], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Address Not Found'], 404);
        }
    }

    public function group_name_list_api(Request $request)
    {
        $group_name_list = GroupCreation::select('id as group_id', 'group_name')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('group_name')
            ->get();

        if (!$group_name_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Group Name List Showed Successfully', 'group_name_list' => $group_name_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Name List Not Found'], 404);
        }
    }

    public function item_name_list_api(Request $request)
    {
        $group_id = $request->input('group_id');

        if (empty($group_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Id Not Found'], 404);
        }

        $item_name_list = ItemCreation::select('id as item_id', 'item_name')
            ->where(function ($query) {
                $query->where('delete_status', '0')->orWhereNull('delete_status');
            })
            ->orderBy('item_name')
            ->where('group_id', '=', $group_id)
            ->get();

        if (!$item_name_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Item Name List Showed Successfully', 'item_name_list' => $item_name_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Name List Not Found'], 404);
        }
    }

    public function item_list_api(Request $request)
    {
        $group_id = $request->input('group_id');

        if (empty($group_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Group Id Not Found'], 404);
        }

        $item_list = ItemCreation::select('item_creation.id as item_id', 'item_creation.item_name', 'item_creation.piece as pieces_count', 'item_creation.distributor_rate as item_price', 'item_creation.id as short_code_id', 'item_creation.short_code', 'item_liters_type.id as item_uom_id','item_liters_type.item_liters_type as item_uom', 'item_properties_type.id as item_packing_type_id','item_properties_type.item_properties_type as item_packing_type')
            ->join('item_liters_type', 'item_liters_type.id', '=', 'item_creation.item_liters_type')
            ->join('item_properties_type', 'item_properties_type.id', '=', 'item_creation.item_properties_type')
            ->where(function ($query) {
                $query->where('item_creation.delete_status', '0')->orWhereNull('item_creation.delete_status');
            })
            ->orderBy('item_creation.item_name')
            ->where('item_creation.group_id', '=', $group_id)
            ->get();

        if (!$item_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Item List Showed Successfully', 'item_list' => $item_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item List Not Found'], 404);
        }
    }

    public function item_short_code_list_api(Request $request)
    {
        $item_id = $request->input('item_id');

        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }

        $item_short_code_list = ItemCreation::select('id as short_code_id', 'short_code')
            ->where('id', $item_id)
            ->get();

        if (!$item_short_code_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Short Code List Showed Successfully', 'item_short_code_list' => $item_short_code_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Short Code List Not Found'], 404);
        }
    }

    public function item_packing_type_list_api(Request $request)
    {
        $item_id = $request->input('item_id');

        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }

        $item_packing_type_list = ItemCreation::select('item_properties_type.id as item_packing_type_id','item_properties_type.item_properties_type as item_packing_type')
            ->join('item_properties_type', 'item_properties_type.id', '=', 'item_creation.item_properties_type')
            ->where('item_creation.id', $item_id)
            ->get();

        if (!$item_packing_type_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Item Packing Type List Showed Successfully', 'item_packing_type_list' => $item_packing_type_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Packing Type List Not Found'], 404);
        }
    }

    public function item_uom_list_api(Request $request)
    {
        $item_id = $request->input('item_id');

        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item Id Not Found'], 404);
        }

        $item_uom_list = ItemCreation::select('item_liters_type.id as item_uom_id','item_liters_type.item_liters_type as item_uom')
            ->join('item_liters_type', 'item_liters_type.id', '=', 'item_creation.item_liters_type')
            ->where('item_creation.id', $item_id)
            ->get();

        if (!$item_uom_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Item UOM Type List Showed Successfully', 'item_uom_list' => $item_uom_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item UOM Type List Not Found'], 404);
        }
    }

    public function market_name_list_api(Request $request)
    {
        $dealer_id = $request->input('dealer_id');

        if (empty($dealer_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Dealer Id Not Found'], 404);
        }

        $get_area_id = DealerCreation::select('id', 'area_id')
        ->where('id', $dealer_id)
        ->where(function ($query) {
            $query->where('delete_status', '0')->where('status', '=', '1')->orWhereNull('delete_status');
        })
        ->orderBy('id')
        ->first();

        if ($get_area_id) {
            $area_ids = explode(',', $get_area_id->area_id);

            $get_area_name = MarketCreation::select('id as market_id', 'area_name as market_name')
                ->whereIn('id', $area_ids)
                ->get();
        }
        else{
            return response()->json(['status' => 'FAILURE', 'message' => 'Area Id Not Found In Dealer Creation'], 404);
        }

        if (!$get_area_name->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Market Name List Showed Successfully', 'market_name_list' => $get_area_name], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Name List Not Found'], 404);
        }
    }

    public function shop_name_list_api(Request $request)
    {
        $market_id = $request->input('market_id');

        if (empty($market_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Id Not Found'], 404);
        }

        $get_shop_name = ShopCreation::select('id as shop_id', 'shop_name')
                ->where('beats_id', $market_id)
                ->get();

        if (!$get_shop_name->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Shop Name List Showed Successfully', 'shop_name_list' => $get_shop_name], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shop Name List Not Found'], 404);
        }
    }

    public function shop_type_list_api(Request $request)
    {
        $shops_type_list = ShopsType::select('id as shops_type_id', 'shops_type as shops_type_name')
            ->where('shops_type', '!=', '')
            ->where('status1', '=', '1')
            ->orderBy('shops_type')
            ->get();

        if (!$shops_type_list->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Shops Type List Showed Successfully', 'shops_type_list' => $shops_type_list], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Shops Type List Not Found'], 404);
        }
    }

    public function item_pieces_prices_api(Request $request)
    {
        $item_id = $request->input('item_id');

        if (empty($item_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Market Id Not Found'], 404);
        }

        $get_item_price = ItemCreation::select('piece as pieces_count', 'distributor_rate as item_price')
                ->where('id', $item_id)
                ->get();

        if (!$get_item_price->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Item List Showed Successfully', 'item_prices_liters_list' => $get_item_price], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Item List Not Found'], 404);
        }
    }

    public function notification_list_api(Request $request)
    {
        $formattedDate = Carbon::now()->format('Y-m-d');
        $before_or_after_login = $request->input('before_or_after_login');

        // Start building the query
        $notification_sales_executive = NotificationSalesRef::select(
            'notification_sales_ref_creation.id as notification_sales_executive_id',
            'notification_sales_ref_creation.group_id',
            'notification_sales_ref_creation.datetime',
            'group_creation.group_name',
            'item_creation.item_name',
            'notification_sales_ref_creation.item_id',
            'notification_sales_ref_creation.description',
            'notification_sales_ref_creation.upd_images'
        )
        ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
        ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
        ->where(function ($query) {
            $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                ->orWhereNull('notification_sales_ref_creation.delete_status');
        });

        // Add the condition based on before_or_after_login
        if ($before_or_after_login != 'all') {
            $notification_sales_executive->where('notification_sales_ref_creation.before_login_or_after_login', $before_or_after_login);
        }

        // Continue building the query
        $notification_sales_executive->where('notification_sales_ref_creation.status', '1')
            ->orderBy('notification_sales_ref_creation.id', 'desc')
            ->limit(5);

        // Fetch the data
        $notification_sales_executive = $notification_sales_executive->get();

        // Process the image URLs
        foreach ($notification_sales_executive as $item) {
            $images = json_decode($item->upd_images); // Decode the JSON array

            if (is_array($images) && !empty($images)) {
                $image_urls = [];
                // Generate image URLs for each image in the array
                foreach ($images as $imageFilename) {
                    $image_urls[] = asset('storage/notification_sales_ref_img/' . $imageFilename);
                }
                $item->image_urls = $image_urls;
            } else {
                // Handle case where images field is empty or not an array
                $item->image_urls = []; // Set as an empty array
            }
        }

        // Return the response
        if (!$notification_sales_executive->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Notification For Sales Executive Showed Successfully', 'notification_sales_executive' => $notification_sales_executive], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Notification For Sales Executive Not Found'], 404);
        }
    }

    public function notification_list_2_api(Request $request)
    {
        $formattedDate = Carbon::now()->format('Y-m-d');

        $notification_sales_executive = NotificationSalesRef::select('notification_sales_ref_creation.id as notification_sales_executive_id', 'notification_sales_ref_creation.group_id', 'notification_sales_ref_creation.datetime', 'group_creation.group_name', 'item_creation.item_name', 'notification_sales_ref_creation.item_id', 'notification_sales_ref_creation.description')
            ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
            ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
            ->whereDate('notification_sales_ref_creation.datetime', $formattedDate)
            ->where(function ($query) {
                $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                      ->orWhereNull('notification_sales_ref_creation.delete_status');
            })
            ->where('notification_sales_ref_creation.status', '1')
            ->orderBy('notification_sales_ref_creation.id', 'desc')
            ->get();

        if (!$notification_sales_executive->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Notification For Sales Executive Showed Successfully', 'notification_sales_executive' => $notification_sales_executive], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Notification For Sales Executive Not Found'], 404);
        }
    }

    public function notification_before_login_api(Request $request)
    {
        $formattedDate = Carbon::now()->format('Y-m-d');

        $notification_sales_executive = NotificationSalesRef::select('notification_sales_ref_creation.id as notification_sales_executive_id', 'notification_sales_ref_creation.group_id', 'notification_sales_ref_creation.datetime', 'group_creation.group_name', 'item_creation.item_name', 'notification_sales_ref_creation.item_id', 'notification_sales_ref_creation.status', 'notification_sales_ref_creation.upd_images as images', 'notification_sales_ref_creation.description')
            ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
            ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
            ->where('notification_sales_ref_creation.before_login_or_after_login', 'before_login')
            ->where('notification_sales_ref_creation.status', '1')
            ->whereDate('notification_sales_ref_creation.datetime', $formattedDate)
            ->where(function ($query) {
                $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                      ->orWhereNull('notification_sales_ref_creation.delete_status');
            })
            ->orderBy('notification_sales_ref_creation.id', 'desc')
            ->limit(1)
            ->get();

        foreach ($notification_sales_executive as $item) {
            $images = json_decode($item->images); // Decode the JSON array

            if (is_array($images) && !empty($images)) {
                $image_urls = [];

                // Generate image URLs for each image in the array
                foreach ($images as $imageFilename) {
                    $imgUrl = asset('storage/notification_sales_ref_img/' . $imageFilename);
                    $image_urls[] = $imgUrl;
                }

                // Assign the array of image URLs to a new property image_urls
                $item->image_urls = $image_urls;
            } else {
                // Handle case where images field is empty or not an array
                $item->image_urls = []; // Or whatever default behavior you want
            }
        }

        if (!$notification_sales_executive->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Notification For Sales Executive Showed Successfully', 'notification_sales_executive' => $notification_sales_executive], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Notification For Sales Executive Not Found'], 404);
        }
    }

    public function notification_after_login_api(Request $request)
    {
        $formattedDate = Carbon::now()->format('Y-m-d');
        $sales_executive_id = $request->input('sales_executive_id');

        $notification_status_main = "No Status Found";

        // Single query to get notifications
        $notification_sales_executive = NotificationSalesRef::select(
            'notification_sales_ref_creation.id as notification_sales_executive_id',
            'notification_sales_ref_creation.group_id',
            'notification_sales_ref_creation.datetime',
            'group_creation.group_name',
            'item_creation.item_name',
            'notification_sales_ref_creation.item_id',
            'notification_sales_ref_creation.status',
            'notification_sales_ref_creation.upd_images as images',
            'notification_sales_ref_creation.description',
            'notification_sales_ref_creation.sales_ref_name as sales_executive_ids',
            'notification_sales_ref_creation.notification_status'
        )
        ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
        ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
        ->where('notification_sales_ref_creation.before_login_or_after_login', 'after_login')
        ->where('notification_sales_ref_creation.status', '1')
        ->whereDate('notification_sales_ref_creation.datetime', $formattedDate)
        ->where(function ($query) {
            $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                ->orWhereNull('notification_sales_ref_creation.delete_status');
        })
        ->orderBy('notification_sales_ref_creation.id', 'desc')
        ->limit(1)
        ->get();

        // Processing the notifications
        if ($notification_sales_executive->isNotEmpty()) {
            foreach ($notification_sales_executive as $item) {
                // Check for sales_executive_id and notification_status
                if (isset($item->sales_executive_ids) && isset($item->notification_status)) {
                    $sales_ref_name_1 = explode(',', $item->sales_executive_ids);
                    $notification_status_1 = explode(',', $item->notification_status);

                    $sales_executive_ids = array_map('trim', $sales_ref_name_1);
                    $index = array_search($sales_executive_id, $sales_executive_ids);

                    if ($index !== false && isset($notification_status_1[$index])) {
                        $notification_status_main = $notification_status_1[$index] == 0 ? "Close" : "Open";
                    }
                }

                // Process image URLs
                $images = json_decode($item->images); // Decode the JSON array

                if (is_array($images) && !empty($images)) {
                    $image_urls = array_map(function($imageFilename) {
                        return asset('storage/notification_sales_ref_img/' . $imageFilename);
                    }, $images);

                    $item->image_urls = $image_urls;
                } else {
                    // Handle case where images field is empty or not an array
                    $item->image_urls = [];
                }
            }
        }

        // Return the response
        if (!$notification_sales_executive->isEmpty()) {
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Notification For Sales Executive Showed Successfully',
                'notification_sales_executive' => $notification_sales_executive,
                'notification_status' => $notification_status_main
            ], 200);
        } else {
            return response()->json([
                'status' => 'FAILURE',
                'message' => 'Notification For Sales Executive Not Found'
            ], 404);
        }
    }

    public function notification_close_after_login_api(Request $request)
    {
        $notification_id = $request->input('notification_sales_executive_id');
        $sales_executive_id = $request->input('sales_executive_id');

        $notification_sales_executive = NotificationSalesRef::select('notification_sales_ref_creation.id as notification_sales_executive_id', 'notification_sales_ref_creation.group_id', 'notification_sales_ref_creation.datetime', 'group_creation.group_name', 'item_creation.item_name', 'notification_sales_ref_creation.item_id', 'notification_sales_ref_creation.status', 'notification_sales_ref_creation.upd_images as images', 'notification_sales_ref_creation.description', 'notification_sales_ref_creation.sales_ref_name', 'notification_sales_ref_creation.notification_status')
            ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
            ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
            ->where('notification_sales_ref_creation.before_login_or_after_login', 'after_login')
            ->where('notification_sales_ref_creation.status', '1')
            ->where('notification_sales_ref_creation.id', $notification_id)
            ->where(function ($query) {
                $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                    ->orWhereNull('notification_sales_ref_creation.delete_status');
            })
            ->first();

        if ($notification_sales_executive) {
            $sales_ref_name = $notification_sales_executive->sales_ref_name;
            $notification_status = $notification_sales_executive->notification_status;

            $sales_ref_name_1 = explode(',', $sales_ref_name);
            $sales_ref_name_count = count($sales_ref_name_1);

            $notification_status_1 = explode(',', $notification_status);

            $notification_status_main = [];

            for ($i = 0; $i < $sales_ref_name_count; $i++) {
                if ($sales_ref_name_1[$i] === $sales_executive_id) {
                    $notification_status_1[$i] = 0;
                }
                array_push($notification_status_main, $notification_status_1[$i]);
            }
            $notification_status_main_str = implode(',', $notification_status_main);

            $tb = NotificationSalesRef::find($notification_id);
            if ($tb) {
                $tb->notification_status = $notification_status_main_str;
                $tb->save();
            }
        }

        $notification_sales_executive = NotificationSalesRef::select('notification_sales_ref_creation.id as notification_sales_executive_id', 'notification_sales_ref_creation.group_id', 'notification_sales_ref_creation.datetime', 'group_creation.group_name', 'item_creation.item_name', 'notification_sales_ref_creation.item_id', 'notification_sales_ref_creation.status', 'notification_sales_ref_creation.upd_images as images', 'notification_sales_ref_creation.description', 'notification_sales_ref_creation.sales_ref_name', 'notification_sales_ref_creation.notification_status')
            ->join('group_creation', 'group_creation.id', '=', 'notification_sales_ref_creation.group_id')
            ->join('item_creation', 'item_creation.id', '=', 'notification_sales_ref_creation.item_id')
            ->where('notification_sales_ref_creation.before_login_or_after_login', 'after_login')
            ->where('notification_sales_ref_creation.status', '1')
            ->where('notification_sales_ref_creation.id', $notification_id)
            ->where(function ($query) {
                $query->where('notification_sales_ref_creation.delete_status', '!=', 1)
                    ->orWhereNull('notification_sales_ref_creation.delete_status');
            })
            ->get();

        if (!$notification_sales_executive->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Notification For Sales Executive Closed Successfully', 'notification_sales_executive' => $notification_sales_executive], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Notification For Sales Executive Not Found'], 404);
        }
    }

    public function expense_creation_api(Request $request)
    {
        $expense_creation=ExpenseTypeCreation::select('id','expense_type')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');})->orderBy('expense_type')->get();

        if (!$expense_creation->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Expense List Showed Successfully', 'expense_creation' => $expense_creation], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense List Not Found'], 404);
        }
    }

    public function sub_expense_creation_api(Request $request)
    {
        $expense_id = $request->input('expense_id');

        if (empty($expense_id)) {
            return response()->json(['status' => 'FAILURE', 'message' => 'Expense Id Not Found'], 404);
        }

        $sub_expense_creation = SubExpenseTypeCreation::select('id', 'sub_expense_type')->where(function($query){$query->where('delete_status', '0')->orWhereNull('delete_status')->where('status', '0');})
        ->where('expense_type', '=', $expense_id)
        ->orderBy('sub_expense_type')
        ->get();

        if (!$sub_expense_creation->isEmpty()) {
            return response()->json(['status' => 'SUCCESS', 'message' => 'Sub Expense List Showed Successfully', 'sub_expense_creation' => $sub_expense_creation], 200);
        } else {
            return response()->json(['status' => 'FAILURE', 'message' => 'Sub Expense List Not Found'], 404);
        }
    }
}
