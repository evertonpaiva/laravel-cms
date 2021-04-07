<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Pages extends Component
{
    use WithPagination;
    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;
    public $modelId;
    public $slug;
    public $title;
    public $content;
    public $isSetToDefaultHomePage;
    public $isSetToDefaultNotFoundPage;

    /**
     * The validation rules.
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'slug' => ['required', Rule::unique('pages', 'slug')->ignore($this->modelId)],
            'content' => 'required'
        ];
    }

    /**
     * The livewire mount function
     */
    public function mount()
    {
        // Resets the pagination after reloading the page
        $this->resetPage();
    }

    /**
     * Run everytime the title
     * variable is updated.
     *
     * @param $value
     */
    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    /**
     * The create function.
     */
    public function create()
    {
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFoundPage();
        Page::create($this->modelData());
        $this->modalFormVisible = false;
        $this->reset();
        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'New Page',
            'eventMessage' => 'Another paga has been created!',
        ]);
    }

    /**
     * The read function
     * @return mixed
     */
    public function read()
    {
        return Page::paginate(5);
    }

    /**
     * The update function
     */
    public function update()
    {
        $this->validate();
        $this->unassignDefaultHomePage();
        $this->unassignDefaultNotFoundPage();
        Page::find($this->modelId)->update($this->modelData());
        $this->modalFormVisible = false;

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Updated Page',
            'eventMessage' => 'There is a page('. $this->modelId .') that has been updated!',
        ]);
    }

    /**
     * The delete function
     */
    public function delete()
    {
        Page::destroy($this->modelId);
        $this->modalConfirmDeleteVisible = false;
        $this->resetPage();

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The page ('. $this->modelId .') has been deleted!',
        ]);
    }

    public function updatedIsSetToDefaultHomePage()
    {
        $this->isSetToDefaultNotFoundPage = null;
    }

    public function updatedIsSetToDefaultNotFoundPage()
    {
        $this->isSetToDefaultHomePage = null;
    }

    /**
     * Show the form modal
     * of the create function.
     */
    public function createShowModal()
    {
        $this->resetValidation();
        $this->reset();
        $this->modalFormVisible = true;
    }

    /**
     * Shows the form modal
     * in update mode.
     * @param $id
     */
    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->reset();
        $this->modelId = $id;
        $this->modalFormVisible = true;
        $this->loadModel();
    }

    /**
     * Shows the delete confirmation modal
     *
     * @param $id
     */
    public function deleteShowModal($id)
    {
        $this->modelId = $id;
        $this->modalConfirmDeleteVisible = true;
    }

    /**
     * Loads the model data
     * of this component.
     */
    public function loadModel()
    {
        $data = Page::find($this->modelId);
        $this->title = $data->title;
        $this->slug = $data->slug;
        $this->content = $data->content;
        $this->isSetToDefaultHomePage = !$data->is_default_home ? null:true;
        $this->isSetToDefaultNotFoundPage = !$data->is_default_not_found ? null:true;
    }

    /**
     * The data for the model mapped
     * in this component.
     *
     * @return array
     */
    public function modelData()
    {
        return [
          'title' => $this->title,
          'slug' => $this->slug,
          'content' => $this->content,
          'is_default_home' => $this->isSetToDefaultHomePage,
          'is_default_not_found' => $this->isSetToDefaultNotFoundPage,
        ];
    }

    /**
     * Unassigns the default home page in the database table
     */
    private function unassignDefaultHomePage()
    {
        if($this->isSetToDefaultHomePage != null) {
            Page::where('is_default_home', true)->update([
               'is_default_home' => false,
            ]);
        }
    }

    /**
     * Unassigns the default not 404 page in the database table
     */
    private function unassignDefaultNotFoundPage()
    {
        if($this->isSetToDefaultNotFoundPage != null) {
            Page::where('is_default_not_found', true)->update([
                'is_default_not_found' => false,
            ]);
        }
    }

    /**
     * Dispatch event
     */
    public function dispatchEvent()
    {
        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Sample Event',
            'eventMessage' => 'You have a sample event notification!',
        ]);
    }

    /**
     * The Livewire render function.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.pages', [
            'data' => $this->read(),
        ]);
    }
}
