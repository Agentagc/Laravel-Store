
<div class="fixed inset-0 z-[999] overflow-y-auto bg-[black]/60 hidden " wire:loading.class.remove="hidden">
    <div class="flex items-center justify-center min-h-screen px-4" @click.self="open = false">
        <div x-show="open" x-transition="" x-transition.duration.300="" class="w-full max-w-lg p-0 my-8 bg-transparent overflow-hidden border-0 rounded-lg panel">
            <div class="p-5 flex justify-center">
                <div class="loader " wire:loading.attr="block">
                </div>
            </div>
        </div>
    </div>
</div>
