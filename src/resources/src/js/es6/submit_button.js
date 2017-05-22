import $ from 'jquery';

// Once the save button is clicked disable it from being clicked again
// Particularly useful for preventing double clicks on the bulk form
// Todo potentially add this to other submit buttons in the future

export default class{

    constructor(){
        $(document).on('submit', // These can be added dynamically so hook to an ele we know exists and filter
            '.bulk_upload_form',
            function(e){
                $('.bulk_upload_form #save').attr('disabled','disabled');
            }
        );
    }
}
