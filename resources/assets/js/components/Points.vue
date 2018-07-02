<template>
    <div>
        <h2>Point flow ~</h2>

        <form @submit.prevent="addPoint" action="" class="mb-3" style="display: none;">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="user_id" v-model="point.end_user_id">
            </div>
            <div class="form-group">
                <select type="text" class="form-control" v-model="point.campaign_id">
                    <option default :value="undefined">Select campaign</option>
                    <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.name}}</option>
                </select>
            </div>
            <div class="form-group">
                <input type="number" class="form-control" placeholder="0" v-model="point.count">
            </div>
            <button type="submit" class="btn btn-light">Add</button>
        </form>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchPoints(pagination.prev_page_url)">Previous</a>
                </li>
                <li class="disabled page-item">
                    <a href="#" class="page-link text-dark">
                        page {{pagination.current_page}} of {{pagination.last_page}}
                    </a>
                </li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchPoints(pagination.next_page_url)">Next</a>
                </li>
            </ul>
        </nav>

        <!-- point flow -->

        <div id="point_flow">
            <div v-if="points" class="point-flow-item d-flex flex-row" v-for="point in points" v-bind:key="point.id">
                <div class="point">
                    <span class="count">{{point.count}}</span>
                    <span class="currency">{{point.campaign.currency_name}}</span>
                </div>
                <div class="details">
                    <span class="user-name" v-if="point.owner">{{point.owner.nick}}<span class="id no-select">(#{{point.end_user_id}})</span> <span class="btn-small">add point</span></span>
                    <span class="user-name" v-else>Törölt felhasználó<span class="id no-select">(#{{point.end_user_id}})</span> <span class="btn-small">add point</span></span>

                    <span class="meta">{{point.created_at}} | {{point.source}}</span>
                </div>
                <div class="actions">
                    <span class="settings"><i class="fas fa-cog"></i></span>
                    <span class="btn action action-edit" @click="editPoint(point)"><i class="far fa-edit"></i></span>
                    <span class="btn action action-delete" @click="deletePoint(point.id)"><i class="far fa-trash-alt"></i></span>
                </div>
            </div>
        </div>


        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchPoints(pagination.prev_page_url)">Previous</a>
                </li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchPoints(pagination.next_page_url)">Next</a>
                </li>
            </ul>
        </nav>

    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                points: false,
                campaigns: [],
                point: {
                    id: '',
                    end_user_id: '',
                    campaign_id: '',
                    source: 'given_by_admin',
                    count: '',
                },
                point_id: '',
                pagination: {},
                edit: false
            }
        },

        created() {
            this.fetchPoints();
            this.fetchCampaigns();
        },

        methods: {
            fetchCampaigns() {
                fetch('api/campaigns?api_token=' + this.user.api_token)
                    .then(res => res.json())
                    .then(res => {
                        this.campaigns = res.data;
                        console.log(this.campaigns);
                    })
                    .catch(err => console.log(err))
            },
            fetchPoints(page_url) {
                let vm = this;
                page_url = page_url || '/api/points?api_token=' + this.user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.points = res.data;
                        console.log(res.data);
                        vm.makePagination(res.meta, res.links);
                    })
                    .catch(err => console.log(err))
            },
            makePagination(meta, links) {
                let pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url : links.next + '&api_token=' + this.user.api_token,
                    prev_page_url: links.prev + '&api_token=' + this.user.api_token
                }

                this.pagination = pagination;
            },
            deletePoint(id) {
                if(confirm('Are you sure?')) {
                    fetch('/api/point/'+id+'?api_token=' + this.user.api_token, {
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert('Point removed');
                        this.fetchPoints();
                    })
                    .catch(err => console.log(err))
                }
            },
            addPoint() {
                console.log('Adding point:');
                console.log(this.point);
                if(this.edit === false) {
                    //add
                    fetch('/api/point?api_token=' + this.user.api_token, {
                        method: 'post',
                        body: JSON.stringify(this.point),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.point.end_user_id = '';
                        this.point.count = '';
                        //todo: add currency and campaign id
                        alert('Point added');
                        this.fetchPoints();
                    })
                    .catch(err => console.log(err))
                } else {
                    //update
                    fetch('/api/point?api_token=' + this.user.api_token, {
                        method: 'put',
                        body: JSON.stringify(this.point),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.point.end_user_id = '';
                            this.point.campaign_id = '';
                            this.point.count = '';
                            //todo: add currency and campaign id
                            alert('Point updated');
                            this.fetchPoints();
                        })
                        .catch(err => console.log(err))
                }
            },
            editPoint(point) {
                this.edit = true;
                this.point.id = point.id;
                this.point.end_user_id = point.end_user_id;
                this.point.campaign_id = point.campaign_id;
                this.point.source = point.source;
                this.point.count = point.count;
            }
        }
    }
</script>