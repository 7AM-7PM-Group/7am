<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class PromotionCreate extends Component
{

    use WithFileUploads;
    public $titles = "", $oldImage = "", $id;

    #[Validate('required|string|max:255')]
    public $title = '';

    public $image = '';

    protected function rules()
    {
        return [
            'image' => $this->id ? "nullable|file|image|max:5120" : "required|file|image|max:5120",
        ];
    }

    #[Validate("required|string")]
    public $description = '';

    #[Validate("required|integer")]
    public $order = '';

    #[Validate("required|boolean")]
    public $is_active = true;

    #[On('createModal')]
    public function openCreateModal()
    {
        // dd('asdfasd');
        $this->resetValidation();
        $this->id = '';
        $this->title = "";
        $this->image = "";
        $this->oldImage = "";
        $this->description = "";
        $this->order = "";
        $this->is_active = true;
        $this->titles = "Create Promotion";
        $this->dispatch('modal-show', name: 'promotion-modal');
        // dd('asdfasd');
    }

    #[On('editModal')]
    public function openEditModal($id)
    {
        $promotion = \App\Models\Promotion::findOrFail($id)->first();

        // dd($promotion);



        $this->resetValidation();
        $this->id = $promotion->id;
        $this->title = $promotion->title;
        $this->image = "";
        $this->oldImage = asset('storage/' . $promotion->image);
        $this->description = $promotion->description;
        $this->order = $promotion->order;
        $this->is_active = $promotion->is_active;
        $this->titles = "Edit Promotion";
        // dd($this->id, $this->oldImage);

        $this->dispatch('modal-show', name: 'promotion-modal');
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->image instanceof TemporaryUploadedFile) {
            $manager = new ImageManager(Driver::class);

            // Baca file dan kompres
            $image = $manager->read($this->image->getRealPath())->toJpeg(50);

            // Simpan file yang sudah dikompres
            $image_path = 'promotion/' . time() . '.jpg';
            Storage::disk('public')->put($image_path, (string) $image);
            $validatedData['image'] = $image_path;
        } else {
            $validatedData['image'] = $this->oldImage;
        }

        \App\Models\Promotion::updateOrCreate(
            ['id' => $this->id],
            [
                'title' => $validatedData['title'],
                'image' => $validatedData['image'],
                'description' => $validatedData['description'],
                'order' => $validatedData['order'],
                'is_active' => $validatedData['is_active'],
            ]
        );

        $this->dispatch('modal-close', name: 'promotion-modal');
        $this->dispatch('refreshPromotionList');
    }

    public function render()
    {
        return view('livewire.promotion-create')->layout('components.layouts.app', ['title' => $this->titles]);
    }
}
