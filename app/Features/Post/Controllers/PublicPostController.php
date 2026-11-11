<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPostController
{
    public function __construct(private readonly PostService $posts) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category']);

        return Inertia::render('Public/PostIndex', [
            'posts' => $this->posts->listPublished($filters),
            'filters' => ['search' => $filters['search'] ?? '', 'category' => $filters['category'] ?? ''],
        ]);
    }

    public function show(string $slug): Response
    {
        return Inertia::render('Public/PostShow', [
            'post' => $this->posts->findPublishedOrFail($slug),
        ]);
    }
}
