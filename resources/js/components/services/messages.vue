<template>
  <div>
    <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
      <v-row>
        <v-col cols="12" md="3" sm="12">
          <v-select
            v-model="params.message_service_id"
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
            v-model="params.message_time_group_by"
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
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px"
          >
            <template v-slot:activator="{ on }">
              <v-text-field
                v-model="params.message_start_at"
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
            <v-date-picker v-model="params.message_start_at" locale="ja-JP" :max="nowDate" />
          </v-menu>
        </v-col>
        <v-col cols="12" md="3" sm="12">
          <v-menu
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px"
          >
            <template v-slot:activator="{ on }">
              <v-text-field
                v-model="params.message_end_at"
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
            <v-date-picker v-model="params.message_end_at" locale="ja-JP" :min="params.message_start_at" :max="nowDate" />
          </v-menu>
        </v-col>
      </v-row>
    </v-card>
    <v-card v-if="isDataEmpty" class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
      <v-card-text>データはありません。</v-card-text>
    </v-card>
    <v-card v-if="!isDataEmpty" class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
      <v-skeleton-loader
        v-if="loadingData"
        class="mx-auto"
        type="card"
      />
      <highcharts v-if="!loadingData" :options="chartOptions" />
    </v-card>
    <v-card v-if="!isDataEmpty" class="pa-3" style="box-shadow: none">
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
export default {
  name: 'ServiceMessage',
  data() {
    return {
      services: [],
      params: {
        message_service_id: '',
        message_time_group_by: TIME_GROUP_BY_DEFAULT,
        message_start_at: '',
        message_end_at: '',
      },
      chartOptions: {
        title: {
          text: 'メッセージ数',
        },
        xAxis: {
          categories: [],
        },
        yAxis: {
          allowDecimals: false,
        },
        series: [],
        credits: {
          enabled: false,
        },
      },
      nowDate: new Date().toISOString().slice(0, 10),
      TIME_GROUP_BY,
      tableHeaders: [],
      tableData: [],
      loadingData: true,
      isDataEmpty: false,
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
    renderData() {
      this.isDataEmpty = false;
      this.loadingData = true;
      this.tableData = [];
      if (this.params.message_service_id) {
        const params = Object.keys(this.params).reduce((prev, key) => {
          if (this.params[key] !== null && this.params[key] !== '') {
            prev[key.replace('message_', '')] = this.params[key];
          }
          return prev;
        }, {});
        const paramsQuery = Object.keys(params).reduce((prev, key) => {
          prev['message_' + key] = params[key];
          return prev;
        }, {});
        if (JSON.stringify(this.$route.query) !== JSON.stringify(paramsQuery)) {
          this.$router.replace({ query: paramsQuery });
        }
        this.axios
          .get(`service/${this.params.message_service_id}/messages`, { params })
          .then(res => {
            const data = res.data.data.message;
            if (res.data.data.totalMessage === 0) {
              this.isDataEmpty = true;
            } else {
              this.isDataEmpty = false;
              this.chartOptions.xAxis.categories = Object.keys(data);
              const dataSeries = [];
              Object.keys(data).forEach(key => {
                Object.keys(data[key]).forEach(label => {
                  if (!(label in dataSeries)) {
                    dataSeries[label] = [];
                  }
                  dataSeries[label].push(data[key][label]);
                });
              });
              this.chartOptions.series = Object.keys(dataSeries).map(label => {
                return {
                  'name': label,
                  'data': dataSeries[label],
                };
              });
              this.tableHeaders = [];
              this.tableHeaders.push({
                text: '時間',
                value: 'duration',
              });
              Object.keys(dataSeries).forEach(label => {
                this.tableHeaders.push({
                  text: label,
                  sortable: false,
                  value: label,
                  align: 'right',
                });
              });
              this.tableData = Object.keys(data).map((key) => {
                const tableDataItem = { ... data[key], duration: key };
                return tableDataItem;
              });
              this.loadingData = false;
            }
          });
      }
    },
    index() {
      this.axios
        .get('all-services')
        .then(res => {
          this.services = res.data.data.services;
          const paramsInit = this.$route.query;
          const paramSearch = { ... this.params };
          Object.keys(paramsInit).forEach(function(key) {
            paramSearch[key] = paramsInit[key];
          });
          if (paramSearch.message_service_id.length === 0 && this.services[0]) {
            paramSearch.message_service_id = this.services[0].service_id;
          }
          this.params = { ... paramSearch };
        })
        .catch(() => {
          this.services = [];
        });
    },
  },
};
</script>

<style scoped>

</style>
