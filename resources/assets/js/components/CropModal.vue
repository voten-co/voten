<template>
    <el-dialog
            title="Crop Image"
            :visible="visible"
            :width="isMobile ? '99%' : '45%'"
            @close="close"
            append-to-body
            class="user-select"
    >
        <h2 class="align-center" v-text="loading ? 'Please wait' : 'Please position and size your image'"></h2>

        <div v-show="!loading">
            <img v-bind:src="photo" id="crop" v-show="photo">
        </div>

        <!-- submit -->
        <span slot="footer" class="dialog-footer">
            <el-button type="text" @click="close" size="medium" class="margin-right-1">
                Cancel
            </el-button>

            <el-button type="success" @click="apply" :loading="loading" size="medium">
                Apply
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
                width: 0,
                height: 0,
                x: 0,
                y: 0,
                photo: '',
                loading: true,
                auth,
                Store,
                clientWidth: 0,
                clientHeight: 0,
                naturalWidth: 0,
                naturalHeight: 0
            }
        },

        props: ['type', 'visible'],

        created () {
            this.$eventHub.$on('crop-photo-uploaded', this.getReady)
        },

        methods: {
            getReady(uploaded) {
                this.photo = uploaded

                this.loading = false

                let that = this
                $(document).ready(function () {
                    $("#crop").on('load', function () {
                        that.clientWidth = $(this).width()
                        that.clientHeight = $(this).height()

                        that.naturalWidth = $(this).prop('naturalWidth')
                        that.naturalHeight = $(this).prop('naturalHeight')
                    });
                });

                this.loadCrop()
            },

            loadCrop () {
                this.$nextTick(function () {
                    $('#crop').Jcrop({
                        setSelect: [200, 200, 0, 0],
                        aspectRatio: 1 / 1,
                        onSelect: showCords,
                        onChange: showCords
                    });

                    let self = this

                    function showCords(c) {
                        self.width = c.w
                        self.height = c.h
                        self.x = c.x
                        self.y = c.y
                    }
                })
            },

            apply () {
                this.loading = true
                // Just two aspects to calculate the real pixel numbers
                let horizontal = this.naturalWidth / this.clientWidth
                let vertical = this.naturalHeight / this.clientHeight

                if (this.type == 'user') {

                    axios.post('/user-avatar-crop', {
                        photo: this.photo,

                        width: parseInt(this.width * horizontal),
                        x: parseInt(this.x * horizontal),

                        height: parseInt(this.height * vertical),
                        y: parseInt(this.y * vertical),
                    }).then((response) => {
                        auth.avatar = response.data
                        this.loading = false
                        this.close()
                    });

                } else if (this.type == 'category') {
                    axios.post('/category-avatar-crop', {
                        photo: this.photo,

                        name: Store.page.category.name,

                        width: parseInt(this.width * horizontal),
                        x: parseInt(this.x * horizontal),

                        height: parseInt(this.height * vertical),
                        y: parseInt(this.y * vertical),
                    }).then((response) => {
                        Store.page.category.avatar = response.data
                        this.loading = false
                        this.close()
                    });

                }

            },

            close () {
                location.reload()
                this.$eventHub.$emit('close')
            },
        }
    };

</script>
