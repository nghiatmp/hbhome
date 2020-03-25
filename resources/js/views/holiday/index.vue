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
        <v-card>
            <v-btn
                class="my-3"
                @click="diaLogcreateHoliday = true"
                color="primary"
            >Create
            </v-btn>
        </v-card>
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
        <v-card v-if="!isLoading">
            <v-tabs
                    v-model="tab"
                    background-color="light-blue darken-1"
                    class="elevation-2"
                    dark
                    :centered="centered"
                    :grow="grow"
                    :vertical="vertical"
                    :right="right"
                    :prev-icon="prevIcon ? 'mdi-arrow-left-bold-box-outline' : undefined"
                    :next-icon="nextIcon ? 'mdi-arrow-right-bold-box-outline' : undefined"
                    :icons-and-text="icons"
            >
                <v-tabs-slider />

                <v-tab href="#tab_chart">
                    Overview Holiday
                    <v-icon v-if="icons">mdi-phone</v-icon>
                </v-tab>

                <v-tab
                    href="#tab_list"
                >
                    List Holiday
                    <v-icon v-if="icons">mdi-phone</v-icon>
                </v-tab>

                <v-tab-item
                    id="tab_chart"
                >
                    <v-card class="ml-10 my-5">
                        <vc-calendar
                            :columns="$screens({ default: 1, lg: 4})"
                            :rows="$screens({ default: 1, lg: 3 })"
                            :is-expanded="$screens({ default: true, lg: false})"
                            :firstDayOfWeek="2"
                            :attributes='attrs'
                        />
                    </v-card>
                </v-tab-item>
                <v-tab-item
                    id="tab_list"
                >
                    <v-row>
                        <v-col cols="12" md="3" sm="12">
                            <v-menu
                                v-model="menuFrom"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="params.from_at"
                                        label="From"
                                        prepend-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="params.from_at" locale="UTC" :max="params.to_at" @input="menuFrom=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="12" md="3" sm="12">
                            <v-menu
                                v-model="menuTo"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="params.to_at"
                                        label="To"
                                        prepend-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="params.to_at" locale="UTC" :min="params.from_at"  @input="menuTo=false" />
                            </v-menu>
                        </v-col>
                    </v-row>
                    <v-data-table
                        :headers="headers"
                        :items="tableData"
                        class="elevation-4 mb-4"
                        locale="US"
                    >
                        <template v-slot:item.id="{ item }">
                            <v-layout justify-center>
                                <v-icon class="ml-2" @click="getDataUpdate(item)">fas fa-edit</v-icon>
                                <v-icon class="ml-2" @click="detete(item.id)">far fa-trash-alt</v-icon>
                            </v-layout>
                        </template>
                    </v-data-table>
                </v-tab-item>
            </v-tabs>
        </v-card>
        <v-dialog
            v-model="diaLogcreateHoliday"
            width="750px"
        >
            <v-card>
                <v-card-title>
                    Create Holiday
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-text-field
                                v-model="paramsCreate.title"
                                :error-messages="TitleCreateErrors"
                                label="Title"
                                dense
                                outlined
                                required
                                @input="$v.paramsCreate.title.$touch()"
                                @blur="$v.paramsCreate.title.$touch()"
                            />
                        </v-col>
                        <v-col cols="6" md="6" sm="6">
                            <v-menu
                                v-model="menuFromCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramsCreate.from_at"
                                        label="From"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                        :error-messages="FromCreateErrors"
                                        @change="$v.paramsCreate.from_at.$touch()"
                                        @blur="$v.paramsCreate.from_at.$touch()"
                                    />
                                </template>
                                <v-date-picker v-model="paramsCreate.from_at" :max="paramsCreate.to_at" @input="menuFromCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6">
                            <v-menu
                                v-model="menuToCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramsCreate.to_at"
                                        label="To"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                        :error-messages="ToCreateErrors"
                                        @change="$v.paramsCreate.to_at.$touch()"
                                        @blur="$v.paramsCreate.to_at.$touch()"

                                    />
                                </template>
                                <v-date-picker v-model="paramsCreate.to_at" :min="paramsCreate.from_at" @input="menuToCreate=false" />
                            </v-menu>
                        </v-col>
                        <div class="v-text-field__details" v-if="errExistHoliday">
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message"> Time is Exits</div>
                                </div>
                            </div>
                        </div>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="ClearValidateCreate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="CreateHoliday"
                    >Create Holiday</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogUpdateHoliday"
            width="750px"
        >
            <v-card>
                <v-card-title>
                    Update Holiday
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-text-field
                                v-model="paramsUpdate.title"
                                :error-messages="TitleUpdateErrors"
                                label="Title"
                                dense
                                outlined
                                required
                                @input="$v.paramsUpdate.title.$touch()"
                                @blur="$v.paramsUpdate.title.$touch()"
                            />
                        </v-col>
                        <v-col cols="6" md="6" sm="6">
                            <v-menu
                                v-model="menuFromUpdate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramsUpdate.from_at"
                                        label="From"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                        :error-messages="FromUpdateErrors"
                                        @change="$v.paramsUpdate.from_at.$touch()"
                                        @blur="$v.paramsUpdate.from_at.$touch()"
                                    />
                                </template>
                                <v-date-picker v-model="paramsUpdate.from_at" :max="paramsUpdate.to_at" @input="menuFromUpdate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6">
                            <v-menu
                                v-model="menuToUpdate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramsUpdate.to_at"
                                        label="To"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                        :error-messages="ToUpdateErrors"
                                        @change="$v.paramsUpdate.to_at.$touch()"
                                        @blur="$v.paramsUpdate.to_at.$touch()"

                                    />
                                </template>
                                <v-date-picker v-model="paramsUpdate.to_at" :min="paramsUpdate.from_at" @input="menuToUpdate=false" />
                            </v-menu>
                        </v-col>
                        <div class="v-text-field__details" v-if="errExistHolidayUpdate">
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message"> Time is Exits</div>
                                </div>
                            </div>
                        </div>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="ClearValidateUpdate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="UpdateHoliday"
                    >Update Holiday</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogdeleteHoliday"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Remove Holiday
                </v-card-title>
                <v-card-subtitle>
                    Do you want to detete ?
                </v-card-subtitle>
                <v-card-actions>
                    <v-btn @click="diaLogdeleteHoliday = false"
                    >Cancel</v-btn>
                    <v-btn
                        color="error"
                        @click="deleteHoliday"
                    >Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import Calendar from 'v-calendar/lib/components/calendar.umd'
    import DatePicker from 'v-calendar/lib/components/date-picker.umd'
    import { required, maxLength } from 'vuelidate/lib/validators';

    export default {
        name: 'Index',
        components: {
            Layout,
            Calendar,
            DatePicker
        },
        data() {
            return {
                tab: null,
                icons: false,
                centered: false,
                grow: true,
                vertical: false,
                prevIcon: false,
                nextIcon: false,
                right: false,
                isLoading:false,
                menuFromCreate: false,
                menuFrom:false,
                menuTo:false,
                menuToCreate: false,
                menuFromUpdate: false,
                menuToUpdate: false,
                diaLogcreateHoliday:false,
                diaLogdeleteHoliday:false,
                diaLogUpdateHoliday:false,
                errExistHoliday:false,
                errExistHolidayUpdate:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
                idHoliday:'',
                params: {
                    from_at: '',
                    to_at: '',
                },
                paramsCreate: {
                    title: '',
                    from_at:'',
                    to_at:'',
                },
                paramsUpdate: {
                    title: '',
                    from_at:'',
                    to_at:'',
                },
                tableData : [],
                headers: [
                    {text: 'No', value: 'duration'},
                    {text: 'Title', value: 'title'},
                    {text: 'From', value: 'from_at'},
                    {text: 'To', value: 'to_at', align: 'center'},
                    {text: '', value: 'id', align: 'center'},
                ],
                attrs: [],
            }
        },
        computed: {
            paramsChangeToRender() {
                return {
                    from_at: this.params.from_at,
                    to_at: this.params.to_at,
                };
            },
            TitleCreateErrors () {
                const errors = [];
                if (!this.$v.paramsCreate.title.$dirty) return errors;
                !this.$v.paramsCreate.title.maxLength && errors.push('Title must be at most 10 characters long');
                !this.$v.paramsCreate.title.required && errors.push('Title is required.');
                return errors
            },
            FromCreateErrors () {
                const errors = [];
                if (!this.$v.paramsCreate.from_at.$dirty) return errors;
                !this.$v.paramsCreate.from_at.required && errors.push('From Time is required.');
                return errors
            },
            ToCreateErrors () {
                const errors = [];
                if (!this.$v.paramsCreate.to_at.$dirty) return errors;
                !this.$v.paramsCreate.to_at.required && errors.push('To Time is required.');
                return errors
            },

            TitleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramsUpdate.title.$dirty) return errors;
                !this.$v.paramsUpdate.title.maxLength && errors.push('Title must be at most 10 characters long');
                !this.$v.paramsUpdate.title.required && errors.push('Title is required.');
                return errors
            },
            FromUpdateErrors () {
                const errors = [];
                if (!this.$v.paramsUpdate.from_at.$dirty) return errors;
                !this.$v.paramsUpdate.from_at.required && errors.push('From Time is required.');
                return errors
            },
            ToUpdateErrors () {
                const errors = [];
                if (!this.$v.paramsUpdate.to_at.$dirty) return errors;
                !this.$v.paramsUpdate.to_at.required && errors.push('To Time is required.');
                return errors
            },
        },
        validations : {
            paramsCreate : {
                title : { required, maxLength: maxLength(127) },
                from_at : { required},
                to_at : { required},
            },
            paramsUpdate: {
                title : { required, maxLength: maxLength(127) },
                from_at : { required},
                to_at : { required},
            },
        },
        watch : {
            paramsChangeToRender: {
                handler() {
                    this.renderDataTable();
                },
                deep: true,
            },
        },
        created(){
            this.renderData();
            this.renderDataTable();
        },
        methods:{
            renderData() {
                this.isLoading = true;
                this.axios
                    .get('api/holidays')
                    .then(res=>{
                        const resData = res.data.data;
                        const dataReturn = [];
                        Object.values(resData).forEach(item=> {
                            var child = {
                                popover: {
                                    label: item.title,
                                },
                                highlight: true,
                                dates:
                                    {start: item.from_at , end: item.to_at},
                                order: 0
                            };
                            dataReturn.push(child);
                        });
                        this.attrs = dataReturn;
                        this.isLoading = false;
                    })
                    .catch(()=> {
                        this.attrs = [];
                        this.isLoading = false;
                    })

                },
            renderDataTable() {
                this.isLoading = true;
                const params= Object.keys(this.params).reduce((prev, key) => {
                    if(this.params[key] !== null) {
                        prev[key] = this.params[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get('api/holidays', {params})
                    .then(res=>{

                        const resData = res.data.data;
                        const DataTable = Object.keys(resData).map((key) => {
                            const dataTableItem = { ... resData[key], duration: parseInt(key)+1 };
                            return dataTableItem;
                        });
                        this.tableData = DataTable;
                        this.isLoading = false;
                    })
                    .catch(()=> {
                        this.tableData = [];
                        this.isLoading = false;
                    })

            },
            CreateHoliday(){
                this.$v.paramsCreate.$touch();
                if (!this.$v.paramsCreate.$invalid) {
                    const paramsCreate= Object.keys(this.paramsCreate).reduce((prev, key) => {
                        if(this.paramsCreate[key] !== null) {
                            prev[key] = this.paramsCreate[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                        .post('/api/holidays', paramsCreate)
                        .then(res => {
                            this.renderData();
                            this.renderDataTable();
                            this.diaLogcreateHoliday = false;
                            this.errExistHoliday=false;
                            this.ClearValidateCreate();
                            this.$v.paramsCreate.$reset();
                            this.snackbar =  true;
                            this.snackbarText = 'Add Holiday Success';
                            this.colors = 'success';
                        })
                        .catch((err)=> {
                            if (err.response.status === 422) {
                                this.errExistHoliday=true;
                            } else {
                                this.$v.paramsCreate.$reset();
                                this.diaLogcreateHoliday = false;
                                this.ClearValidateCreate();
                                this.snackbar =  true;
                                this.snackbarText = 'Add Holiday False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            deleteHoliday(){
                const idHoliday = this.idHoliday;
                this.axios
                    .delete(`/api/holidays/${idHoliday}`)
                    .then(res=>{
                        this.renderData();
                        this.renderDataTable();
                        this.diaLogdeleteHoliday = false;
                        this.snackbar = true;
                        this.snackbarText = 'Remove Holiday Success';
                        this.colors = 'success';
                    })
                    .catch(err=>{
                        this.diaLogdeleteHoliday = false;
                        this.snackbar = true;
                        this.snackbarText = 'Remove Holiday Error';
                        this.colors = 'error';
                    });
            },
            getDataUpdate(item){
                console.log(item);
                this.idHoliday = item.id;
                this.paramsUpdate.title = item.title;
                this.paramsUpdate.from_at = item.from_at;
                this.paramsUpdate.to_at = item.to_at;
                this.diaLogUpdateHoliday = true;
            },
            UpdateHoliday(){
                this.$v.paramsUpdate.$touch();
                if (!this.$v.paramsUpdate.$invalid) {
                    const paramUpdate = Object.keys(this.paramsUpdate).reduce((prev, key) => {
                        if (this.paramsUpdate[key] !== '') {
                            prev[key] = this.paramsUpdate[key];
                        }
                        return prev;
                    }, {});
                    const idUpdate = this.idHoliday;
                    this.axios
                        .put(`/api/holidays/${idUpdate}`, paramUpdate)
                        .then(res => {
                            this.renderData();
                            this.renderDataTable();
                            this.diaLogUpdateHoliday = false;
                            this.errExistHolidayUpdate = false;
                            this.ClearValidateUpdate();
                            this.snackbar = true;
                            this.snackbarText = 'Update Holiday Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 422) {
                                this.errExistHolidayUpdate = true;
                            } else {
                                this.ClearValidateUpdate;
                                this.diaLogcreateResource = false;
                                this.snackbar = true;
                                this.snackbarText = 'Update Holiday False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            detete(id) {
                this.idHoliday = id;
                this.diaLogdeleteHoliday=true;
            },
            ClearValidateCreate() {
                this.diaLogcreateHoliday=false;
                this.$v.paramsCreate.$reset();
                this.paramsCreate.title='';
                this.paramsCreate.from_at='';
                this.paramsCreate.to_at='';
                this.errExistHoliday= false;
            },
            ClearValidateUpdate() {
                this.diaLogUpdateHoliday=false;
                this.$v.paramsUpdate.$reset();
                this.paramsUpdate.title='';
                this.paramsUpdate.from_at='';
                this.paramsUpdate.to_at='';
                this.errExistHolidayUpdate= false;
            },
        }
    }
</script>


