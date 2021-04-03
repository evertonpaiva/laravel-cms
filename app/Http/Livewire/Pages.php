<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Pages extends Component
{
    public $modalFormVisible = true;
    public $slug;
    public $title;
    public $content;

    /**
     * The validation rules.
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'slug' => ['required', Rule::unique('pages', 'slug')],
            'content' => 'required'
        ];
    }

    /**
     * Run everytime the title
     * variable is updated.
     *
     * @param $value
     */
    public function updatedTitle($value)
    {
        $this->generateSlug($value);
    }

    /**
     * The create function.
     */
    public function create()
    {
        $this->validate();
        Page::create($this->modelData());
        $this->modalFormVisible = false;
        $this->resetVars();
    }

    /**
     * Show the form modal
     * of the create function.
     */
    public function createShowModal()
    {
        $this->modalFormVisible = true;
    }

    /**
     * The data for the model mapped
     * in this component.
     *
     * @return array
     */
    public function  modelData()
    {
        return [
          'title' => $this->title,
          'slug' => $this->slug,
          'content' => $this->content
        ];
    }

    /**
     * Resets all the variables
     * to null.
     */
    public function resetVars()
    {
        $this->title = null;
        $this->slug = null;
        $this->content = null;
    }

    /**
     * Generate a url slug
     * base on the title
     * @param $value
     */
    private function generateSlug($value)
    {
        $process1 = str_replace(' ', '-', $value);
        $process2 = strtolower($process1);
        $this->slug = $process2;
    }

    /**
     * The Livewire render function.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.pages');
    }
}
