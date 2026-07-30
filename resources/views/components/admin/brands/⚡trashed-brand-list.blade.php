<?php

use App\Models\Brand;
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

new #[Layout('admin.master'), Title('لیست برند های حذف شده')]
class extends Component {
    use WithPagination;

    #[Computed()]
    public function brands(): Paginator
    {
        return Brand::query()
            ->onlyTrashed()
            ->paginate(10);
    }

    #[On('hard-destroy-brand')]
    public function hardDestroyRow($brand_id): void
    {
        Brand::query()->withTrashed()->findOrFail($brand_id)->forceDelete();
    }

    public function restoreRow($brand_id): void
    {
        Brand::query()->withTrashed()->findOrFail($brand_id)->restore();
    }

    public function confirmDelete($id): void
    {
        $this->dispatch('hard-delete-brand', brand_id: $id);
    }
};
?>

<div class="max-w-4xl mx-auto px-6 pt-6 space-y-6" >
    <div class="panel">
        @include('layouts.waiting')
        <div class="flex items-center justify-between mb-5">
            <h5 class="text-lg font-semibold dark:text-white-light">لیست برند ها</h5>
            <a href="{{ route("admin.brands.list") }}"
               class="flex items-center gap-2 btn btn-outline-danger btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5"
                          stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                برند ها
            </a>
        </div>
        <div class="mb-5">
            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>تصویر</th>
                        <th>نام برند</th>
                        <th>تاریخ حذف</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->brands as $index => $brand)
                        <tr>
                            <td class="w-10">
                                {{ $this->brands->firstItem() + $index }}
                            </td>

                            {{-- بخش تصویر --}}
                            <td class="w-20">
                                @if($brand->image)
                                    <div class="flex items-center justify-center">
                                        <img
                                            src="{{ url('images/brands/' . $brand->image) }}"
                                            alt="{{ $brand->name }}"
                                            class="w-12 h-12 rounded-lg object-cover ring-2 ring-gray-200 dark:ring-gray-700"
                                            onerror="this.src='{{ url('images/placeholder.png') }}'"
                                        >
                                    </div>
                                @else
                                    <div
                                        class="flex items-center justify-center w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.5"
                                                  d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z"
                                                  stroke="currentColor" stroke-width="1.5"/>
                                            <path
                                                d="M9 10C9 10.5523 8.55228 11 8 11C7.44772 11 7 10.5523 7 10C7 9.44772 7.44772 9 8 9C8.55228 9 9 9.44772 9 10Z"
                                                fill="currentColor"/>
                                            <path
                                                d="M2 13L5.10556 10.2147C5.85739 9.55048 7.01616 9.59703 7.71213 10.3186L11.7994 14.7196C12.3882 15.3416 13.3559 15.4267 14.0461 14.9151L14.2935 14.7285C15.1078 14.1286 16.2319 14.1918 16.9765 14.8803L20 17.6667"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap font-medium">
                                {{ $brand->name }}
                            </td>

                            <td class="whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ Verta::instance($brand->deleted_at)->formatJalaliDate()  }}
                            </td>

                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="restoreRow({{$brand->id}})" type="button" x-tooltip="بازگردانی"
                                            class="p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    </button>

                                    <button
                                        wire:click="confirmDelete({{ $brand->id }})"
                                        type="button" x-tooltip="حذف دائم"
                                        class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg" class="text-rose-500">
                                            <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5"
                                                  stroke-linecap="round"/>
                                            <path
                                                d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path opacity="0.5" d="M9.5 11L10 16M14.5 11L14 16" stroke="currentColor"
                                                  stroke-width="1.5" stroke-linecap="round"/>
                                            <path opacity="0.5"
                                                  d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6"
                                                  stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $this->brands->links('admin.layouts.admin_pagination') }}
</div>
@script
<script !src="">
    Livewire.on('hard-delete-brand', (event) => {
        Swal.fire({
            title: 'حذف برند',
            text: 'آیا مطمئن هستید؟ این عملیات قابل بازگشت نیست.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف کن',
            cancelButtonText: 'انصراف',
            customClass: {
                confirmButton: 'btn btn-danger ltr:ml-4 rtl:mr-4',
                cancelButton: 'btn btn-outline-secondary',
                popup: 'sweet-alerts',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('hard-destroy-brand', { brand_id: event.brand_id });
                Swal.fire({
                    title: 'حذف شد!',
                    text: 'برند با موفقیت حذف شد.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        popup: 'sweet-alerts',
                    },
                    buttonsStyling: false,
                });
            }
        });
    });

</script>
@endscript

