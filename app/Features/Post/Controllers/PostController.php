<?php

namespace App\Features\Post\Controllers;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Post\Enums\PostCategory;
use App\Features\Post\Models\Post;
use App\Features\Post\Services\PostExcelService;
use App\Features\Post\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController
{
    public function __construct(
        private readonly PostService $posts,
        private readonly PostExcelService $excel,
        private readonly AiClientInterface $ai,
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $filters = $request->only(['search', 'category', 'status']);

        return Inertia::render('Post/Index', [
            'posts' => $this->posts->listForUser($user, $filters),
            'stats' => $this->posts->statsForUser($user),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'maxAi' => (int) config('openai.max_articles'),
        ]);
    }

    public function show(Post $post): Response
    {
        return Inertia::render('Post/Show', ['post' => $post]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $this->posts->create($request->user(), $data, $request->file('cover'));

        return back()->with('success', 'Artikel berhasil dibuat.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $this->posts->update($post, $data, $request->file('cover'));

        return back()->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->posts->delete($post);

        return to_route('posts.index')->with('success', 'Artikel berhasil dihapus.');
    }

    public function publish(Post $post): RedirectResponse
    {
        $post = $this->posts->togglePublish($post);

        return back()->with('success', $post->is_published ? 'Artikel dipublikasikan.' : 'Artikel dijadikan draf.');
    }

    public function uploadCover(Request $request, Post $post): RedirectResponse
    {
        $request->validate(['cover' => ['required', 'image', 'max:2048']]);
        $this->posts->setCover($post, $request->file('cover'));

        return back()->with('success', 'Foto sampul berhasil diunggah.');
    }

    public function removeCover(Post $post): RedirectResponse
    {
        $this->posts->removeCover($post);

        return back()->with('success', 'Foto sampul berhasil dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $binary = $this->excel->export($this->posts->allForUser($request->user()));

        return $this->download($binary, 'artikel-'.$request->user()->username.'.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120']]);

        $count = $this->excel->import($request->user(), $request->file('file')->getRealPath());

        return back()->with('success', "{$count} artikel berhasil diimpor.");
    }

    public function aiGenerate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic' => ['required', 'string', 'max:200'],
            'count' => ['required', 'integer', 'min:1', 'max:'.config('openai.max_articles')],
        ]);

        try {
            $items = array_map(
                fn ($item) => $item->toArray(),
                $this->ai->generate($data['topic'], (int) $data['count'])
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('ai_items', $items)->with('success', count($items).' draf artikel dihasilkan.');
    }

    public function aiSave(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:'.config('openai.max_articles')],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.category' => ['nullable', Rule::in(PostCategory::values())],
            'items.*.excerpt' => ['nullable', 'string', 'max:500'],
            'items.*.content' => ['required', 'string'],
        ]);

        $count = $this->posts->createMany($request->user(), $data['items']);

        return back()->with('success', "{$count} artikel AI disimpan sebagai draf.");
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(PostCategory::values())],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ];
    }

    private function download(string $binary, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($binary) {
            echo $binary;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
