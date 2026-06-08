<div class="py-2 px-4 w-full bg-white h-fit rounded dark:bg-gray-700 flex justify-between">
    <div class="">

        <div class="font-semibold md:text-lg">{{ $slot }}</div>
        <div class="">
            @if (session()->has('success'))
                <div class="text-green-400 text-sm">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="text-rose-400 text-sm">{{ session('error') }}</div>
            @endif
        </div>
    </div>
    <div class="">{{ $button ?? '' }}</div>
</div>
