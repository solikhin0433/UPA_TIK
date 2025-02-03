<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\Page;
use App\Models\usermodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    // Menampilkan daftar menu
    public function index()
    {
        $menus = Menu::whereNull('parent_id')
            ->with('children')
            ->orderBy('order_number')
            ->get();

        return response()->json([
            'success' => true,
            'menus' => $menus,
        ], 200);
    }

    // Membuat menu baru
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'slug' => 'required|unique:menus,slug',
            'order_number' => 'required|integer',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'parent_id' => 'nullable|exists:menus,menus_id', // Validasi parent_id

        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        DB::beginTransaction();
    
        try {
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('menu-thumbnails', 'public');
            }
    
            // Membuat halaman baru
            $page = Page::create([
                'title' => $request->name,
                'thumbnail' => $thumbnailPath,
                'content' => $request->content,
                'user_id' => Auth::id(),
            ]);
    
            // Membuat menu baru
            $menu = Menu::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id ?: null, // Pastikan parent_id diterima atau null
                'page_id' => $page->pages_id,
                'order_number' => $request->order_number,
                'slug' => $request->slug,
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan',
                'menu' => $menu,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
    
            // Menghapus thumbnail jika gagal
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    // Menampilkan detail menu berdasarkan ID
    public function show($id)
    {
        try {
            $menu = Menu::with('children')->find($id);
    
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak ditemukan',
                ], 404);
            }
    
            // Sembunyikan kolom 'updated_at' dari menu dan children-nya
            $menu->makeHidden(['updated_at']);
            if ($menu->relationLoaded('children')) {
                $menu->children->each(function ($child) {
                    $child->makeHidden(['updated_at']);
                });
            }
    
            // Ambil data admin (user dengan id = 1)
            $admin = usermodel::find(1); // Jika primary key adalah 'id'

            return response()->json([
                'success' => true,
                'menu' => $menu,
                'admin' => $admin ? [
                    'username' => $admin->id == 1 ? 'admin' : $admin->username,
                ] : null,
            ], 200);
            
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    

    // Mengupdate menu berdasarkan ID
    public function update(Request $request, $id)
    {
        $menu = Menu::with('page')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan',
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'slug' => 'required|unique:menus,slug,' . $id . ',menus_id',
            'order_number' => 'required|integer',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'parent_id' => 'nullable|exists:menus,menus_id',  // Validasi parent_id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $thumbnailPath = $menu->page->thumbnail;

            if ($request->hasFile('thumbnail')) {
                if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $thumbnailPath = $request->file('thumbnail')->store('menu-thumbnails', 'public');
            }

            // Update halaman terkait menu
            $menu->page->update([
                'title' => $request->name,
                'thumbnail' => $thumbnailPath,
                'content' => $request->content,
            ]);

            // Update menu
            $menu->update([
                'name' => $request->name,
                'parent_id' => $request->parent_id ?: null,  // Periksa dan perbarui parent_id
                'order_number' => $request->order_number,
                'slug' => $request->slug,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui',
                'menu' => $menu,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Menghapus menu berdasarkan ID
    public function destroy($id)
    {
        $menu = Menu::with('page')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();

        try {
            if ($menu->page && $menu->page->thumbnail) {
                Storage::disk('public')->delete($menu->page->thumbnail);
            }

            if ($menu->page) {
                $menu->page->delete();
            }

            $menu->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}