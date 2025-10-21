<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        // ฝั่งแอดมิน: เอาสด + แบ่งหน้า
        $categories = Category::orderBy('name')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:categories,name',
            'slug' => 'nullable|string|max:160|unique:categories,slug',
        ]);
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }
        Category::create($validated);

        Cache::forget('homepage.categories'); 

        // ถ้าคุณรวม UI ที่หน้าแดชบอร์ด ให้เด้งกลับแดชบอร์ดแทน
        return redirect()->route('admin.dashboard')->with('success', 'เพิ่มหมวดหมู่แล้ว');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:160|unique:categories,slug,' . $category->id,
        ]);
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }
        $category->update($validated);

        Cache::forget('homepage.categories');

        return redirect()->route('admin.dashboard')->with('success', 'แก้ไขหมวดหมู่แล้ว');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        Cache::forget('admin.dashboard');

        return back()->with('success', 'ลบหมวดหมู่แล้ว');
    }
}
