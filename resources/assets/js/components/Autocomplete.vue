<template>
    <div>
        <input id="ciri_user_autocomplete" type="text" placeholder="felhasználó keresése név alapján ..." v-model="query" v-on:keyup="autoComplete" v-on:focus="reOpen()" class="form-control">
        <div class="panel-footer" v-if="results.length">
            <ul class="list-group">
                <li class="list-group-item" v-for="result in results" @click="onClickButton(result.id)">

                        {{ result.nick }}

                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                temp: '',
                query: '',
                results: []
            }
        },
        methods: {
            autoComplete(){
                this.results = [];
                console.log('autocomplete');
                if(this.query.length > 2){
                    axios.get('/api/user_search',{params: {query: this.query}}).then(response => {
                        this.results = response.data;
                        console.log(response.data);
                    });
                }
            },
            onClickButton (event) {
                this.temp = this.results;
                this.results = [];
                this.$emit('clicked', event);
            },
            reOpen() {
                this.results = this.temp;
            }
        }
    }

</script>
