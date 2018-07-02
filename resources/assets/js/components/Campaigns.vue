<template>
    <div>

        <div id="layer" @click="toggleLayer(false)" v-show="this.add_campaign">
            <div class="panel" v-on:click.stop>
                <form @submit.prevent="addCampaign" action="">
                    <div v-show="!this.loading && !this.success">
                        <div class="form-panel-title">
                            <h3>Add Campaign</h3>
                        </div>
                        <div class="fancy-form-group inline">
                            <label for="campaign_name">Name:</label>
                            <input id="campaign_name" type="text" class="form-control" placeholder="Campaign name" v-model="campaign.name" required>
                        </div>
                        <div class="fancy-form-group inline">
                            <label for="currency_name">Campaign:</label>
                            <input id="currency_name" type="text" class="form-control" placeholder="Currency name" v-model="campaign.currency_name">
                        </div>

                        <button type="submit" class="btn-rounded btn-outline pull-right">Add Campaign!</button>
                    </div>

                    <div id="loading" v-show="this.loading && !this.success">
                        <loader></loader>
                    </div>

                    <div id="success" v-if="this.success">
                        <i class="fa fa-check"></i> "{{this.campaign.name}}" succesfully added!
                    </div>
                </form>
            </div>
        </div>

        <nav aria-label="Page navigation" class="page-navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchCampaigns(pagination.prev_page_url)">Previous</a>
                </li>
                <li class="disabled page-item">
                    <a href="#" class="page-link text-dark">
                        page {{pagination.current_page}} of {{pagination.last_page}}
                    </a>
                </li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchCampaigns(pagination.next_page_url)">Next</a>
                </li>
            </ul>
            <ul class="page-actions">
                <li><span @click="toggleLayer(true)" class="btn-rounded btn-outline btn-action">Add campaign</span></li>
            </ul>
        </nav>

        <div v-if="campaigns" class="card card-body" v-for="campaign in campaigns" v-bind:key="campaign.id">
            <h3>{{campaign.name}} ( {{campaign.currency_name}} )</h3>
            <hr>
            <button class="btn btn-danger" @click="deleteCampaign(campaign.id)">Delete</button>
            <button class="btn btn-warning" @click="editCampaign(campaign)">Edit</button>
        </div>

        <div class="info" v-if="!campaigns">Még nincsenek kampányok. Hozz létre egy újat!</div>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchCampaigns(pagination.prev_page_url)">Previous</a>
                </li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a class="page-link" href="#" @click="fetchCampaigns(pagination.next_page_url)">Next</a>
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
                campaigns: false,
                campaign: {
                    id: '',
                    name: '',
                    currency_name: '',
                    status : 'active',
                },
                campaign_id: '',
                loading: false,
                success: false,
                pagination: {},
                edit: false,
                add_campaign : false
            }
        },
        created() {
            this.fetchCampaigns();
        },

        methods: {
            fetchCampaigns(page_url) {
                let vm = this;
                page_url = page_url || '/api/campaigns?api_token=' + this.user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.campaigns = res.data;
                        vm.makePagination(res.meta, res.links);
                        this.edit = false;
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
            deleteCampaign(id) {
                if(confirm('Are you sure?')) {
                    fetch('/api/campaign/'+id+'?api_token='  + this.user.api_token, {
                        method: 'delete'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Campaign removed');
                            this.fetchCampaigns();
                        })
                        .catch(err => console.log(err))
                }
            },
            addCampaign() {
                this.loading = true;
                if(this.edit === false) {
                    //add
                    console.log('adding campaign:');
                    console.log(this.campaign);
                    fetch('/api/campaign?api_token=' + this.user.api_token, {
                        method: 'post',
                        body: JSON.stringify(this.campaign),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.loading = false;
                            this.success = true;

                            setTimeout(function(){
                                this.success = false;
                                this.campaign.name = '';
                                this.add_campaign = false;
                                this.campaign.currency_name = '';
                            }, 2000);

                            this.fetchCampaigns();

                        })
                        .catch(err => console.log(err))
                } else {
                    //update
                    fetch('/api/campaign?api_token=' + this.user.api_token, {
                        method: 'put',
                        body: JSON.stringify(this.campaign),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.campaign.name = '';
                            this.campaign.currency_name = '';
                            alert('Campaign updated');
                            this.fetchCampaigns();
                        })
                        .catch(err => console.log(err))
                }
            },
            editCampaign(campaign) {
                this.edit = true;
                this.campaign.id = campaign.id;
                this.campaign.name = campaign.name;
                this.campaign.currency_name = campaign.currency_name;
            },
            toggleLayer(val){
                this.add_campaign = val;
            }
        }
    }
</script>