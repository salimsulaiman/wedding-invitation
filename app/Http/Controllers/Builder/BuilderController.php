<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BuilderController extends Controller
{
    public function index(Invitation $invitation): Response
    {
        $invitation->load([
            'theme:id,name,theme_category_id,thumbnail',
            'theme.category:id,name,allow_custom_photos',
            'theme.category:id,name',
            'domain',
            'couples',
            'events',
            'galleries',
            'coverPhotos',
            'loveStories',
            'accounts',
            'giftAddress',
            'music',
            'guests',
        ])->loadCount('guests');

        $accessibleCategoryIds = $invitation->user->accessibleThemeCategories()->pluck('theme_categories.id');

        $availableThemes = Theme::whereIn('theme_category_id', $accessibleCategoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'theme_category_id', 'thumbnail']);

        return Inertia::render('Builder/Index', [
            'invitation' => $invitation,
            'availableThemes' => $availableThemes,
        ]);
    }

    public function close(Invitation $invitation): RedirectResponse
    {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.invitations.index')
            : redirect()->route('user.invitations.index');
    }
}