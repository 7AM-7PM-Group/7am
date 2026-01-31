@php
    $classes = Flux::classes()->add('mx-auto p-2 md:p-0 w-full [:where(&)]:max-w-7xl');
@endphp

<div {{ $attributes->class($classes) }} data-flux-container>
    {{ $slot }}
</div>
