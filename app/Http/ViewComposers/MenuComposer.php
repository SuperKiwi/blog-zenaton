<?php

namespace Framework\Http\ViewComposers;

use Illuminate\View\View;
use Framework\Models\Category;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('categories', Category::select('title', 'slug')->get());
    }
}
