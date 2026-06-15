<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Post;

final class PostController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $this->view('admin/posts/index', [
            'title' => 'Insights',
            'posts' => Post::all('created_at DESC'),
        ], 'admin');
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/posts/form', ['title' => 'New Insight', 'post' => null], 'admin');
    }

    public function store(): void
    {
        $user = Auth::require('admin');
        $this->validate([
            'title'  => 'required|min:3|max:200',
            'status' => 'in:draft,published',
        ], '/admin/posts/create');

        $status = Request::string('status', 'draft');
        $id = Post::create([
            'author_id'    => $user['id'],
            'title'        => Request::string('title'),
            'slug'         => $this->uniqueSlug(slugify(Request::string('title'))),
            'excerpt'      => Request::string('excerpt'),
            'content'      => Request::string('content'),
            'category'     => Request::string('category', 'General'),
            'tags'         => Request::string('tags'),
            'status'       => $status,
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ]);
        $this->respond(['id' => $id], 'Insight saved.', '/admin/posts');
    }

    public function edit(array $params): void
    {
        Auth::require('admin');
        $post = Post::find((int) $params['id']);
        if ($post === null) {
            flash('error', 'Post not found.');
            redirect('/admin/posts');
        }
        $this->view('admin/posts/form', ['title' => 'Edit Insight', 'post' => $post], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $post = Post::find($id);
        if ($post === null) {
            flash('error', 'Post not found.');
            redirect('/admin/posts');
        }
        $this->validate([
            'title'  => 'required|min:3|max:200',
            'status' => 'in:draft,published',
        ], '/admin/posts/' . $id . '/edit');

        $status = Request::string('status', 'draft');
        $slug = $post['slug'];
        if (Request::string('title') !== $post['title']) {
            $slug = $this->uniqueSlug(slugify(Request::string('title')), $id);
        }
        Post::update($id, [
            'title'        => Request::string('title'),
            'slug'         => $slug,
            'excerpt'      => Request::string('excerpt'),
            'content'      => Request::string('content'),
            'category'     => Request::string('category', 'General'),
            'tags'         => Request::string('tags'),
            'status'       => $status,
            'published_at' => $status === 'published' ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ]);
        $this->respond(['id' => $id], 'Insight updated.', '/admin/posts');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Post::delete((int) $params['id']);
        $this->respond(null, 'Insight deleted.', '/admin/posts');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM posts WHERE slug = ?';
            $params = [$slug];
            if ($ignoreId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            if ((int) Database::scalar($sql, $params) === 0) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}
