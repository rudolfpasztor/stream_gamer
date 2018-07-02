<template>
    <div>
        <h2 class="page-title">Drops</h2>

        <div class="layer" @click="showTickets(false)" v-show="this.show_tickets">
            <div class="panel to-top" v-on:click.stop>
                    <h3>Tickets ({{this.show_tickets.length}}db)</h3>
                    <div class="tickets-container">
                        <div class="ticket" v-for="ticket in this.show_tickets">
                            {{ticket.nick}}
                        </div>
                    </div>
            </div>
        </div>

        <div class="layer" @click="toggleLayer(false)" v-show="this.add_drops">
            <div class="panel" v-on:click.stop>
                <form @submit.prevent="addDrop" action="">
                    <div v-show="!this.loading && !this.success">
                        <div class="form-panel-title">
                            <h3 v-if="this.edit">Edit drop</h3>
                            <h3 v-else>Add drops</h3>
                        </div>
                        <div class="fancy-form-group inline">
                            <label for="campaign_name">Name:</label>
                            <input id="campaign_name" type="text" class="form-control" placeholder="Drop name" v-model="drop.name" required>
                        </div>
                        <div class="fancy-form-group inline">
                            <label for="point_campaign">Campaign:</label>
                            <select id="point_campaign" type="text" class="form-control" v-model="drop.campaign_id" required>
                                <option default value="">Válassz ...</option>
                                <option v-for="campaign in campaigns" :value="campaign.id">{{campaign.name}}</option>
                            </select>
                        </div>
                        <div class="fancy-form-group inline">
                            <label for="img_url">Kép url:</label>
                            <input id="img_url" type="text" class="form-control" placeholder="Http://example.com/img.jpg" v-model="drop.image_url">
                        </div>

                        <div class="fancy-form-group inline">
                            <label for="price">Ár:</label>
                            <input id="price" type="number" min="0" class="form-control" placeholder="0" v-model="drop.price">
                        </div>

                        <div class="fancy-form-group inline">
                            <label for="question_gate">Question gate:</label>
                            <select id="question_gate" class="form-control" v-model="drop.question_id" required>
                                <option default value="">Válassz ...</option>
                                <option v-for="question in questions" :value="question.id">{{question.question}}</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-rounded btn-outline pull-right" v-if="this.edit">Save Drop!</button>
                        <button type="submit" class="btn-rounded btn-outline pull-right" v-else>Add Drop!</button>
                    </div>

                    <div id="loading" v-show="this.loading && !this.success">
                        <loader></loader>
                    </div>

                    <div id="success" v-if="this.success">
                        <i class="fa fa-check"></i> "{{this.drop.name}}" succesfully added!
                    </div>
                </form>
            </div>
        </div>

        <nav aria-label="Page navigation" class="page-navigation">
            <ul class="pagination">

            </ul>

            <ul class="page-actions">
                <li><span @click="toggleLayer(true)" class="btn-rounded btn-outline btn-action">Add new</span></li>
                <li><span @click="activateAll()" class="btn-rounded btn-outline btn-action">Activate all</span></li>
                <li><span @click="deactivateAll()" class="btn-rounded btn-outline btn-action">Passivate all</span></li>
            </ul>
        </nav>

        <div id="drops" class="">
            <div class="drop-container">
                <div class="drop" v-if="drops"  v-for="drop in drops" v-bind:key="drop.id" v-bind:id="'drop-' + drop.id">
                 <span class="tickets" @click="showTickets(drop)">
                    x{{drop.ticket_count.length}}
                </span>
                    <div class="d-flex flex-column">
                    <span class="img">
                        <span class="edit-drop" @click="editDrop(drop)"><i class="icon-note"></i></span>
                        <img v-bind:src="drop.image_url" alt="">
                    </span>
                        <div class="datas d-flex flex-column">
                            <h4>{{drop.name}}</h4>
                            <span class="price">
                                <span class="ttl">Jegy Ár:</span>
                                <span class="coin-small"></span>
                                <span class="count">{{drop.price}}</span>
                            </span>
                            <span class="question-gate">
                                <i>Question Gate:</i>
                                {{drop.question_gate.question}}
                            </span>
                            <span v-bind:class="'status status-' + drop.status">{{drop.status}}</span>

                            <ul class="actions" v-if="drop.winners == 0">
                                <li v-if="drop.status === 'passive' && drop.ticket_count.length > 0"><span @click="shuffle(drop.ticket_count, drop)" class="btn-rounded btn-outline btn-action">Sorsolás</span></li>
                                <li v-if="drop.status === 'passive' && drop.winners == 0"><span @click="activateDrop(drop)" class="btn-rounded btn-outline btn-action">Aktiválás</span></li>
                                <li v-if="drop.status === 'active'"><span @click="deactivateDrop(drop)" class="btn-rounded btn-outline btn-action">Deaktiválás</span></li>
                            </ul>
                            <a v-bind:href="'/caspar/winner/' + JSON.parse(drop.winners).nick" target="_blank" class="winner" v-else-if="drop.ticket_count.length > 0">
                                <small>Nyertes:</small>
                                {{JSON.parse(drop.winners).nick}}
                                <small>Kattints ide a Caspar linkért</small>
                            </a>

                        </div>

                    </div>
                </div>
                <div v-else class="info">
                    Még nincsenek dropok, adj hozzá egyet
                </div>
            </div>
        </div>


    </div>
</template>

<script>
    export default {
        props: ['auth_user'],
        data() {
            return {
                drops: [],
                questions: [],
                campaigns: [],
                drop: {
                    id: '',
                    name: '',
                    campaign_id: '',
                    qty: 1,
                    question_id: '',
                    price: '',
                    image_url: '',
                    winners: false
                },
                drop_id: '',
                pagination: {},
                edit: false,
                add_drops : false,
                loading: false,
                success: false,
                show_tickets: false,
            }
        },
        created() {
            console.log('created');
            this.fetchDrops();
            this.fetchCampaigns();
            this.fetchQuestions();
            //this.listen();
        },
         mounted() {
            console.log('mounted');

        },

        methods: {
            listen(){
              console.log('listening');

              Echo.channel('drops-channel')
                  .listen('NewTicket', (tickets) => {
                      alert('New Ticket, mate!');
                      console.log(tickets);
                  })
            },
            fetchCampaigns() {
                fetch('api/campaigns?api_token=' + this.auth_user.api_token)
                    .then(res => res.json())
                    .then(res => {
                        this.campaigns = res.data;
                        console.log(this.campaigns);
                        this.loading = false;
                        this.success = false;
                        this.edit = false;
                    })
                    .catch(err => console.log(err))
            },
            fetchQuestions() {
                let page_url = '/api/questions?api_token=' + this.auth_user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        for(let i = 0; i < res.data.length; i++){
                            let tmp = JSON.parse(res.data[i].answers);
                            res.data[i].answers = tmp;
                        }
                        this.questions = res.data;
                        console.log(res.data);
                    })
                    .catch(err => console.log(err))
            },
            fetchDrops() {
                let vm = this;
                let page_url = '/api/drops?api_token=' + this.auth_user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.drops = res.data;
                        console.log(res.data);
                    })
                    .catch(err => console.log(err))
            },
            shuffle(obj, drop) {
                console.log('-------------');
                let random_pos = Math.floor(Math.random()*obj.length);
                console.log('winner position is: ' + random_pos);
                let array = Object.values(obj);
                let winner = obj[random_pos];

                this.drop.winners = winner;
                console.log('-------------');
                console.log('winner is:');
                console.log(this.drop.winners);
                console.log('-------------');

                this.drop.campaign_id = drop.campaign_id;
                this.drop.id = drop.id;
                this.drop.image_url = drop.image_url;
                this.drop.name = drop.name;
                this.drop.price = drop.price;
                this.drop.qty = 1;
                this.drop.question_id = drop.question_id;
                this.drop.status = drop.status;
                this.drop.winners = JSON.stringify(this.drop.winners);
                console.log('Updating drop:');
                console.log(this.drop);

                fetch('/api/drop?api_token=' + this.auth_user.api_token, {
                    method: 'put',
                    body: JSON.stringify(this.drop),
                    headers: {
                        'content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.edit = false;
                        this.drop.name = '';
                        this.drop.campaign_id = '';
                        this.drop.price = '';
                        this.drop.qty = 1;
                        this.drop.question_id = '';
                        this.drop.image_url = '';
                        this.drop.winners = false;
                        alert('winner updated');
                        this.fetchDrops();
                    })
                    .catch(err => console.log(err))

            },
            showTickets(drop) {
                console.log(drop);
                if(!drop) {
                    this.show_tickets = false;
                } else {
                    this.show_tickets = drop.ticket_count;
                }
            },
            activateDrop(drop) {
                if(confirm('Are you sure?')) {
                    fetch('/api/drop/activate/'+drop.id+'?api_token=' + this.auth_user.api_token, {
                        method: 'get'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Drop aktiválva');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            deactivateDrop(drop) {
                if(confirm('Are you sure?')) {
                    fetch('/api/drop/deactivate/'+drop.id+'?api_token=' + this.auth_user.api_token, {
                        method: 'get'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Drop aktiválva');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            activateAll() {
                if(confirm('Are you sure?')) {
                    fetch('/api/drops/activate/?api_token=' + this.auth_user.api_token, {
                        method: 'get'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Dropok aktiválva');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            deactivateAll() {
                if(confirm('Are you sure?')) {
                    fetch('/api/drops/deactivate/?api_token=' + this.auth_user.api_token, {
                        method: 'get'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Dropok kikapcsolva');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            deletePoint(id) {
                if(confirm('Are you sure?')) {
                    fetch('/api/drop/'+id+'?api_token=' + this.auth_user.api_token, {
                        method: 'delete'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Point removed');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            addDrop() {
                console.log('Adding drop:');
                console.log(this.drop);
                if(this.edit === false) {
                    //add
                    fetch('/api/drop?api_token=' + this.auth_user.api_token, {
                        method: 'post',
                        body: JSON.stringify(this.drop),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.drop.campaign_id = '';
                            this.drop.image_url = '';
                            this.drop.qty = 1;
                            this.drop.question_id = '';
                            this.drop.name = '';
                            this.drop.price = '';
                            alert('Drop added');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                } else {
                    //update
                    fetch('/api/drop?api_token=' + this.auth_user.api_token, {
                        method: 'put',
                        body: JSON.stringify(this.drop),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.edit = false;
                            this.drop.name = '';
                            this.drop.campaign_id = '';
                            this.drop.price = '';
                            this.drop.qty = 1;
                            this.drop.question_id = '';
                            this.drop.image_url = '';
                            this.drop.winners = false;
                            alert('Drop updated');
                            this.fetchDrops();
                        })
                        .catch(err => console.log(err))
                }
            },
            editDrop(drop) {
                this.toggleLayer(true);
                this.edit = true;
                this.drop.id = drop.id;
                this.drop.name = drop.name;
                this.drop.campaign_id = drop.campaign_id;
                this.drop.price = drop.price;
                this.drop.qty = 1;
                this.drop.question_id = drop.question_id;
                this.drop.winners = drop.winners;
                this.drop.image_url = drop.image_url;
            },
            toggleLayer(val){
                this.add_drops = val;
                if(!val) {
                    this.edit = false;
                    this.drop.name = '';
                    this.drop.campaign_id = '';
                    this.drop.price = '';
                    this.drop.qty = 1;
                    this.drop.question_id = '';
                    this.drop.image_url = '';
                }
            }
        }
    }
</script>