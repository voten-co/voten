<?php

namespace App\Http\Controllers;

use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;

class BlockCategoriesController extends Controller
{
    use CachableUser;

    /**
     * Store a newly created hidden_category record in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            DB::table('hidden_categories')->insert([
                'user_id'     => Auth::id(),
                'category_id' => $request->category_id,
            ]);

            // update the cach record for hiddenSubmissions:
            $this->updateHiddenCategories(Auth::id(), $request->category_id);

            return response('Channel blocked successfully.', 200);
        } catch (\Exception $e) {
            return response('Something went wrong!', 500);
        }
    }

    /**
     * Remove the hidden_category record from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        DB::table('hidden_categories')->where([
            ['user_id', Auth::id()],
            ['category_id', $request->category_id],
        ])->delete();

        return response('Channel unblocked successfully.', 200);
    }
}
