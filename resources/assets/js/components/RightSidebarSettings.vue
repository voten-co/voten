<template>
    <el-dialog
            title="Customize Sidebar"
            :visible="visible"
            :width="isMobile ? '99%' : '550px'"
            @close="close"
            append-to-body
    >
        <div class="user-select">
            <el-form label-position="top" label-width="10px" :model="form">
                <el-form-item label="Limit channel results to:">
                    <el-input-number v-model="form.channelsLimit" :step="1" :min="2" :max="500"></el-input-number>
                </el-form-item>

                <el-form-item label="Filter channels by...">
                    <el-select v-model="form.channelsFilter" placeholder="Filter channels by..." filterable>
                        <el-option v-for="item in filters" :key="item.value" :label="item.description" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>

                <div class="form-toggle no-border">
                    Display channel avatars:
                    <el-switch v-model="form.showChannelAvatars"></el-switch>
                </div>

                <el-form-item label="Sidebar Color">
                    <el-select v-model="form.color" placeholder="Sidebar Color..." filterable>
                        <el-option
                                v-for="item in colors"
                                :key="item"
                                :label="item"
                                :value="item">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
        </div>

        <span slot="footer" class="dialog-footer" v-if="changed">
            <el-button round type="success" @click="save" size="medium">
                Save
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    data() {
        return {
            form: {
                channelsFilter: Store.settings.rightSidebar.channelsFilter,
                channelsLimit: Store.settings.rightSidebar.channelsLimit,
                showChannelAvatars:
                    Store.settings.rightSidebar.showChannelAvatars,
                color: Store.settings.rightSidebar.color
            },

            filters: [
                {
                    value: 'subscribed',
                    description: 'Channels I have subscribed to'
                },
                {
                    value: 'moderating',
                    description: 'Channels I am moderating'
                },
                {
                    value: 'bookmarked',
                    description: 'Channels I have bookmarked'
                }
            ],

            colors: [
                'Blue',
                'Dark Blue',
                'Red',
                'Dark',
                'Gray',
                'Green',
                'Purple'
            ]
        };
    },

    props: ['visible'],

    beforeDestroy() {
        if (window.location.hash == '#sidebarSettings') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'sidebarSettings';
    },

    computed: {
        changed() {
            if (
                Store.settings.rightSidebar.channelsFilter !=
                    this.form.channelsFilter ||
                Store.settings.rightSidebar.channelsLimit !=
                    this.form.channelsLimit ||
                Store.settings.rightSidebar.showChannelAvatars !=
                    this.form.showChannelAvatars ||
                Store.settings.rightSidebar.color != this.form.color
            ) {
                return true;
            }

            return false;
        }
    },

    methods: {
        save() {
            Store.settings.rightSidebar.channelsFilter = this.form.channelsFilter;
            Store.settings.rightSidebar.channelsLimit = this.form.channelsLimit;
            Store.settings.rightSidebar.showChannelAvatars = this.form.showChannelAvatars;
            Store.settings.rightSidebar.color = this.form.color;

            this.close();
        },

        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
