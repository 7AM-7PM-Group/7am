<div class="space-y-4">
    <div class="w-full h-[200px] md:h-[400px] bg-center bg-cover bg-no-repeat flex justify-center items-center text-white text-3xl md:text-5xl font-bold"
        style="background-image: url('{{ asset('assets/promotion-hero.png') }}')">
        <img alt="Promotion Hero" class="md:h-1/5 h-1/3" src="{{ asset('assets/7am.png') }}">
    </div>

    <flux:container class="space-y-6">
        @foreach ($promotions as $promotion)
            <div class="md:flex even:md:flex-row-reverse md:flex-wrap">
                <div class="md:w-9/24 aspect-4/3">
                    <img alt="{{ $promotion->title }}" class="w-full rounded-lg "
                        src="{{ asset('storage/' . $promotion->image) }}">
                </div>
                <div class="md:w-15/24 flex flex-col p-4 space-y-4">
                    <h2 class="text-xl text-center font-semibold">{{ $promotion->title }}</h2>
                    <p class="text-gray-700 text-sm">{{ $promotion->description }}</p>
                </div>
                <div class=" mt-4! w-full">
                    <flux:separator />
                </div>
            </div>
        @endforeach
    </flux:container>
</div>
