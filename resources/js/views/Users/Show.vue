<template>
    <div class="flex flex-col items-center">
        <div class="relative mb-8">
            <div class="w-100 h-64 overflow-hidden z-10">
                <img src="https://cdn.pixabay.com/photo/2017/03/26/12/13/countryside-2175353_960_720.jpg" alt="user background image" class="object-cover w-full">
            </div>

            <div class="absolute flex items-center bottom-0 left-0 -mb-8 ml-12 z-20">
                <div class="w-32">
                    <img src="https://cdn.pixabay.com/photo/2014/07/09/10/04/man-388104_960_720.jpg" alt="user profile image" class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg">
                </div>

                <p class="text-2xl text-gray-100 ml-4">{{ user.data.attributes.name }}</p>
            </div>

            <div class="absolute flex items-center bottom-0 right-0 mb-4 mr-12 z-20">
                <button class="py-1 px-3 bg-gray-400 rounded"
                        @click="$store.dispatch('sendFriendRequest', $route.params.userId)">
                    {{ friendButtonText }}
                </button>
            </div>
        </div>

        <p v-if="postLoading">Loading posts...</p>

        <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post" />

        <p v-if=" ! postLoading && posts.data.length < 1">No posts found. Get started...</p>
    </div>
</template>

<script>
    import Post from '../../components/Post';
    import { mapGetters } from 'vuex';

    export default {
        name: "Show",

        components: {
            Post
        },

        data: () => {
            return {
                posts: null,
                postLoading: true,
            }
        },

        mounted() {
            this.$store.dispatch('fetchUser', this.$route.params.userId);

            axios.get('/api/users/' + this.$route.params.userId + '/posts')
                .then(res => {
                    this.posts = res.data;
                })
                .catch(error => {
                    console.log('Unable to fetch posts');
                })
                .finally(() => {
                    this.postLoading = false;
                });
        },

        computed: {
            ...mapGetters({
                user: 'user',
                friendButtonText: 'friendButtonText',
            }),
        }
    }
</script>

<style scoped>

</style>
