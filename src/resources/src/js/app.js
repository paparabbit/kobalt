
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// Add vue components

Vue.component('overview-list', require('./components/Overview_list.vue'));
Vue.component('overview-list-item', require('./components/Overview_list_item.vue'));
Vue.component('image-gallery', require('./components/Image_gallery.vue'));
Vue.component('image-gallery-item', require('./components/Image_gallery_item.vue'));


// ES6 js

import Lightbox from './es6/lightbox';
import FlashMessages from './es6/flash_messages';
import SelectTwo from './es6/select_two';
import DeleteButton from './es6/delete_button';
import SubmitButton from './es6/submit_button';
import Permalinks from './es6/permalinks';



class AdminSystem{

    constructor() {

        let self = this;

        $(function(){
            self.init();
        })
    }



    init(){
        console.log('>>initing');

        this.flash_messages = new FlashMessages();
        this.submit_button = new SubmitButton();

        this.setupSelectTwo();
        this.setupDeleteButton();
        this.setupPermalinks();
        this.setupGalleryComponents();
        this.setupOverviewListComponents();
    }


    setupDeleteButton(){

        if(document.getElementById('delete_button') == null){
            return false;
        }

        this.delete_button = new DeleteButton();
    }


    /* Select 2 is used for the Tagging in blog */
    setupSelectTwo(){

        if(document.getElementById('tag_list') == null){
            return false;
        }

        this.select_two = new SelectTwo();
    }


    setupPermalinks(){

        if(document.querySelector('.permalink_from') == null){
            return false;
        }

        this.permalinks = new Permalinks();
    }



    setupGalleryComponents(){

        if (document.getElementById('sortable-image-gallery') == null){
            return false;
        }

        let parent = this;

        this.sortable_gallery_vue = new Vue({
            el: '#sortable-image-gallery'
        });

        this.lightbox = new Lightbox();

        this.lightbox.on('closedown', function(){
            parent.updateGallery();
        });
    }



    updateGallery() {
        this.sortable_gallery_vue.$refs.image_gallery.pullFromServer();
    }



    setupOverviewListComponents(){

        if(document.getElementById('overviewlist') == null){
            return false;
        }

        var overviewlist_vue = new Vue({
            el: '#overviewlist'
        });
    }


}

// BOOT UP

new AdminSystem();
