
<div id='sortable-image-gallery'>
    <image-gallery
        ref="image_gallery"
        resource_id = "{{ $resource_id }}"
        list = "{{ $images }}"
        editimage_path = "{{ $editimage_path }}">
    </image-gallery>

    {{-- Add image button --}}

    <a href="{{ $addimage_action }}" class="btn btn-primary btn-outline lbox">Add image</a>


    {{-- Bulk Add image button --}}

    @if( isset($bulk_addimage_action))

        <a href="{{ $bulk_addimage_action }}" class="btn btn-primary btn-outline lbox">Bulk add images</a>

    @endif
</div>