const state = {
    user: null,
    userStatus: null,
};

const getters = {
    user: state => {
        return state.user;
    },
    friendship: state => {
        return state.user.data.attributes.friendship;
    },
    friendButtonText: (state, getters, rootState) => {
        if (getters.friendship === null) {
            return 'Add Friend';
        } else if (getters.friendship.data.attributes.confirmed_at === null) {
            return 'Pending Friend Request';
        }
    }
};

const actions = {
    fetchUser({commit, dispatch}, userId) {
        commit('setUserStatus', 'loading');

        axios.get('/api/users/' + userId)
            .then(res => {
                commit('setUser', res.data);
                commit('setUserStatus', 'success');
            })
            .catch(error => {
                commit('setUserStatus', 'error');
            });
    },
    sendFriendRequest({commit, state}, friendId) {
        commit('setButtonText', 'Loading');

        axios.post('/api/friend-request', { 'friend_id': friendId })
            .then(res => {
                commit('setUserFriendship', res.data);
            })
            .catch(error => {
            });
    },
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
    setButtonText(state, text) {
        state.friendButtonText = text;
    }
};

export default {
    state, getters, actions, mutations,
}
