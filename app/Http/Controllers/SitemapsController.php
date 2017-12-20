<?php

namespace App\Http\Controllers;

use App\Channel;
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
            ->select('id', 'channel_name', 'slug', 'created_at')
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
     * Loads the channels sitemap.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function channels()
    {
        $channels = Channel::orderBy('id', 'desc')
            ->select('id', 'name', 'created_at')
            ->get();

        return response()->view('sitemaps.channels', compact('channels'))
            ->header('Content-Type', 'text/xml');
    }
}
