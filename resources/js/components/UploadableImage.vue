<template>
    <div>
        <img :src="imageObject.data.attributes.path"
             :alt="alt"
             ref="userImage"
             :class="classes">
    </div>
</template>

<script>
    import Dropzone from 'dropzone';

    export default {
        name: "UploadableImage",

        props: [
            'userImage',
            'imageWidth',
            'imageHeight',
            'location',
            'classes',
            'alt',
        ],

        data: () => {
            return {
                dropzone: null,
                uploadedImage: null,
            }
        },

        mounted() {
            this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
        },

        computed: {
            settings() {
                return {
                    paramName: 'image',
                    url: '/api/user-images',
                    acceptedFiles: 'image/*',
                    params: {
                        'width': this.imageWidth,
                        'height': this.imageHeight,
                        'location': this.location,
                    },
                    headers: {
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                    },
                    success: (e, res) => {
                        this.uploadedImage = res;
                    }
                };
            },

            imageObject() {
                return this.uploadedImage || this.userImage;
            }
        }
    }
</script>

<style scoped>

</style>
