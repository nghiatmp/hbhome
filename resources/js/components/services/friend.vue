<template>
  <div>
    <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
      <v-row>
        <v-col cols="12" md="3" sm="12">
          <v-select
            v-model="params.friend_service_id"
            :items="services"
            label="サービスID"
            item-value="service_id"
            item-text="service_name"
            :hide-details="true"
            outlined
            dense
          />
        </v-col>
        <v-col cols="12" md="3" sm="12">
          <v-select
            v-model="params.friend_time_group_by"
            :items="TIME_GROUP_BY"
            item-value="key"
            item-text="value"
            label="グループ化"
            :hide-details="true"
            outlined
            dense
          />
        </v-col>
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
                v-model="params.friend_start_at"
                label="開始日"
                prepend-icon="event"
                :hide-details="true"
                clearable
                readonly
                outlined
                dense
                v-on="on"
              />
            </template>
            <v-date-picker v-model="params.friend_start_at" :max="nowDate" @input="menuFrom=false" />
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
                v-model="params.friend_end_at"
                label="終了日"
                prepend-icon="event"
                :hide-details="true"
                clearable
                readonly
                outlined
                dense
                v-on="on"
              />
            </template>
            <v-date-picker v-model="params.friend_end_at" :min="params.friend_start_at" :max="nowDate" @input="menuTo=false" />
          </v-menu>
        </v-col>
      </v-row>
    </v-card>
    <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
      <v-skeleton-loader
        v-if="loadingData"
        class="mx-auto"
        type="card"
      />
      <highcharts v-if="!loadingData" :options="chartOptions" />
    </v-card>
    <v-card class="pa-3" style="box-shadow: none">
      <v-data-table
        :headers="tableHeaders"
        :items="tableData"
        class="elevation-4 mb-4"
        :loading="loadingData"
      />
    </v-card>
  </div>
</template>

<script>
import { TIME_GROUP_BY, TIME_GROUP_BY_DEFAULT } from '@/constants/common';
import { Chart } from 'highcharts-vue';

export default {
  name: 'ReportFriendComponent',
  components: {
    highcharts: Chart,
  },
  data() {
    return {
      chartOptions: {
        title: {
          text: '友だち登録数',
        },
        xAxis: {
          categories: [],
        },
        yAxis: {
          allowDecimals: false,
        },
        series: [
          {
            name: 'Active',
            data: [],
          },
          {
            name: 'Inactive',
            data: [],
          },
        ],
        credits: {
          enabled: false,
        },
      },
      params: {
        friend_service_id: '',
        friend_time_group_by: TIME_GROUP_BY_DEFAULT,
        friend_start_at: '',
        friend_end_at: '',
      },
      nowDate: new Date().toISOString().slice(0, 10),
      menuFrom: false,
      menuTo: false,
      tableHeaders: [
        { text: '時間', value: 'duration' },
        { text: 'Active', value: 'active' },
        { text: 'Inactive', value: 'deactive' },
      ],
      services: [],
      tableData: [],
      TIME_GROUP_BY,
      loadingData: true,
    };
  },
  watch: {
    params: {
      handler(value) {
        this.renderData();
      },
      deep: true,
    },
  },
  created() {
    this.index();
  },
  methods: {
    async renderData() {
      this.loadingData = true;
      this.tableData = [];
      const params = Object.keys(this.params).reduce((prev, key) => {
        if (this.params[key] !== null && this.params[key] !== '') {
          prev[key.replace('friend_', '')] = this.params[key];
        }
        return prev;
      }, {});
      const paramsQuery = Object.keys(params).reduce((prev, key) => {
        prev['friend_' + key] = params[key];
        return prev;
      }, {});
      if (JSON.stringify(this.$route.query) !== JSON.stringify(paramsQuery)) {
        this.$router.replace({ query: paramsQuery });
      }

      const res = await this.axios.get(`service/${this.params.friend_service_id}/friend`, { params });

      const resData = res.data.data;
      this.chartOptions.xAxis.categories = Object.keys(resData);

      this.chartOptions.series[0]['data'] = Object.values(resData).reduce((friends, friend) => {
        friends.push(friend.active);
        return friends;
      }, []);
      this.chartOptions.series[1]['data'] = Object.values(resData).reduce((friends, friend) => {
        friends.push(friend.deactive);
        return friends;
      }, []);
      this.tableData = Object.keys(resData).map((key) => {
        const dataTableItem = { ... resData[key], duration: key };
        return dataTableItem;
      });
      this.loadingData = false;
    },
    async index() {
      const res = await this.axios.get('all-services');
      if (res.status !== 200) {
        this.services = [];
        return;
      }

      this.services = res.data.data.services;
      const paramsInit = this.$route.query;
      const paramSearch = { ... this.params };
      Object.keys(paramsInit).forEach(function(key) {
        paramSearch[key] = paramsInit[key];
      });
      if (paramSearch.friend_service_id.length === 0 && this.services[0]) {
        paramSearch.friend_service_id = this.services[0].service_id;
      }
      this.params = { ... paramSearch };
    },
  },
};
</script>
