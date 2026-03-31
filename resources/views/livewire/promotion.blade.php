<div class="space-y-4">
    <div class="w-full h-[200px] md:h-[400px] bg-center bg-cover bg-no-repeat flex justify-center items-center text-white text-3xl md:text-5xl font-bold"
        style="background-image: url('{{ asset('assets/promotion-hero.png') }}')">
        <img alt="Promotion Hero" class="md:h-1/5 h-1/3" src="{{ asset('assets/7am.png') }}">
    </div>

    <flux:container class="space-y-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($promotions as $promotion)
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl card-hover border border-orange-100">
                    <div class="w-full aspect-4/6 bg-cover bg-no-repeat bg-center "
                        style="background-image: url({{ asset('storage/' . $promotion->image) }})">
                    </div>
                    <div class="p-6">
                        <h4 class="font-bold text-xl mb-2">{{ $promotion->title }}</h4>
                        <div class="">{{ $promotion->description }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:container>
</div>
