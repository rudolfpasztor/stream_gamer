<template>
<div>
    <p class="last-activity">
        <strong>Last activity:</strong> {{settings.last_activity}}
    </p>
    <form @submit.prevent="saveSettings">
        <!-- Rounded switch -->
        <label class="switch">
            <input type="checkbox" v-on:change="chatbotOnOff" v-model="comparisonvalue">
            <span class="slider round"></span>
            <span class="txt">Chatbot bekapcsolása</span>
        </label>
        <div class="fancy-form-group inline">
            <label for="chatbot_point_count">Points:</label>
            <input id="chatbot_point_count" type="number" min="1" class="form-control" placeholder="1" v-model="settings.give_point_count" required>
        </div>
        <div class="fancy-form-group inline">
            <label for="check_users_interval">Minutes:</label>
            <input id="check_users_interval" type="number" min="1" class="form-control" placeholder="1" v-model="settings.check_users_interval" required>
        </div>
        <div class="fancy-form-group inline">
            <label for="chatbot_source">Source:</label>
            <input id="chatbot_source" type="text" class="form-control" placeholder="1" v-model="settings.chatbot_point_source" required>
        </div>
        <button type="submit" class="btn-rounded btn-outline pull-right">Mentés</button>
    </form>
</div>
</template>

<script>
    export default {
        props: ['auth_user'],
        data() {
            return {
                settings: {
                    check_users_interval: 0,
                    give_point_count: 0,
                    chatbot_run: 0,
                    campaign_id: 1,
                    chatbot_point_source: "Közvetítés",
                    last_activity: ''
                },
                loading: false,
                comparisonvalue: false,
            }
        },
        created() {
            console.log('created');
            this.fetchSettings();

        },
        mounted() {
            console.log('mounted');

        },

        methods: {
            fetchSettings() {
                console.log(this.settings);
                console.log('Fetching settings ...');
                fetch('api/settings?api_token=' + this.auth_user.api_token)
                    .then(res => res.json())
                    .then(res => {
                        console.log(res);
                        if(res.chatbot_run === '1') {
                            this.comparisonvalue = true;
                        }  else {
                            this.comparisonvalue = false;
                        }
                        this.settings = res;
                    })
                    .catch(err => console.log(err))
            },
            saveSettings() {
                let settings_object = {
                    "settings": this.settings
                }
                fetch('api/settings?api_token=' + this.auth_user.api_token, {
                    method: 'post',
                    body: JSON.stringify(settings_object),
                    headers: {
                        'content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        alert('Chatbot settings updated!');
                    })
                    .catch(err => console.log(err))
            },
            chatbotOnOff() {
                console.log('chatbotOnOff' + this.comparisonvalue);
                if(this.comparisonvalue) {
                    this.settings.chatbot_run = 1;
                } else {
                    this.settings.chatbot_run = 0;
                }
            }

        }
    }
</script>