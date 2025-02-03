<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DaftarMenuController extends Controller
{
    // Get list of main menus
    public function index(): JsonResponse
    {
        $menus = Menu::whereNull('parent_id')->get(); // Get main menus

        return response()->json([
            'success' => true,
            'message' => 'List of main menus',
            'data' => $menus
        ]);
    }

    // Get sub-menus for a specific parent menu
    public function subMenu(int $id): JsonResponse
    {
        $parentMenu = Menu::find($id);

        if (!$parentMenu) {
            return response()->json([
                'success' => false,
                'message' => 'Parent menu not found',
                'data' => null
            ], 404);
        }

        $subMenus = Menu::where('parent_id', $id)->with('page')->get(); // Include related page

        return response()->json([
            'success' => true,
            'message' => "List of sub-menus for {$parentMenu->name}",
            'data' => [
                'parentMenu' => $parentMenu,
                'subMenus' => $subMenus
            ]
        ]);
    }

    // Get content for a specific page
    public function content(int $pageId): JsonResponse
    {
        $page = DB::table('pages')->where('pages_id', $pageId)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Page content retrieved successfully',
            'data' => $page
        ]);
    }
}