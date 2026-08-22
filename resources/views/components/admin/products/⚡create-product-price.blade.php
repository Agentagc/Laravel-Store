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
use App\Models\Color;
use App\Models\Guaranty;
use App\Models\ProductPrice;

new #[Layout('admin.master'), Title('ایجاد محصول')] class extends Component {
    #[Validate('required')]
    public $main_price;

    #[Validate('required')]
    #[Validate('required')]
    #[Validate('required')]
    public $discount,
        $count,
        $max_sell;

    #[Validate('required')]
    #[Validate('required')]
    public $color_id,
        $guaranty_id;

    public $price;

    public $parent_id;
    public $colors;
    public $guaranties;

    public $product;

    public function mount(Product $product): void
    {
        $this->product = $product;

        $this->colors = Color::query()->pluck('name', 'id');
        $this->guaranties = Guaranty::query()->pluck('name', 'id');
    }

    public function createRow(): void
    {
        $this->validate();

        $exist = ProductPrice::query()->where('color_id', $this->color_id)

        ProductPrice::query()->create([
            'main_price' => $this->main_price,
            'price' => $this->main_price * (1 - $this->discount / 100),
            'discount' => $this->discount,
            'count' => $this->count,
            'max_sell' => $this->max_sell,
            'status' => ProductStatus::Active->value,
            'product_id' => $this->product->id,
            'color_id' => $this->color_id,
            'guaranty_id' => $this->guaranty_id,
        ]);

        $this->product->colors()->syncWithoutDetaching([$this->color_id]);

        $this->product->guaranties()->syncWithoutDetaching([$this->guaranty_id]);

        $productId = $this->product->id;

        session()->flash('success', 'تنوع قیمت محصول ایجاد شد');

        $this->reset();

        $this->redirectRoute('admin.product.prices', [
            'product' => $productId,
        ]);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()->where('parent_id', '!=', null)->pluck('name', 'id');
    }

    #[Computed]
    public function brands()
    {
        return Brand::query()->pluck('name', 'id');
    }
};
?>

<div class="max-w-4xl mx-auto px-6 pt-6 space-y-6">

    @include('admin.layouts.success')
    @include('layouts.waiting')

    <div class="flex items-center justify-between">
        <div>
            <h5 class="text-lg font-semibold dark:text-white-light">ایجاد تنوع قیمت محصول جدید</h5>
            <p class="text-sm text-gray-500 mt-1">اطلاعات تنوع قیمت محصول را تکمیل کنید</p>
        </div>
    </div>

    {{-- بخش ۱: اطلاعات پایه --}}
    <div class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"
                    fill="currentColor" />
            </svg>
            اطلاعات پایه
        </h6>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div wire:ignore>
                <label for="color_id">رنگ <span class="text-xs text-gray-400">(اختیاری)</span></label>
                <select wire:model="color_id" id="color-select">
                    <option>انتخاب کنید</option>
                    @foreach ($this->colors as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('color_id')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div wire:ignore>
                <label for="brand_id">گارانتی <span class="text-xs text-gray-400">(اختیاری)</span></label>
                <div>
                    <select wire:model="guaranty_id" id="guaranty-select">
                        <option>انتخاب کنید</option>
                        @foreach ($this->guaranties as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                @error('guaranty_id')
                    <p class="text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- بخش ۲: قیمت‌گذاری --}}
    <div class="panel">
        <h6 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">قیمت‌گذاری و موجودی</h6>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="main_price">قیمت اصلی <span class="text-danger text-xs">*</span></label>
                <input wire:model="main_price" id="name" type="text" placeholder="0" class="form-input">
                @error('main_price')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="discount">درصد تخفیف <span class="text-danger text-xs">*</span></label>
                <input wire:model="discount" id="discount" type="text" placeholder="۰" class="form-input">
            </div>
            <div>
                <label for="count">تعداد <span class="text-danger text-xs">*</span></label>
                <input wire:model="count" id="count" type="text" placeholder="۰" class="form-input">
            </div>
        </div>
        <div class="mt-4 max-w-xs">
            <label for="max_sell">حداکثر فروش <span class="text-danger text-xs">*</span></label>
            <input wire:model="max_sell" id="max_sell" type="text" placeholder="بدون محدودیت" class="form-input">
        </div>
    </div>

    {{-- دکمه‌ها --}}
    <div class="flex items-center justify-between pb-6">
        <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary">انصراف</a>
        <button wire:click.prevent="createRow" class="btn btn-success px-8">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="ltr:mr-2 rtl:ml-2">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
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
        els.forEach(function(select) {
            NiceSelect.bind(select);
        });

        // seachable
        let options = {
            searchable: true,
        };
        NiceSelect.bind(document.getElementById('color-select'), options);
        NiceSelect.bind(document.getElementById('guaranty-select'), options);

        class MyUploadAdapter {
            constructor(loader) {
                // The file loader instance to use during the upload. It sounds scary but do not
                // worry — the loader will be passed into the adapter later on in this guide.
                this.loader = loader;

                // URL where to send files.
                {{-- this.url = '{{ route('ckeditor.upload') }}'; --}}
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
                editor.model.document.on('change:data', () => {
                    @this.set('description', editor.getData(), false)
                })
            })
            .catch(error => {
                console.error(error.stack);
            });
    </script>
@endscript
