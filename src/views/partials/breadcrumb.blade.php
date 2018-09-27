<ol class="breadcrumb bg-light">
    @if ($depth == 1)
        <li class="breadcrumb-item active">List {{ str_plural($title) }}</li>
    @else
        @if($back_link != null)
            <li class="breadcrumb-item">
                <a href="{{ $back_link }}">Back to {{  $back_title != '' ? str_plural($back_title) : str_plural($title) }}</a>
            </li>
        @endif
        <li class="breadcrumb-item active">{{ $button_text. ' ' .$title }}</li>
    @endif
</ol>