import $ from 'jquery';

export default class DeleteButton {

    constructor(){

        $('#delete_button').on('click', function(){

            var delete_form = $('input[value="DELETE"]').parent();
            delete_form.submit();

            // parent.$.magnificPopup.close();

            return false;
        })
    }
}
