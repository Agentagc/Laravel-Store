<?php

use App\Models\Guaranty;
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

new #[Layout('admin.master'), Title('لیست گارانتی ها')]
class extends Component {
    use WithPagination;


    #[Validate('required|unique:guaranties,name')]
    public $name;

    public $search = '';

    public $editIndex;


    public function createRow(): void
    {
        $this->validate();


        Guaranty::query()->create([
            'name' => $this->name,
        ]);

        session()->flash('success', ' گارانتی جدید ایجاد شد');
        $this->reset();
    }

    public function editRow($id): void
    {
        $this->editIndex = $id;
        $guaranty = Guaranty::query()->findOrFail($id);
        $this->name = $guaranty->name;
    }

    public function updateRow(): void
    {
        $this->validate();

        $guaranty = Guaranty::query()->findOrFail($this->editIndex);


        $guaranty->update([
            'name' => $this->name,
        ]);

        session()->flash('success', 'گارانتی ویرایش شد');
        $this->reset();
    }

    #[Computed()]
    public function guaranties(): Paginator
    {
        return Guaranty::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->paginate(10);
    }

    #[On('destroy-guaranty')]
    public function destroyRow($guaranty_id): void
    {
        Guaranty::destroy($guaranty_id);
    }

    public function confirmDelete($id): void
    {
        $this->dispatch('delete-guaranty', guaranty_id: $id);
    }

};
?>

<div class="max-w-4xl mx-auto px-6 pt-6 space-y-6">
    <div class="panel">

        @include('admin.layouts.success')
        @include('layouts.waiting')

        <div class="flex items-center justify-between mb-5 pt-2">
            <h5 class="text-lg font-semibold dark:text-white-light">ایجاد گارانتی</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name">نام گارانتی</label>
                        <input wire:model="name" id="name" type="text" class="form-input">
                        @error('name')
                        <p class="text-danger mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                @if($editIndex)
                    <button wire:click.prevent="updateRow" class="btn btn-primary !mt-6">ویرایش</button>
                @else
                    <button wire:click.prevent="createRow" class="btn btn-success !mt-6">ثبت</button>
                @endif
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="flex items-center justify-between mb-5">
            <h5 class="text-lg font-semibold dark:text-white-light">لیست گارانتی ها</h5>
            <a href="#"
               class="flex items-center gap-2 btn btn-outline-danger btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.5001 6H3.5" stroke="current\App\Models\Guaranty" stroke-width="1.5" stroke-linecap="round"/>
                    <path
                        d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5"
                        stroke="current\App\Models\Guaranty" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                گارانتی های حذف شده
            </a>
        </div>

        <div class="mb-5">
            <div class="relative">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="current\App\Models\Guaranty" stroke-width="1.5"/>
                        <path d="M16.5 16.5L21 21" stroke="current\App\Models\Guaranty" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="جستجو در گارانتی ها ..."
                    class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary dark:focus:border-primary transition"
                />
            </div>
        </div>

        <div class="mb-5">
            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام گارانتی</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->guaranties as $index => $guaranty)
                        <tr>
                            <td class="w-10">
                                {{ $this->guaranties->firstItem() + $index }}
                            </td>

                            <td class="whitespace-nowrap font-medium">
                                {{ $guaranty->name }}
                            </td>

                            <td class="whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ Verta::instance($guaranty->created_at)->formatJalaliDate()  }}
                            </td>

                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="editRow({{ $guaranty->id }})" type="button" x-tooltip="ویرایش"
                                            class="p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg" class="text-blue-500">
                                            <path
                                                d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z"
                                                stroke="currentColor" stroke-width="1.5"/>
                                            <path opacity="0.5"
                                                  d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015"
                                                  stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </button>

                                    <button
                                        wire:click="confirmDelete({{ $guaranty->id }})"
                                        type="button" x-tooltip="حذف"
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
    {{ $this->guaranties->links('admin.layouts.admin_pagination') }}
</div>
@script
<script !src="">
    Livewire.on('delete-guaranty', (event) => {
        Swal.fire({
            title: 'حذف گارانتی',
            text: 'آیا مطمئن هستید؟',
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
                Livewire.dispatch('destroy-guaranty', {guaranty_id: event.guaranty_id});
                Swal.fire({
                    title: 'حذف شد!',
                    text: 'گارانتی با موفقیت حذف شد.',
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

