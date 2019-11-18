<template>
    <div>
        <img src="https://cdn.pixabay.com/photo/2017/03/26/12/13/countryside-2175353_960_720.jpg"
             alt="user background image"
             ref="userImage"
             class="object-cover w-full">
    </div>
</template>

<script>
    import Dropzone from 'dropzone';

    export default {
        name: "UploadableImage",

        props: [
            'imageWidth',
            'imageHeight',
            'location',
        ],

        data: () => {
            return {
                dropzone: null,
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
                        alert('uploaded!');
                    }
                };
            }
        }
    }
</script>

<style scoped>

</style>
