<div class="faq">
    @foreach($contents as $content)
        <div class="box w-100">
            <div class="top setbox">
                <div class="left setbox fw-bold">
                    <span class="">Q</span>
                    <div>{{ $content['question'] }}</div>
                </div>
                <div class="right" onclick="slide(this)">
                    <div class="min"></div>
                    <div class="plus"></div>
                </div>
            </div>
            <div class="bot setbox">
                <div class="left">
                    <span>A</span>
                    <div>
                        @foreach($content['answer'] as $widget)
                            <div class="col-12 {{ $class ?? '' }}">
                                @component('functions.parse-widget', ['widget' => $widget])
                                @endcomponent
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="right">
                    <div class="min"></div>
                    <div class="plus"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>
