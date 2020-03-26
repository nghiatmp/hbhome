<template>
    <Layout>
        <v-snackbar
            v-model="snackbar"
            :color="colors"
            :right="true"
            :timeout="2500"
            :top="true"
        >
            {{ snackbarText }}
            <v-btn
                dark
                text
                @click="snackbar = false"
            >
                Close
            </v-btn>
        </v-snackbar>
        <v-btn color="primary" class="ml-3" @click="diaLogcreateTeam = true">
            Create
        </v-btn>
        <v-card class="pa-3" style="box-shadow: none">
            <v-skeleton-loader
                type="card"
                v-if="isLoadingTable"
            />
            <v-data-table
                v-if="!isLoadingTable"
                :headers="headers"
                :items="tableData"
                class="elevation-4 mb-4"
                locale="US"
            >
                <template v-slot:item.id="{ item }">
                    <v-icon @click="getdataUpdate(item)">far fa-edit mr-2</v-icon>
                </template>
            </v-data-table>
        </v-card>
        <v-dialog
            v-model="diaLogcreateTeam"
            width="500px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Create Team
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center" cols="12">
                            <v-text-field
                                v-model="paramCreate.title"
                                label="Title"
                                dense
                                outlined
                                :error-messages="TitleCreateErrors"
                                @change="$v.paramCreate.title.$touch()"
                                @blur="$v.paramCreate.title.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramCreate.parent_id"
                                :items="Team"
                                label="Select Team"
                                item-value="id"
                                item-text="title"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="ParentCreateErrors"
                                @change="$v.paramCreate.parent_id.$touch()"
                                @blur="$v.paramCreate.parent_id.$touch()"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <div class="v-text-field__details ml-5" v-if="errMessageCreate">
                    <div class="v-messages theme--light error--text" role="alert">
                        <div class="v-messages__wrapper">
                            <p class="v-messages__message" v-html="errMessage"></p>
                        </div>
                    </div>
                </div>
                <v-card-actions>
                    <v-btn @click="ClearValidateCreate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="createTeam"
                    >Create Team</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogUpdateTeam"
            width="500px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Update Team
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center" cols="12">
                            <v-text-field
                                v-model="paramUpdate.title"
                                label="Title"
                                dense
                                outlined
                                :error-messages="TitleUpdateErrors"
                                @change="$v.paramUpdate.title.$touch()"
                                @blur="$v.paramUpdate.title.$touch()"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <div class="v-text-field__details ml-5" v-if="errMessageUpdate">
                    <div class="v-messages theme--light error--text" role="alert">
                        <div class="v-messages__wrapper">
                            <p class="v-messages__message" v-html="errMessageUp"></p>
                        </div>
                    </div>
                </div>
                <v-card-actions>
                    <v-btn @click="ClearValidateUpdate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="updateTeam"
                    >Update Team</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { required} from "vuelidate/lib/validators";
    export default {
        name: 'Index',
        components: { Layout },
        data (){
            return {
                snackbar: false,
                snackbarText:'',
                colors:'',
                isLoadingTable:false,
                diaLogUpdateTeam:false,
                IdUpdate:'',
                headers: [
                    {text: 'No', value: 'duration'},
                    {text: 'Title', value: 'name'},
                    {text: '', value: 'id'},
                ],
                paramCreate:{
                    title:'',
                    parent_id:'0',
                },
                paramUpdate:{
                    title:'',
                },
                diaLogcreateTeam:false,
                errMessageCreate:false,
                errMessage:'',
                errMessageUpdate:false,
                errMessageUp:'',
                Team:[],
                tableData:[],
            }
        },
        computed:{
            TitleCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.title.$dirty) return errors;
                !this.$v.paramCreate.title.required && errors.push('Title is required.');
                return errors
            },
            ParentCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.parent_id.$dirty) return errors;
                !this.$v.paramCreate.parent_id.required && errors.push('Team is required.');
                return errors
            },
            TitleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.title.$dirty) return errors;
                !this.$v.paramUpdate.title.required && errors.push('Title is required.');
                return errors
            },
        },
        validations : {
            paramCreate: {
                title: {required},
                parent_id: {required},
            },
            paramUpdate:{
                title: {required},
            },
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData() {
                this.isLoadingTable = true;
                this.axios
                    .get(`/api/teams`)
                    .then(res=>{
                        const teams =res.data.data;
                        const dataTeam = [
                            {
                                'id':'0',
                                'title':'HBL'
                            },
                        ];
                        const dataReturn = [];
                        Object.values(teams).forEach(key => {
                            key.name = key.title;
                            key.duration = key.id;
                            dataTeam.push(key);
                            dataReturn.push(key);
                            if (key.children.length > 0 ) {
                                Object.values(key.children).forEach(item => {
                                    item.name = key.title+'-'+item.title;
                                    item.duration = item.id;
                                    dataReturn.push(item);
                                })
                            }
                        });
                        this.tableData = dataReturn;
                        this.Team = dataTeam;
                        this.isLoadingTable = false;

                    })
                    .catch(err=>{
                        this.isLoadingTable = false;
                    });
            },
            createTeam(){
                this.$v.paramCreate.$touch();
                if (!this.$v.paramCreate.$invalid) {
                    const paramsCreate = Object.keys(this.paramCreate).reduce((prev, key) => {
                        if (this.paramCreate[key] !== '') {
                            prev[key] = this.paramCreate[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                        .post(`/api/teams`, paramsCreate)
                        .then(res => {
                            this.renderData();
                            this.diaLogcreateTeam = false;
                            this.errMessageCreate = false;
                            this.ClearValidateCreate();
                            this.snackbar = true;
                            this.snackbarText = 'Add Team Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 400 || err.response.status === 422) {
                                this.errMessageCreate = true;
                                this.errMessage = err.response.data.message;
                            } else {
                                this.ClearValidateCreate;
                                this.diaLogcreateResource = false;
                                this.snackbar = true;
                                this.snackbarText = 'Add Team False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            getdataUpdate(item) {
                this.IdUpdate = item.id;
                this.paramUpdate.title = item.title;
                this.diaLogUpdateTeam = true;
            },
            updateTeam(){
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const paramsUpdate = Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if (this.paramUpdate[key] !== '') {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    const IdTeam = this.IdUpdate;
                    this.axios
                        .put(`/api/teams/${IdTeam}`, paramsUpdate)
                        .then(res => {
                            this.renderData();
                            this.diaLogUpdateTeam = false;
                            this.errMessageUpdate = false;
                            this.ClearValidateUpdate();
                            this.snackbar = true;
                            this.snackbarText = 'Update Team Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 400 || err.response.status === 422) {
                                this.errMessageUpdate = true;
                                this.errMessageUp = err.response.data.message;
                            } else {
                                this.ClearValidateUpdate;
                                this.diaLogUpdateTeam = false;
                                this.snackbar = true;
                                this.snackbarText = 'Update Team False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            ClearValidateCreate() {
                this.diaLogcreateTeam=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.title='';
                this.paramCreate.parent_id='';
                this.paramCreate.errMessageCreate=false;
            },
            ClearValidateUpdate() {
                this.diaLogUpdateTeam=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.title='';
                this.paramCreate.errMessageUpdate=false;
            },
        }
    };
</script>
