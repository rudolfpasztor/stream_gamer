<template>

    <div>

        <add_point_panel :user="this.auth_user" :mode="this.mode" v-show="this.add_points" @event="panelHandler"></add_point_panel>

        <h2>Users</h2>

        <autocomplete @clicked="onClickChild"></autocomplete>

        <form @submit.prevent="addUser" action="" class="mb-3" style="display: none;">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="user_id" v-model="user.user_id">
            </div>
            <div class="form-group">
                <input type="number" class="form-control" placeholder="0" v-model="user.count">
            </div>
            <button type="submit" class="btn btn-light">Add</button>
        </form>

        <nav aria-label="Page navigation" class="page-navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchUsers(pagination.prev_page_url)">Previous</a>
                </li>
                <li class="disabled page-item">
                    <a href="#" class="page-link text-dark">
                        page {{pagination.current_page}} of {{pagination.last_page}}
                    </a>
                </li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchUsers(pagination.next_page_url)">Next</a>
                </li>
            </ul>

            <ul class="page-actions">
                <li><span @click="instantPoints(15, 'online')" class="btn-rounded btn-outline btn-action">+15</span></li>
                <li><span @click="instantPoints(30, 'online')" class="btn-rounded btn-outline btn-action">+30</span></li>
                <li><span @click="togglePointPanel(true, 'online')" class="btn-rounded btn-outline btn-action">Add point to all online</span></li>
                <li><span @click="togglePointPanel(true, 'all')" class="btn-rounded btn-outline btn-action">Add point to all</span></li>
            </ul>
        </nav>

        <!-- user flow -->

        <div id="point_flow">
            <div v-if="users" class="point-flow-item d-flex flex-row" v-for="user in users" v-bind:key="user.id">
                <div class="point">
                    <span class="count" v-if="user.sum_points">{{user.sum_points}}</span>
                    <span class="count" v-else>0</span>

                    <span class="currency"></span>
                </div>
                <div class="details">
                    <span class="user-name">{{user.nick}} <span class="id no-select">(#{{user.id}})</span> <span class="btn-small">add point</span></span>
                    <span class="meta">{{user.created_at}}</span>
                </div>
                <div class="actions">
                    <span class="settings"><i class="fas fa-cog"></i></span>
                    <span class="action action-edit btn" @click="editUser(user)"><i class="far fa-edit"></i></span>
                    <span class="action action-delete btn" @click="deleteUser(user.id)"><i class="far fa-trash-alt"></i></span>
                </div>
            </div>
            <div v-else class="info">Még nincsenek usereink :(</div>
        </div>



        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchUsers(pagination.prev_page_url)">Previous</a>
                </li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchUsers(pagination.next_page_url)">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</template>


<script>
    export default {
        props: ['auth_user'],
        data() {
            return {
                users: false,
                user: {
                    id: '',
                    nick: '',
                    twitch_id: '',
                    email: '',
                    foreign_user_id: ''
                },
                end_user_id: '',
                pagination: {},
                mode: 'all',
                edit: false,
                add_points : false,
            }
        },

        created() {
            this.fetchUsers();
        },

        methods: {
            fetchUsers(page_url) {
                let vm = this;
                page_url = page_url  || '/api/end_users?api_token=' + this.auth_user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.users = res.data;
                        console.log(res.data);
                        vm.makePagination(res);
                    })
                    .catch(err => console.log(err))
            },
            makePagination(res) {
                let pagination = {
                    current_page: res.current_page,
                    last_page: res.last_page,
                    next_page_url : res.next_page_url + '&api_token=' + this.auth_user.api_token,
                    prev_page_url: res.prev_page_url + '&api_token=' + this.auth_user.api_token
                }

                this.pagination = pagination;
            },
            instantPoints(_count, mode) {
                let point = {
                        campaign_id: 1,
                        source: 'extra pont',
                        count: _count,
                    };
                let url = '';
                if(mode === 'online') {
                    url = '/api/points/for_every_online?api_token=' + this.auth_user.api_token;
                } else if(mode === 'all') {
                    url = '/api/point/to_all?api_token=' + this.auth_user.api_token;
                }
                fetch(url, {
                    method: 'post',
                    body: JSON.stringify(point),
                    headers: {
                        'content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        alert( data.status + '! ' + data.status_msg );
                    })
                    .catch(err => console.log(err))
            },
            deleteUser(id) {
                if(confirm('Are you sure?')) {
                    fetch('/api/end_user/'+id+'?api_token=' + this.auth_user.api_token, {
                        method: 'delete'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('User removed');
                            this.fetchUsers();
                        })
                        .catch(err => console.log(err))
                }
            },
            addUser() {
                if(this.edit === false) {
                    //add
                    fetch('/api/end_user?api_token=' + this.auth_user.api_token, {
                        method: 'post',
                        body: JSON.stringify(this.user),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.user.id = '';
                            this.user.nick = '';
                            this.user.twitch_id = '';
                            this.user.foreign_user_id = '';
                            this.user.email = '';
                            alert('User added');
                            this.fetchUsers();
                        })
                        .catch(err => console.log(err))
                } else {
                    //update
                    fetch('/api/end_user?api_token=' + this.auth_user.api_token, {
                        method: 'put',
                        body: JSON.stringify(this.user),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.user.id = '';
                            this.user.nick = '';
                            this.user.twitch_id = '';
                            this.user.foreign_user_id = '';
                            this.user.email = '';
                            alert('User updated');
                            this.fetchUsers();
                        })
                        .catch(err => console.log(err))
                }
            },
            fetchSingleUser(_user) {
                fetch('/api/end_user/'+_user+'?api_token=' + this.auth_user.api_token)
                    .then(res => res.json())
                    .then(res => {
                        this.users = res;
                        console.log('fetched');
                        console.log(res.data);
                    })
                    .catch(err => console.log(err))
            },
            onClickChild(value) {
              console.log('child clicked');
              console.log(value);
              this.fetchSingleUser(value);
            },
            panelHandler(params, mode) {
                this.togglePointPanel(params, mode);
                this.fetchUsers();
            },
            togglePointPanel(_val, mode) {
              this.add_points = _val;
              this.mode = mode;
            },
            editUser(user) {
                this.edit = true;
                this.user.id = user.id;
                this.user.email = user.email;
                this.user.nick = user.nick;
                this.user.email = user.email;
                this.user.twitch_id = user.twitch_id;
                this.user.foreign_user_id = user.foreign_user_id;
            }
        }
    }
</script>