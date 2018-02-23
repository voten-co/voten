<template>
    <section class="banned-user-wrapper">
        <div class="banned-user">
            <div class="left">
                <markdown :text="list.body"></markdown>
            </div>

            <div class="actions">
                <el-tooltip content="Edit" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-edit h-primary" @click="$emit('edit-rule', list)"></i>
                </el-tooltip>

                <el-tooltip content="Delete" placement="top" transition="false" :open-delay="500">
                    <i class="pointer v-icon go-gray v-delete h-red"
                       @click="$emit('delete-rule', list.id, list.channel_id)"></i>
                </el-tooltip>
            </div>
        </div>
    </section>
</template>

<script>
import Markdown from '../components/Markdown.vue';

export default {
    components: { Markdown },

    data() {
        return {
            showDescription: false
        };
    },

    props: ['list']
};
</script>
