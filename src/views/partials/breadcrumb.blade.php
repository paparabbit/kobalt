<ol class="breadcrumb">
    @if ($depth == 1)
        <li class="active">List {{ str_plural($title) }}</li>
    @else
        <li>
                @if($back_link != null)
                        <a href="{{ $back_link }}">Back to {{  $back_title != '' ? str_plural($back_title) : str_plural($title) }}</a>
                @endif
        </li>
        <li class="active">{{ $button_text. ' ' .$title }}</li>
    @endif
</ol>