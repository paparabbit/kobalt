
export default class TinyMce {

    constructor(){

        tinymce.init({
             selector: '.rich-text',
             plugins: [
                 'paste code, link'
             ],
             toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                style_formats: [
                    { title: 'Intro copy', block: 'p', classes: 'intro-copy' },
                ],
                content_css: [
                 '/css/admin.css',
             ],
         });
    }
}
