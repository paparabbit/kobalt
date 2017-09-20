<template>
        <table class="table table-bordered table-striped list-page">
            <thead>
                <tr>
                    <th v-for="(meta, key) in decodedMeta">{{ key }}</th>
                    <th></th>
                </tr>
            </thead>
            <draggable :list="passedList" :options="{disabled:getStatus()}" element="tbody" @end="endDrag" class="">
                <overview-list-item v-for="item in passedList" key="item.id" :item="item" :meta="decodedMeta" :edit_path="edit_path"></overview-list-item>
            </draggable>
        </table>
</template>

<script>
    import draggable from 'vuedraggable';

    export default{

         props: ['list', 'meta', 'edit_path', 'sort_column'],

        data(){
            return{
                // Convert the json back into an array
                // You cannot use a computed prop on this as it wont stay interactive
                passedList: this.sortedList(_.values(JSON.parse(this.list)))
            }
        },
        computed: {
            decodedMeta: function(){
                return JSON.parse(this.meta)
            }
        },


        methods: {
            // This will disable dragging if theres no sort on
            getStatus(){
                if(!_.includes(this.decodeMetaFunc(), 'sort_on')){
                    return true;
                }
                return false;
            },

            endDrag(event){
                this.buildSortOnArray();
            },

            sortedList(a){

                var column

                if(this.sort_column == ''){

                    // Nothing supplied to sort on so guess

                    if(_.includes(this.decodeMetaFunc(), 'sort_on')){
                        // Theres a sort on field
                        column = 'sort_on';

                    }else if(_.includes(this.decodeMetaFunc(), 'published_at')){
                        // Theres a 'date' field
                        column = 'published_at';

                    }else{
                        // Use id
                        column = 'id';
                    }

                }else{
                    column = this.sort_column;
                }

                return _.sortBy(a, function(el){
                    return el[column];
                })
            },

            decodeMetaFunc(){
                return JSON.parse(this.meta);
            },

            buildSortOnArray(){
                var ids = [];

                for ( var i = 0; i < this.passedList.length; ++i) {
                    ids.push(this.passedList[i].id);
                }

                this.postToServer(ids)
            },

            postToServer(ids){

                $.ajax({
                     success: function()
                     {
//                        console.log('>>SUCCESS');
                     },
                     error: function()
                     {
//                        console.log('>>ERROR');
                     },
                     data: {
                         'ids': ids.join(','),
                         'action': 'sortItems',
                     },
                     'type': 'POST',
                     dataType: 'json',
                     url: document.location.href + '/sort'
                 });
            },
        },
        components: {
            draggable
        }
    }
</script>
