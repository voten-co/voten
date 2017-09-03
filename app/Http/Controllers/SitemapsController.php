<?php

namespace App\Http\Controllers;

use App\Category;
use App\Help;
use App\Submission;
use App\User;

class SitemapsController extends Controller
{
    /**
     * Loads the main indexing sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function index()
    {
        return response()->view('sitemaps.index')
                    ->header('Content-Type', 'text/xml');
    }

    /**
     * Loads the pages sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function pages()
    {
        return response()->view('sitemaps.pages')
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Loads the submissions sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function submissions()
    {
        $submissions = Submission::orderBy('id', 'desc')
            ->select('id', 'category_name', 'slug', 'created_at')
            ->get();

        return response()->view('sitemaps.submissions', compact('submissions'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Loads the users sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function users()
    {
        $users = User::orderBy('id', 'desc')
            ->select('id', 'username', 'updated_at')
            ->get();

        return response()->view('sitemaps.users', compact('users'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Loads the categories sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function categories()
    {
        $categories = Category::orderBy('id', 'desc')
            ->select('id', 'name', 'created_at')
            ->get();

        return response()->view('sitemaps.categories', compact('categories'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Loads the helps sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function helps()
    {
        $helps = Help::orderBy('id', 'desc')->get();

        return response()->view('sitemaps.helps', compact('helps'))
            ->header('Content-Type', 'text/xml');
    }
}
