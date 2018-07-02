<template>
    <div>
        <h2 class="page-title">Questions</h2>



        <nav aria-label="Page navigation" class="page-navigation">
            <ul class="pagination">

            </ul>

            <ul class="page-actions">
                <li><span @click="toggleLayer(true)" class="btn-rounded btn-outline btn-action">Add new</span></li>
                <li><span @click="activateAll()" class="btn-rounded btn-outline btn-action">Activate all</span></li>
                <li><span @click="deactivateAll()" class="btn-rounded btn-outline btn-action">Passivate all</span></li>
            </ul>
        </nav>


        <div id="questions">
            <div v-for="question in questions"  class="question">
                <h3>
                    {{question.question}}
                    <span class="delete" @click="deleteQuestion(question.id)"><i class="fa fa-trash-alt"></i></span>
                    <span class="edit" @click="editQuestion(question)"><i class="fa fa-edit"></i></span>
                </h3>
                <div class="half-container">
                    <div class="half correct">
                        {{question.correct_answers}}
                        <span>Helyes válasz</span>
                    </div>
                    <div class="half incorrect">
                        {{question.incorrect_answers}}
                        <span>Helytelen válasz</span>
                    </div>
                </div>
                <ul class="answers">
                    <li v-for="(answer, index) in question.answers">
                        {{answer.value}}
                    </li>
                </ul>
                <a v-if="question.help_link" v-bind:href="question.help_link" target="_blank" class="help-link"><i class="icon-link"></i>Help link</a>
            </div>
        </div>



        <div class="layer" @click="toggleLayer(false)" v-show="this.add_question">
            <div class="panel" v-on:click.stop>
                <form @submit.prevent="addQuestion" action="">
                    <div v-show="!this.loading && !this.success">
                        <div class="form-panel-title">
                            <h3 v-if="this.edit">Edit Question</h3>
                            <h3 v-else>Add Question</h3>
                        </div>
                        <div class="overflow">
                            <div class="fancy-form-group inline">
                                <label for="question">Question:</label>
                                <input id="question" type="text" class="form-control" placeholder="Question" v-model="question.question" required>
                            </div>
                            <div class="fancy-form-group inline">
                                <label for="help">Help link:</label>
                                <input id="help" type="text" class="form-control" placeholder="Url of the article with helpful information...." v-model="question.help_link" required>
                            </div>

                            <div v-for="(answer, index) in question.answers" v-bind:class="'fancy-form-group inline answer-line'">
                                <label>Answer {{index}}:</label>
                                <input v-bind:name="answer.name" v-model="answer.value" placeholder="Válasz" class="form-control" type="text" required>
                                <span class="set_correct" @click="setCorrectAnswer(index)">
                                    <i v-if="question.correct_answer_id === index" class="icon-check"></i> <span>Ez a helyes válasz</span>
                                </span>
                            </div>
                            <span class="add-input-btn" @click="addInput"><i class="icon-plus"></i>Add new input</span>

                        </div>
                        <button type="submit" class="btn-rounded btn-outline pull-right" v-if="this.edit">Save Question!</button>
                        <button type="submit" class="btn-rounded btn-outline pull-right" v-else>Add Question!</button>
                    </div>

                    <div id="loading" v-show="this.loading && !this.success">
                        <loader></loader>
                    </div>

                    <div id="success" v-if="this.success">
                        <i class="fa fa-check"></i> Question succesfully added!
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['auth_user'],
        data() {
            return {
                questions: false,
                question: {
                    id: '',
                    question: '',
                    correct_answer_id: '',
                    help_link: '',
                    correct_answers: 0,
                    incorrect_answers: 0,
                    answers: [{ id: 0, value: '', name:'answer' }]
                },
                question_id: '',
                add_question : false,
                pagination: {},
                edit: false,
                success: false,
                loading: false,
            }
        },

        created() {
            this.fetchQuestions();
        },

        methods: {
            fetchQuestions(page_url) {
                let vm = this;
                page_url = page_url  || '/api/questions?api_token=' + this.auth_user.api_token;
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        for(let i = 0; i < res.data.length; i++){
                            let tmp = JSON.parse(res.data[i].answers);
                            console.log(tmp);
                            res.data[i].answers = tmp;
                        }
                        this.questions = res.data;
                        console.log(res.data);
                        vm.makePagination(res);
                        this.loading = false;
                        this.success = false;
                        this.edit = false;
                        this.add_question = false;
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
            deleteQuestion(id) {
                if(confirm('Are you sure?')) {
                    this.loading = true;
                    fetch('/api/question/'+id+'?api_token=' + this.auth_user.api_token, {
                        method: 'delete'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Question removed');
                            this.loading = false;
                            this.success = true;
                            this.fetchQuestions();
                        })
                        .catch(err => console.log(err))
                }
            },
            addInput() {
                let index = this.question.answers.length;
                index ++;
                this.question.answers.push({ id: index, value: '', name: 'answer' })
            },
            setCorrectAnswer(id){
                this.question.correct_answer_id = id;
            },
            addQuestion() {
                if(this.edit === false) {
                    //add
                    this.loading = true;
                    fetch('/api/question?api_token=' + this.auth_user.api_token, {
                        method: 'post',
                        body: JSON.stringify(this.question),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                                this.question.id = '';
                                this.question.question = '';
                                this.question.correct_answer_id = '';
                                this.question.correct_answers = 0;
                                this.question.incorrect_answers = 0;
                                this.question.help_link = '';
                                this.question.answers = [];
                                this.loading = false;
                                this.success = true;
                            this.fetchQuestions();
                        })
                        .catch(err => console.log(err))
                } else {
                    //update
                    this.loading = true;
                    fetch('/api/question?api_token=' + this.auth_user.api_token, {
                        method: 'put',
                        body: JSON.stringify(this.question),
                        headers: {
                            'content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.question.id = '';
                            this.question.question = '';
                            this.question.correct_answer_id = '';
                            this.question.correct_answers = 0;
                            this.question.incorrect_answers = 0;
                            this.question.help_link = '';
                            this.question.answers = [];
                            this.loading = false;
                            this.success = true;
                            alert('Question updated');
                            this.fetchQuestions();
                        })
                        .catch(err => console.log(err))
                }
            },
            editQuestion(question) {
                this.edit = true;
                this.question.id = question.id;
                this.question.question = question.question;
                this.question.correct_answer_id = question.correct_answer_id;
                this.question.correct_answers = question.correct_answers;
                this.question.incorrect_answers = question.incorrect_answers;
                this.question.help_link = question.help_link;
                this.question.answers = question.answers;
                this.toggleLayer(true);
            },
            toggleLayer(val){
                this.add_question = val;
                if(!val) {
                    this.edit = false;
                    this.question.id = '';
                    this.question.question = '';
                    this.question.correct_answer_id = '';
                    this.question.correct_answers = 0;
                    this.question.incorrect_answers = 0;
                    this.question.help_link = '';
                    this.question.answers = [];
                }
            }
        }
    }
</script>