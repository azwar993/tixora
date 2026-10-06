<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $articles = Article::query()
            ->when(
                in_array($status, ['draft', 'published'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $slug = $this->inputSlug($request, $request->input('title', ''));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|string|max:255',
            'status' => 'sometimes|in:draft,published',
            'published_at' => 'nullable|date',
        ]);
        $validated['slug'] = $slug;

        validator(
            ['slug' => $slug],
            ['slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')]]
        )->validate();

        $validated['status'] ??= 'draft';
        $validated['published_at'] = $validated['status'] === 'published'
            ? ($validated['published_at'] ?? now())
            : null;
        $validated['created_by'] = Auth::id();

        Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Article berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $slug = $this->inputSlug($request, $request->input('title', ''), $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|string|max:255',
            'status' => 'sometimes|in:draft,published',
            'published_at' => 'nullable|date',
        ]);
        $validated['slug'] = $slug;

        validator(
            ['slug' => $slug],
            [
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('articles', 'slug')->ignore($article->id),
                ],
            ]
        )->validate();

        $status = $validated['status'] ?? $article->status;
        $validated['status'] = $status;
        $validated['published_at'] = $status === 'published'
            ? ($validated['published_at'] ?? $article->published_at ?? now())
            : null;

        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Article berhasil diperbarui.');
    }

    public function publish(Article $article)
    {
        $article->status = 'published';
        $article->published_at ??= now();
        $article->save();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Article berhasil dipublikasikan.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Article berhasil dihapus.');
    }

    public function publicIndex()
    {
        $articles = Article::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        if ($article->status !== 'published') {
            abort(404);
        }

        return view('articles.show', compact('article'));
    }

    private function inputSlug(Request $request, string $title, ?Article $article = null): string
    {
        $slug = Str::slug((string) $request->input('slug', ''));

        if ($slug === '') {
            $baseSlug = Str::slug($title) ?: 'article';
            $slug = $baseSlug;
            $suffix = 2;

            while (Article::query()
                ->where('slug', $slug)
                ->when($article, fn ($query) => $query->where('id', '!=', $article->getKey()))
                ->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
        }

        return $slug;
    }
}
