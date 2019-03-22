<template>

    <draggable :list="passedList" handle="{disabled:getStatus()}" tag="ul" @end="endDrag" class="has-many-uniquely">
        <image-gallery-item v-for="item in passedList" class="has-many-uniquely"
            :key="item.id"
            :id=item.id
            :image_data=item.thumbnail_path
            :editimage_path="editimage_path">
        </image-gallery-item>
    </draggable>

</template>

<script>
    import draggable from 'vuedraggable';

    export default {

        props: ['list','resource_id','editimage_path'],

        data: function(){
            return{
                // Convert the json back into an array
                // You cannot use a computed prop on this as it wont loop through
                passedList: this.sortedList(_.values(JSON.parse(this.list)))
            }
        },

        mounted: function(){
            /*Events.$on('eventFired', () => {
                alert('event was heard')
            });*/
        },

        methods: {
            // This will disable dragging if theres no sort on
            getStatus(){
                if(!_.includes(this.decodeListFunc(), 'sort_on')){
                    return true;
                }
                return false;
            },

            endDrag(event){
                this.buildSortOnArray();
            },

            addThing: function(a){
                this.passedList.push(a)
            },


            sortedList: function(a){
                return _.sortBy(a, function(el){
                  return el.sort_on;
                })
            },

            buildSortOnArray(){
                var ids = [];

                for ( var i = 0; i < this.passedList.length; ++i) {
                    ids.push(this.passedList[i].id);
                }

                this.postToServer(ids)
            },

            decodeListFunc(){
                return JSON.parse(this.list);
            },

            pullFromServer(){
                console.log('>>PULLING');
                var parent = this;
                $.getJSON({
                     success: function(data)
                    {
                        //console.log('>>HELLO')
                        //console.log(parent.sortedList(_.values(data)));
                        parent.passedList = parent.sortedList(_.values(data))
                     },
                     error: function(e)
                     {
                        console.log('>>FAIL')
                     },
                     data: {
                         'resource_id': this.resource_id,
                         'action': 'whatdowecallthis',
                     },
                     'type': 'POST',
                     dataType: 'json',
                     url: document.location.href + '/imageload'
                 });

            },


            postToServer(ids){
                $.ajax({
                     success: function(data)
                    {
                     },
                     error: function(e)
                     {
                     },
                     data: {
                         'ids': ids.join(','),
                         'action': 'sortItems',
                     },
                     'type': 'POST',
                     dataType: 'json',
                     url: document.location.href + '/imagesort'
                 });
            },
        },

        components: {
            draggable
        }
    }
</script>
