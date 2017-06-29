<template>
    <div class="profile-avatar user-profile">
        <button type="button" class="avatar-preview">
            <img v-bind:alt="name" v-bind:src="avatar" v-show="avatar" />

            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" v-if="uploading"></i>

            <span class="update square">
                <i class="v-icon flaticon-photo-camera" aria-hidden="true"></i>
                Upload photo
                <input type="file" id="fileUploadFile" @change="bindFile" >
            </span>
        </button>
    </div>
</template>

<script>
    export default {

        props: ['avatar', 'name'],

        data: function () {
            return {
                uploading: false,
                fileUploadFormData: new FormData(),
            }
        },

        methods: {

            bindFile: function (e) {
                this.fileUploadFormData.append('photo', e.target.files[0]);
                this.fileUploadFormData.append('name', this.name);
                this.avatar = '';
                this.uploading = true;
                this.upload();
            },

            upload: function (e) {
                axios.post('/category-avatar', this.fileUploadFormData).then((response) => {
                    this.avatar = response.data;
                    this.uploading = false;
                });
            }
        },

    }

</script>
