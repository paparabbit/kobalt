<template>
    <tr>
        <td v-for="(meta_name, key) in meta">
            <template v-if="meta_name == 'sort_on'">
                <span class="sort-on">{{ item['sort_on'] }}</span>
            </template>
            <template v-else-if="meta_name == 'is_active' || meta_name == 'is_published'">
                {{ convertStatus(item[meta_name]) }}
            </template>
            <template v-else-if="meta_name.toLowerCase().includes('date')">
                {{ convertDate(item[meta_name].date) }}
            </template>
            <template v-else-if="key.toLowerCase().includes('date')">
                {{ convertDate(item[meta_name].date) }}
            </template>
            <template v-else>
                {{ item[meta_name] }}
            </template>
        </td>
        <td v-if="editable">
            <a :href="buildUrl()" class="btn btn-primary">Edit</a>
        </td>
        <td v-if="showable">
            <a class="btn btn-primary">Show</a>
        </td>
    </tr>
</template>

<script>
    import moment from 'moment';

    export default{

        props: ['item', 'meta', 'edit_path', 'editable', 'showable'],

        methods: {
            buildUrl(){
                return this.edit_path + '/' + this.item.id + '/edit';
            },
            convertDate(date){
                return moment(date).format("Do MMM YYYY");
            },
            convertStatus(status){
                return status == 1 ? 'Yes' : 'No';
            }
        },
        components: {
            moment
        }
    }
</script>
