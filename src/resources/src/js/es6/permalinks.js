import $ from 'jquery';

export default class Permalinks {

    constructor(){

        this.permalinkFrom = $('.permalink_from');
        this.permalinkTo = $('.permalink_to');

        if (this.permalinkFrom.length && this.permalinkTo.length) {
            this.monitorFields();
        }
    }


    monitorFields(){

        let fromVal = this.permalinkFrom.val().toLowerCase().split(' ').join('-');
        let parent = this;

        this.permalinkFrom.bind(
            'keyup',
            function()
            {
                let toVal = parent.permalinkTo.val()
                let synced = toVal == '' || toVal == parent.fromVal;

                parent.fromVal = parent.permalinkFrom.val().toLowerCase().split(' ').join('-');
                if (synced) {
                    parent.permalinkTo.val(parent.fromVal);
                }
            }
        );
    }
}
