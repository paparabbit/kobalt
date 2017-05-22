// import $ from 'jquery';
// import $.magnificPopup from MagnificPopup;
import EventEmitter from 'event-emitter-es6';

export default class Lightbox extends EventEmitter {

    constructor(){
        super();

        // this.gallery_ref = gallery_ref;
        this.setupLightbox();
    }



    setupLightbox(){

        var parent = this;

        // These are added dynamically so hook to an ele we know exists and filter

        $('#sortable-image-gallery').on('click',
            'a.lbox',
            function(event) {

                if (event.isDefaultPrevented()) {
                    return false;
                }

                parent.openLightbox($(this))

                return false;
            }
        )
    }



    openLightbox(item){

        let parent = this;

        $.magnificPopup.open({
            type: 'iframe',
            alignTop: true,
            items: {
                src: item.attr('href') + '?lbox=true'
            },
            iframe: {
                markup: '<div class="mfp-iframe-scaler image-iframe">' +
                '<div class="mfp-close"></div>' +
                '<iframe class="mfp-iframe" id="lightbox-iframe" frameborder="0" allowfullscreen></iframe>' +
                '</div>'
            },
            callbacks: {

                open: function(){
                    $('#lightbox-iframe').on('load', function(){
                        parent.onIframeLoad();
                    })
                },

                close: function () {
                    //!TODO We know there must be a gallery as its the only page we add it on
                    parent.emit('closedown');
                }
            }
        })
    }





    onIframeLoad(){

        // If theres a hard delete button put a marker on it so laravel can deal with it

        if($('#lightbox-iframe').contents().find('.container').hasClass('delete')){

            console.log('>>>WE GOT IT');
            var delete_form = $('#lightbox-iframe').contents().find('#delete_form');
            var action = delete_form.prop('action');

            delete_form.attr('action', action + '?iframe=true');
        };


        // If its the bulk upload form put a marker on it so laravel can close it down
        //!TODO CAN WE JUS?T FIND IT BY ID???

        if($('#lightbox-iframe').contents().find('.container form').hasClass('bulk_upload_form')){

            console.log('>>>WE GOT THE BULK FORM');
            var bulk_form = $('#lightbox-iframe').contents().find('.bulk_upload_form');
            var action = bulk_form.prop('action');
            //
            bulk_form.attr('action', action + '?iframe=true');
        };



        // If there is a back button

        $('#lightbox-iframe').contents().find('#back_button').on('click',function(){

            // Is this the delete page

            if($('#lightbox-iframe').contents().find('.container').hasClass('delete')){
                window.history.back();
            }else{
                $.magnificPopup.close();
            }

            return false;
        });
    }
}