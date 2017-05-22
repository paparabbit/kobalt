import $ from 'jquery';
import 'select2';

export default class SelectTwo{

    constructor(){
        $('#tag_list').select2({
            placeholder: 'Choose a tag',
            tags: true,
            tokenSeparators: [",", " "],
            createTag: function (newTag) {
                if (newTag.term) {
                    return {
                        id: 'new:' + newTag.term,
                        text: newTag.term + ' (new)'
                    };
                }
            }
        });
    }
}
