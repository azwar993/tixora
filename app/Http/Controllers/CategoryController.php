<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan semua category.
     */
    public function index()
    {
        $categories = Category::latest()->get();

        return view('admin.dashboard', compact('categories'));
    }

    /**
     * Menyimpan category baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Category berhasil ditambahkan.');
    }

    /**
     * Mengupdate category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Category berhasil diperbarui.');
    }

    /**
     * Menghapus category.
     */
    public function destroy(Category $category)
    {
        $eventCount = Event::where(
            'category',
            $category->name
        )->count();

        if ($eventCount > 0) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'error',
                    "Category {$category->name} tidak dapat dihapus karena masih digunakan oleh {$eventCount} event."
                );
        }

        $category->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Category berhasil dihapus.');
    }
}