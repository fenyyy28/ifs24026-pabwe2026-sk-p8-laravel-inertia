<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Services\PostExcelService;
use App\Features\Post\Services\PostService;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPostController
{
    public function __construct(
        private readonly PostService $posts,
        private readonly PostExcelService $excel,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category', 'status']);

        return Inertia::render('Admin/PostList', [
            'posts' => $this->posts->listAll($filters),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function userPosts(Request $request, User $user): Response
    {
        $filters = $request->only(['search', 'category', 'status']);

        return Inertia::render('Admin/UserPostList', [
            'author' => $user->only(['id', 'name', 'username']),
            'posts' => $this->posts->listForUser($user, $filters),
            'stats' => $this->posts->statsForUser($user),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function export(): StreamedResponse
    {
        return $this->download($this->excel->export($this->posts->all(), true), 'semua-artikel.xlsx');
    }

    public function exportUser(User $user): StreamedResponse
    {
        return $this->download($this->excel->export($this->posts->allForUser($user), true), 'artikel-'.$user->username.'.xlsx');
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
