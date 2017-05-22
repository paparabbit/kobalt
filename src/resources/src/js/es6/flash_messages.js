import $ from 'jquery';

export default class FlashMessages {

    constructor(){
        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    }
}