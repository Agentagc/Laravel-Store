<?php

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Contracts\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Hekmatinasser\Verta\Verta;

new #[Layout('admin.master'), Title('ویرایش محصول')]
class extends Component {
    use WithFileUploads;


    public $name;

    public $e_name;

    public $price;

    public $discount, $count, $max_sell;

    public $description;

    public $image;

    public $category_id, $brand_id;

    public $parent_id;

    public $product;


    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->e_name = $product->e_name;
        $this->price = $product->price;
        $this->discount = $product->discount;
        $this->count = $product->count;
        $this->max_sell = $product->max_sell;
        $this->description = $product->description;
        $this->image = $product->image;
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;
        $this->dispatch('loadckeditor',['description'=>$this->description]);

    }

    public function updateRow(): void
    {
        $this->validate([
            'name' => 'required|unique:products,name,'.$this->product->id,
            'e_name' => 'required|unique:products,e_name,'.$this->product->id,
            'description' => 'required',
            'price' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'category_id'=> 'required',
            'brand_id'=> 'required',
        ]);

        if ($this->image) {
            $image = $this->image->hashName();
            $this->image->storeAs('images/products/', $image, 'public');
        };


        Product::query()->findOrFail($this->product->id)->update([
            'name' => $this->name,
            'e_name' => $this->e_name,
            'description' => $this->description,
            'slug' => make_slug($this->name),
            'price' => $this->price,
            'discount' => $this->discount,
            'count' => $this->count,
            'max_sell' => $this->max_sell,
            'image' => $this->image ? $image : $this->product->image,
            'category_id'=> $this->category_id,
            'brand_id'=> $this->brand_id,


        ]);

        session()->flash('success', 'محصول جدید ویرایش شد');
        $this->reset();
        $this->redirectRoute('admin.products.list');
    }




    #[Computed()]
    public function categories()
    {
        return Category::query()
            ->where('parent_id', '!=', null)
            ->pluck('name', 'id');
    }

    #[Computed()]
    public function brands()
    {
        return Brand::query()
            ->pluck('name', 'id');
    }
};
?>

<div class="max-w-4xl mx-auto px-6 pt-6 space-y-6">

    @include('admin.layouts.success')
    @include('layouts.waiting')

    <div class="flex items-center justify-between">
        <div>
            <h5 class="text-lg font-semibold dark:text-white-light">ویرایش محصول</h5>
            <p class="text-sm text-gray-500 mt-1">اطلاعات محصول را تکمیل کنید</p>
        </div>
    </div>

    {{-- بخش ۱: اطلاعات پایه --}}
    <div class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="currentColor"/></svg>
            اطلاعات پایه
        </h6>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div >
                <label for="name">نام محصول <span class="text-danger text-xs">*</span></label>
                <input wire:model="name" id="name" type="text" placeholder="مثلاً: گوشی سامسونگ گلکسی A55" class="form-input">
                @error('name')<p class="text-danger text-xs mt-1">{{$message}}</p>@enderror
            </div>
            <div>
                <label for="e_name">نام انگلیسی <span class="text-danger text-xs">*</span></label>
                <input wire:model="e_name" id="e_name" type="text" placeholder="e.g. Samsung Galaxy A55" class="form-input" dir="ltr">
                @error('e_name')<p class="text-danger text-xs mt-1">{{$message}}</p>@enderror
            </div>
            <div wire:ignore>
                <label for="category_id">دسته‌بندی <span class="text-danger text-xs">*</span></label>
                <select wire:model="category_id" id="category-select" >
                    <option>انتخاب کنید</option>
                    @foreach($this->categories as $key=>$value)
                        @if($key === $category_id)
                            <option selected value="{{$key}}">{{$value}}</option>
                        @else
                            <option value="{{$key}}">{{$value}}</option>
                        @endif

                    @endforeach
                </select>
                @error('category_id')<p class="text-danger text-xs mt-1">{{$message}}</p>@enderror
            </div>
            <div wire:ignore>
                <label for="brand_id">برند<span class="text-danger text-xs">*</span></label>
                <div>
                    <select wire:model="brand_id" id="brand-select">
                        <option>انتخاب کنید</option>
                        @foreach($this->brands as $key=>$value)
                            @if($key === $brand_id)
                                <option selected value="{{$key}}">{{$value}}</option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @error('brand_id')
                <p class="text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- بخش ۲: قیمت‌گذاری --}}
    <div class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">قیمت‌گذاری و موجودی</h6>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="price">قیمت (تومان) <span class="text-danger text-xs">*</span></label>
                <input wire:model="price" id="price" type="text" placeholder="۱۲,۰۰۰,۰۰۰" class="form-input">
                @error('price')<p class="text-danger text-xs mt-1">{{$message}}</p>@enderror
            </div>
            <div>
                <label for="discount">درصد تخفیف <span class="text-xs text-gray-400">(اختیاری)</span></label>
                <input wire:model="discount" id="discount" type="text" placeholder="۰" class="form-input">
            </div>
            <div>
                <label for="count">تعداد <span class="text-xs text-gray-400">(اختیاری)</span></label>
                <input wire:model="count" id="count" type="text" placeholder="۰" class="form-input">
            </div>
        </div>
        <div class="mt-4 max-w-xs">
            <label for="max_sell">حداکثر فروش <span class="text-xs text-gray-400">(اختیاری)</span></label>
            <input wire:model="max_sell" id="max_sell" type="text" placeholder="بدون محدودیت" class="form-input">
        </div>
    </div>

    {{-- بخش ۳: تصویر --}}
    <div class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">تصویر محصول</h6>
        <label for="ctnFile" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-[#e0e6ed] dark:border-[#17263c] rounded-lg cursor-pointer hover:border-primary transition-colors bg-[#f8fafc] dark:bg-[#0e1726]">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-gray-400 mb-2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <p class="text-sm text-gray-500">تصویر را اینجا بکشید یا <span class="text-primary font-semibold">انتخاب کنید</span></p>
            <p class="text-xs text-gray-400 mt-1">فرمت‌های مجاز: JPG، PNG — حداکثر ۲ مگابایت</p>
            <input wire:model="image" id="ctnFile" type="file" class="hidden">
        </label>
        @error('image')<p class="text-danger text-xs mt-2">{{$message}}</p>@enderror
    </div>

    {{-- بخش ۴: توضیحات --}}
    <div wire:ignore class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
            توضیحات محصول <span class="text-danger text-xs">*</span>
        </h6>
        <textarea id="editor" class="form-textarea" rows="5"></textarea>
        @error('description')<p class="text-danger text-xs mt-1">{{$message}}</p>@enderror
    </div>

    {{-- دکمه‌ها --}}
    <div class="flex items-center justify-between pb-6">
        <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary">انصراف</a>
        <button wire:click.prevent="updateRow" class="btn btn-success px-8">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="ltr:mr-2 rtl:ml-2"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            ثبت محصول
        </button>
    </div>

</div>
@assets
<link rel="stylesheet" type="text/css" href="{{ url('panel/css/nice-select2.css') }}">
<script src="{{ url('panel/js/nice-select2.js') }}"></script>
<script src="{{ url('panel/plugins/ckeditor5/ckeditor.js') }}"></script>
@endassets

@script
<script>
    // default
    let els = document.querySelectorAll('.selectize');
    els.forEach(function (select) {
        NiceSelect.bind(select);
    });

    // seachable
    let options = {
        searchable: true,
    };
    NiceSelect.bind(document.getElementById('category-select'), options);
    NiceSelect.bind(document.getElementById('brand-select'), options);

    class MyUploadAdapter {
        constructor(loader) {
            // The file loader instance to use during the upload. It sounds scary but do not
            // worry — the loader will be passed into the adapter later on in this guide.
            this.loader = loader;

            // URL where to send files.
            {{--this.url = '{{ route('ckeditor.upload') }}';--}}
        }

        // Starts the upload process.
        upload() {
            return this.loader.file.then(
                (file) =>
                    new Promise((resolve, reject) => {
                        this._initRequest();
                        this._initListeners(resolve, reject, file);
                        this._sendRequest(file);
                    })
            );
        }

        // Aborts the upload process.
        abort() {
            if (this.xhr) {
                this.xhr.abort();
            }
        }

        // Initializes the XMLHttpRequest object using the URL passed to the constructor.
        _initRequest() {
            const xhr = (this.xhr = new XMLHttpRequest());
            // Note that your request may look different. It is up to you and your editor
            // integration to choose the right communication channel. This example uses
            // a POST request with JSON as a data structure but your configuration
            // could be different.
            // xhr.open('POST', this.url, true);
            xhr.open("POST", this.url, true);
            xhr.setRequestHeader("x-csrf-token", "{{ csrf_token() }}");
            xhr.responseType = "json";
        }

        // Initializes XMLHttpRequest listeners.
        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${file.name}.`;
            xhr.addEventListener("error", () => reject(genericErrorText));
            xhr.addEventListener("abort", () => reject());
            xhr.addEventListener("load", () => {
                const response = xhr.response;
                // This example assumes the XHR server's "response" object will come with
                // an "error" which has its own "message" that can be passed to reject()
                // in the upload promise.
                //
                // Your integration may handle upload errors in a different way so make sure
                // it is done properly. The reject() function must be called when the upload fails.
                if (!response || response.error) {
                    return reject(response && response.error ? response.error.message : genericErrorText);
                }
                // If the upload is successful, resolve the upload promise with an object containing
                // at least the "default" URL, pointing to the image on the server.
                // This URL will be used to display the image in the content. Learn more in the
                // UploadAdapter#upload documentation.
                resolve({
                    default: response.url,
                });
            });
            // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
            // properties which are used e.g. to display the upload progress bar in the editor
            // user interface.
            if (xhr.upload) {
                xhr.upload.addEventListener("progress", (evt) => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        // Prepares the data and sends the request.
        _sendRequest(file) {
            // Prepare the form data.
            const data = new FormData();
            data.append("upload", file);
            // Important note: This is the right place to implement security mechanisms
            // like authentication and CSRF protection. For instance, you can use
            // XMLHttpRequest.setRequestHeader() to set the request headers containing
            // the CSRF token generated earlier by your application.
            // Send the request.
            this.xhr.send(data);
        }

    }

    function SimpleUploadAdapterPlugin(editor) {
        editor.plugins.get("FileRepository").createUploadAdapter = (loader) => {
            // Configure the URL to the upload script in your back-end here!
            return new MyUploadAdapter(loader);
        };
    }

    Livewire.on('loadckeditor', (event) => {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                extraPlugins: [SimpleUploadAdapterPlugin],
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        '|',
                        'fontSize',
                        'fontColor',
                        '|',
                        'imageUpload',
                        'blockQuote',
                        'insertTable',
                        'undo',
                        'redo',
                        'codeBlock'
                    ]
                },
                language: {
                    ui: 'fa',
                    content: 'fa'
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                },

            })
            .then(editor => {
                editor.setData(event[0].description);
                editor.model.document.on('change:data', () => {
                    @this.set('description', editor.getData(), false)
                })
            })
            .catch(error => {
                console.error(error.stack);
            });
    })

</script>
@endscript

