<template>
    <div id="layer" @click="closeLayer()">
        <div class="panel" v-on:click.stop>
            <form @submit.prevent="addPointToAll">
                <div v-show="!this.loading && !this.success">
                    <div class="form-panel-title">
                        <h3>Add points to all</h3>
                    </div>
                    <div class="fancy-form-group inline">
                        <label for="point_source">Reason:</label>
                        <input id="point_source" type="text" class="form-control" placeholder="added_by_admin" v-model="point.source" required>
                    </div>
                    <div class="fancy-form-group inline">
                        <label for="point_campaign">Campaign:</label>
                        <select id="point_campaign" type="text" class="form-control" v-model="point.campaign_id" required>
                            <option default value="">Válassz ...</option>
                            <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.name}}</option>
                        </select>
                    </div>
                    <div class="fancy-form-group inline">
                        <label for="point_count">Qty:</label>
                        <input id="point_count" type="number" class="form-control" placeholder="0" v-model="point.count" required>
                    </div>
                    <button type="submit" class="btn-rounded btn-outline pull-right">Add points to all!</button>
                </div>

                <div id="loading" v-show="this.loading && !this.success">
                    <loader></loader>
                </div>

                <div id="success" v-if="this.success">
                    <i class="fa fa-check"></i> {{point.count}} points added to {{this.return_points}} users! <br><small>({{this.online_count}} viewers)</small>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user', 'mode', 'instant'],
        data() {
            return {
                points: [],
                campaigns: [],
                point: {
                    campaign_id: 1,
                    source: '',
                    count: '',
                },
                loading: false,
                success: false,
                return_points: 0,
                online_count: 0
            }
        },
        created() {
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
            addPointToAll() {
                //add
                this.loading = true;
                console.log(this.point);
                let url = '';
                if(this.mode === 'online') {
                    url = '/api/points/for_every_online?api_token=' + this.user.api_token;
                } else if(this.mode === 'all') {
                    url = '/api/point/to_all?api_token=' + this.user.api_token;
                }
                fetch(url, {
                    method: 'post',
                    body: JSON.stringify(this.point),
                    headers: {
                        'content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    this.point.campaign_id = '';
                    this.point.source = '';
                    this.return_points = data.count;
                    this.online_count = data.online_count;
                    this.loading = false;
                    this.success = true;
                    setTimeout(function(){
                        this.success = false;
                        this.return_points = 0;
                        this.point.count = 0;
                    }, 2000);
                })
                .catch(err => console.log(err))
            },
            closeLayer() {
                console.log('close...');
                this.$emit('event', false);
            }
        }
    }
</script>