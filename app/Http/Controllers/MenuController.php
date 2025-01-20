<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;

class MenuController extends Controller
{
    // Menampilkan halaman utama manajemen menu
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Menu',
            'list' => ['Home', 'Menu'],
        ];

        $page = (object) [
            'title' => 'Manajemen Menu Sistem'
        ];

        $activeMenu = 'menu';
        $menus = Menu::with('children')->whereNull('parent_id')->get();

        return view('admin.menu.index', compact('breadcrumb', 'page', 'activeMenu', 'menus'));
    }

    // Menampilkan data menu dalam format JSON untuk DataTables
    public function list(Request $request)
    {
        $menus = Menu::with('parent');

        return DataTables::of($menus)
            ->addIndexColumn()
            ->addColumn('aksi', function ($menu) {
                $btn  = '<button onclick="modalAction(\'' . url('/menu/' . $menu->menus_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/menu/' . $menu->menus_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/menu/' . $menu->menus_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus',
            'parent_id' => 'nullable|exists:menus,menus_id',
            'order_number' => 'required|integer',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Create menu
            $menu = Menu::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'order_number' => $request->order_number
            ]);

            // Create page
            $page = new Page();
            $page->title = $request->name;
            $page->content = $request->content;
            $page->menus_id = $menu->menus_id;
            $page->user_id = auth()->id();

            // Handle thumbnail if uploaded
            if ($request->hasFile('thumbnail')) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('thumbnail'));
                $image->encode(new WebpEncoder(75));
                $imageName = time() . '_' . $request->file('thumbnail')->getClientOriginalName() . '.webp';
                
                // Save the image
                $image->save(storage_path('app/public/thumbnails/' . $imageName));
                $page->thumbnail = 'thumbnails/' . $imageName;
            }

            $page->save();

            // Update menu with page_id
            $menu->page_id = $page->pages_id;
            $menu->save();

            DB::commit();
            return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function update(Request $request, $menu_id)
    {
        $menu = Menu::findOrFail($menu_id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug,' . $menu_id . ',menus_id',
            'parent_id' => 'nullable|exists:menus,menus_id',
            'order_number' => 'required|integer',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Update menu
            $menu->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'order_number' => $request->order_number
            ]);

            // Update or create page
            $page = Page::where('menus_id', $menu->menus_id)->first();
            if (!$page) {
                $page = new Page();
                $page->menus_id = $menu->menus_id;
                $page->user_id = auth()->id();
            }

            $page->title = $request->name;
            $page->content = $request->content;

            // Handle thumbnail update
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($page->thumbnail && Storage::disk('public')->exists($page->thumbnail)) {
                    Storage::disk('public')->delete($page->thumbnail);
                }

                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('thumbnail'));
                $image->encode(new WebpEncoder(75));
                $imageName = time() . '_' . $request->file('thumbnail')->getClientOriginalName() . '.webp';
                
                // Save the new image
                $image->save(storage_path('app/public/thumbnails/' . $imageName));
                $page->thumbnail = 'thumbnails/' . $imageName;
            }

            $page->save();

            // Update menu with page_id if not set
            if (!$menu->page_id) {
                $menu->page_id = $page->pages_id;
                $menu->save();
            }

            DB::commit();
            return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function edit($menu_id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Menu',
            'list' => ['Home', 'Menu', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Menu'
        ];

        $menu = Menu::with('page')->findOrFail($menu_id);
        $parents = Menu::whereNull('parent_id')->where('menus_id', '!=', $menu_id)->get();
        $activeMenu = 'menu';

        return view('admin.menu.edit', compact('breadcrumb', 'page', 'menu', 'parents', 'activeMenu'));
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Menu',
            'list' => ['Home', 'Menu', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Menu'
        ];

        $parents = Menu::whereNull('parent_id')->get();
        $activeMenu = 'menu';

        return view('admin.menu.create', compact('breadcrumb', 'page', 'parents', 'activeMenu'));
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('upload'));
            $image->encode(new WebpEncoder(75));
            $imageName = time() . '_' . $request->file('upload')->getClientOriginalName() . '.webp';
            
            // Save the image
            $image->save(storage_path('app/public/uploads/' . $imageName));
            
            $url = asset('storage/uploads/' . $imageName);
            return response()->json(['url' => $url]);
        }
        return response()->json(['error' => 'No image uploaded.'], 400);
    
    }
  

    // Menghapus menu
    public function destroy(string $menu_id)
    {
        $menu = Menu::findOrFail($menu_id);

        if ($menu->children()->count() > 0) {
            return redirect('/menu')->with('error', 'Menu memiliki submenu, harap hapus terlebih dahulu.');
        }

        if ($menu->content_id) {
            $menu->content()->delete();
        }

        $menu->delete();

        return redirect('/menu')->with('success', 'Menu berhasil dihapus.');
    }
}