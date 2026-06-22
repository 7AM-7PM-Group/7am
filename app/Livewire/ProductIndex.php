<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CustomerPricelist;
use App\Models\Product;
use App\Models\SubCategory;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\PricelistRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $title = 'All Product';

    public $productId;

    public $categories;

    public $subCategories;

    protected EsbApiRequest $request;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $category = '';

    #[Url(except: '')]
    public $sub_category = '';

    #[Url(except: 1)]
    public $page = 1;

    #[Url(except: 50)]
    public $per_page = 50;

    public $name = '';

    #[Validate('nullable')]
    public $description = '';

    #[Validate('nullable|file|image')]
    public $image;

    public $preview;

    #[Validate('required|integer')]
    public $moq = 1;

    public $maximum_order = 0;

    #[Validate('required_unless:maximum_order, 0|nullable')]
    public $cutoff_time = '';

    public function mount(EsbApiAuth $auth)
    {
        $this->request = new EsbApiRequest($auth);
        $this->categories = Category::all();
        $this->subCategories = SubCategory::all();
    }

    public function updatedSearch()
    {
        // $this->resetPage();
    }

    public function updatedImage()
    {
        // Jangan gunakan dd() di sini karena akan memutus sinkronisasi state Livewire
    }

    public function sync()
    {
        Product::syncProduct();
        CustomerPricelist::syncCustomerPricelist();
        // CustomerPricelist::sync(new PricelistRequest(new EsbApiRequest($auth)));

    }

    public function openEditModal($id)
    {
        $this->reset('image');
        $this->resetValidation();
        $this->productId = $id;
        $product = Product::find($id);
        $this->name = $product->name;
        $this->preview = $product->image ? asset('storage/'.$product->image) : '';
        $this->description = $product->description;
        $this->moq = $product->moq ?? 1;
        $this->maximum_order = $product->maximum_order ?? 0;
        $this->cutoff_time = $product->cutoff_time;

        $this->dispatch('modal-show', name: 'edit-product-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $product = Product::find($this->productId);

            if (! $product) {
                return;
            }

            $oldPath = $product->image;
            $path = $oldPath;

            if ($this->image instanceof TemporaryUploadedFile) {
                $manager = new ImageManager(new Driver);
                $image = $manager->read($this->image->getRealPath());

                // Optimasi gambar
                $image->scaleDown(width: 800);
                $quality = 90;
                $encoded = $image->toWebp($quality);

                while (strlen((string) $encoded) > 102400 && $quality > 20) {
                    $quality -= 5;
                    $encoded = $image->toWebp($quality);
                }

                $filename = uniqid().'.webp';
                $path = 'product/'.$filename;
                Storage::disk('public')->put($path, (string) $encoded);
            }

            $product->update([
                'name' => $this->name,
                'image' => $path,
                'description' => $this->description,
                'moq' => $this->moq,
                'cutoff_time' => $this->cutoff_time,
                'maximum_order' => $this->maximum_order,
            ]);

            // Hapus file lama jika ada upload baru
            if ($this->image instanceof TemporaryUploadedFile && $oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            DB::commit();
            session()->flash('success', 'Product updated successfully');
            $this->dispatch('modal-close', name: 'edit-product-modal');
            $this->reset('image');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug')) {
                throw $th;
            }
            session()->flash('error', 'Failed to update product: '.$th->getMessage());
        }
    }

    public function render()
    {

        $products = Product::filters(['search' => $this->search, 'category' => $this->category, 'sub_category' => $this->sub_category])->paginate($this->per_page)->withQueryString();

        // dd($products);

        return view('livewire.product-index', compact('products'))->layout('components.layouts.app', ['title' => 'All Product']);
    }
}
