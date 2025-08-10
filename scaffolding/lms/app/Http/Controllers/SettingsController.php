<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    public function setLanguage(Request $request)
    {
        $lang = $request->input('lang', 'en');
        session(['locale' => $lang, 'lang' => $lang]);
        return redirect()->back();
    }

    public function dismissOnboarding(Request $request)
    {
        session(['show_onboarding' => false]);
        return redirect()->back();
    }
}

