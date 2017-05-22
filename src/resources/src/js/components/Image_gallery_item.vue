<template>
    <li>
        <span><img :src="image_data"></span>
        <a :href="buildUrl()" class="edit lbox">Edit</a>
    </li>
</template>

<script>
    export default{

        props : ['id','image_data','editimage_path'],

        methods: {
            buildUrl(){
                return this.editimage_path + this.id + '/edit';
            }
        }
    }
</script>
