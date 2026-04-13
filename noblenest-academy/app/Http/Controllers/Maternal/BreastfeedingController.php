<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Services\MaternalContentFilterService;
use Illuminate\Support\Facades\Auth;

class BreastfeedingController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index()
    {
        $profile = Auth::user()->maternalProfile;

        $content = $this->filter->safeContentQuery($profile)
            ->forCategory('breastfeeding')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('maternal.breastfeeding.index', compact('content', 'profile'));
    }
}
