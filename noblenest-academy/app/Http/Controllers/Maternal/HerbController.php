<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Services\MaternalContentFilterService;
use Illuminate\Support\Facades\Auth;

class HerbController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index()
    {
        $profile = Auth::user()->maternalProfile;

        $herbs = $this->filter->safeContentQuery($profile)
            ->forCategory('herbs')
            ->orderBy('title')
            ->paginate(12);

        return view('maternal.herbs.index', compact('herbs', 'profile'));
    }
}
