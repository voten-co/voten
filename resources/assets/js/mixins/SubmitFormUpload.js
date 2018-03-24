export default {
    methods: {
        ////////////////////////////////////////////
        /////////// Photo Upload Methods  //////////
        ////////////////////////////////////////////
        beforePhotoUploadCheckings(file) {
            const isInCorrectFormat =
                file.type === 'image/jpeg' ||
                file.type === 'image/jpg' ||
                file.type === 'image/png';
            const doesNotExceedFileSize =
                file.size / 1024 / 1024 < this.photosSizeLimit;

            if (!isInCorrectFormat) {
                this.$message.error(
                    'Only files with jpg/png formats are allowed! '
                );
            }

            if (!doesNotExceedFileSize) {
                this.$message.error(
                    `Uplaoded photo size can not exceed ${
                        this.photosSizeLimit
                    }mb!`
                );
            }

            return isInCorrectFormat && doesNotExceedFileSize;
        },
        exceededPhotoFileCount(files, fileList) {
            this.$message.error(
                `The limit is ${this.photosNumberLimit}, you selected ${
                    files.length
                } files this time. You may add up to ${this.photosNumberLimit -
                    fileList.length} more files for this post. `
            );
        },
        failedPhotoUpload(err, file, fileList) {
            this.$message.error(err.message);
            this.photos = fileList;
        },
        removePhoto(file, fileList) {
            this.photos = fileList;
        },
        photoPreview(file) {
            this.previewPhotoImage = file.url;
            this.previewPhotoFileName = file.name;
            this.previewPhotoModal = true;
        },
        successfulPhotoUpload(response, file, fileList) {            
            file.id = response.data.id;
            this.photos.push(file);
        },

        /////////////////////////////////////////////
        /////////// GIF Upload Methods  /////////////
        /////////////////////////////////////////////
        // beforeGifUploadCheckings(file) {
        //     const isInCorrectFormat = file.type === 'image/gif';
        //     const doesNotExceedFileSize =
        //         file.size / 1024 / 1024 < this.gifSizeLimit;

        //     if (!isInCorrectFormat) {
        //         this.$message.error(
        //             'Only animated GIF files with .gif format are allowed! '
        //         );
        //     }

        //     if (!doesNotExceedFileSize) {
        //         this.$message.error(
        //             `Uplaoded GIF size can not exceed ${this.gifSizeLimit}mb!`
        //         );
        //     }

        //     return isInCorrectFormat && doesNotExceedFileSize;
        // },
        // exceededGifFileCount(files, fileList) {
        //     this.$message.error(
        //         `You can only Upload one GIF file per submission.`
        //     );
        // },
        // failedGifUpload(err, file, fileList) {
        //     this.$message.error(err.message);
        //     this.gif_id = null;
        // },
        // removeGif(file, fileList) {
        //     this.gif_id = null;
        // },
        // gifPreview(file) {
        //     this.previewGifImage = file.url;
        //     this.previewGifFileName = file.name;
        //     this.previewGifModal = true;
        // },
        // successfulGifUpload(response, file, fileList) {
        //     this.gif_id = response.data.id;
        // }
    }
};
