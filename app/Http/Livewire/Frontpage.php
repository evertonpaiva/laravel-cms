<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Frontpage extends Component
{
    public $urlslug;
    public $title;
    public $content;

    /**
     * The livewire mount function.
     *
     * @param $urlslug
     */
    public function mount($urlslug = null)
    {
        $this->retrieveContent($urlslug);
    }

    /**
     * Retrieves the content of the page.
     *
     * @param $urlslug
     */
    public function retrieveContent($urlslug)
    {
        // Get home page if slug is empty
        if(empty($urlslug)){
            $data = Page::where('is_default_home', true)->first();
        } else {

            // Get the page according to the url slug
            $data = Page::where('slug', $urlslug)->first();

            // If we can't retrieve anything, let's get the default 404 not found page
            if(!$data) {
                $data = Page::where('is_default_not_found', true)->first();
            }
        }

        $this->title = $data->title;
        $this->content = $data->content;
    }

    /**
     * Gets all the sidebar links
     *
     * @return mixed
     */
    public function sideBarLinks()
    {
        return DB::table('navigation_menus')
            ->where('type', '=', 'SidebarNav')
            ->orderBy('sequence', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get the top navigation links
     *
     * @return mixed
     */
    public function topNavLinks()
    {
        return DB::table('navigation_menus')
            ->where('type', '=', 'TopNav')
            ->orderBy('sequence', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * The livewire render function
     *
     * @return mixed
     */
    public function render()
    {
        return view('livewire.frontpage', [
            'sideBarLinks' => $this->sideBarLinks(),
            'topNavLinks' => $this->topNavLinks(),
        ])->layout('layouts.frontpage');
    }
}
