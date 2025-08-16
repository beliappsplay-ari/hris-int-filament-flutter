<!--div>
<x-filament::breadcrumbs :breadcrumbs="[
    '/hris/payrolls' => 'Payrolls',
    '' => 'List',
]" />
<div class="flex justify-between mt-1">
    <div class="font-bold text-3xl">Payroll</div>

</div>
    <div>
        <form wire:submit.prevent="save" class="w-full max-w-sm flex mt-2">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="fileInput">
                        Pilih Berkas
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="fileinput" type="file" wire:model='file'>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <button
                        type="submit">
                        Unggah
                    </button>
                </div>
            </form>
    </div>
</div>
