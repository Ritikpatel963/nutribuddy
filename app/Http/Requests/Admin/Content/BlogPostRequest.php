<?php

namespace App\Http\Requests\Admin\Content;

use App\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage content') ?? false;
    }

    public function rules(): array
    {
        $post = $this->route('blogPost');

        return [
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'], 'author_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post instanceof BlogPost ? $post->id : null)],
            'content' => ['required', 'string'], 'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])], 'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'], 'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
        ];
    }
}
