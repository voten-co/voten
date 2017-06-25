<?php

namespace App\Http\Controllers;

use App\BlockedDomain;
use App\Category;
use Illuminate\Http\Request;

class BlockDomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores a BlockedDomain record.
     *
     * Hint: since general bann happens from through backend form (and not via ajax) we check for it.
     * If it isn't via ajax, it means it's been from backend and done by a VotenAdministrator.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Collection $blockedDomain
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'domain'   => 'required|url',
            'category' => 'alpha_num|max:25',
        ]);

        if (!($blockEverywhere = !$request->ajax() && $this->mustBeVotenAdministrator())) {
            $category = Category::where('name', $request->category)->firstOrFail();
            abort_unless($this->mustBeModerator($category->id), 403);
        }

        $blockedDomain = new BlockedDomain([
            'category'    => $blockEverywhere ? 'all' : $request->category,
            'domain'      => domain($request->domain),
            'description' => $request->description,
        ]);
        $blockedDomain->save();

        return $request->ajax() ? $blockedDomain : back();
    }

    /**
     * Returns all the domains that are blocked for submitting(url type submission) to this category.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|max:25',
        ]);

        return BlockedDomain::where('category', $request->category)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Unblock.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'domain'   => 'required',
            'category' => 'alpha_num|max:25',
        ]);

        if (!($blockEverywhere = !$request->ajax() && $this->mustBeVotenAdministrator())) {
            $category = Category::where('name', $request->category)->firstOrFail();
            abort_unless($this->mustBeModerator($category->id), 403);
        }

        BlockedDomain::where('domain', $request->domain)
            ->where('category', $blockEverywhere ? 'all' : $request->category)
            ->delete();

        return $blockEverywhere ? back() : response('Unblocked in '.$request->category, 200);
    }
}
