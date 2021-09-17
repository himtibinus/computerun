@component('components.section-heading', ['title' => 'Our Partners'])
@endcomponent
<h3 class="text-center fw-bold content-divider">SPONSORS</h3>
<div class="content-divider-short text-center placeholder-sponsors margin-1">
    @component('components.sponsorship-invite-message', ['is_empty' => true, 'id' => rand(0,7)])
    @endcomponent
</div>
<h3 class="text-center fw-bold content-divider">MEDIA PARTNERS</h3>
<div class="content-divider-short text-center placeholder-sponsors margin-1">
    @component('components.sponsorship-invite-message', ['is_empty' => true, 'id' => rand(0,7)])
    @endcomponent
</div>
