<template>
  <main-layout>
    <div>
      <form class="validate-form" @submit.prevent="save">
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
          <h2 class="text-lg font-medium mr-auto">
            {{ status }} Tahun Ajaran PSB
          </h2>
          <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <button
              type="button"
              class="button w-32 mr-2 mb-2 flex items-center justify-center shadow-md"
              @click="back()"
              :class="{ 'opacity-50 cursor-not-allowed': loading }"
              :disabled="loading"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-2" /> Kembali
            </button>
            <button
              type="submit"
              class="button w-32 mb-2 flex items-center justify-center bg-theme-1 text-white shadow-md"
              :class="{ 'opacity-50 cursor-not-allowed': loading }"
              :disabled="loading"
            >
              <SaveIcon class="w-4 h-4 mr-2" /> Simpan
              <LoadingIcon
                icon="oval"
                color="white"
                class="w-4 h-4 ml-auto"
                v-show="loading"
              />
            </button>
          </div>
        </div>
        <div class="intro-y box mt-5 lg:w-1/2">
          <div
            class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5"
          >
            <h2 class="font-medium text-base mr-auto">Form</h2>
          </div>
          <div id="vertical-form" class="px-5 pb-5 pt-3 b">
            <form-text v-model="ta.kode"></form-text>
            <form-text v-model="ta.nama"></form-text>
            <form-text v-model="ta.aktif"></form-text>
          </div>
        </div>
      </form>
    </div>
  </main-layout>
</template>

<script>
import Toastify from "toastify-js";
import NProgress from "nprogress";
import MainLayout from "./../../Layouts/MainLayout";
import FormText from "./../../app/form/Text";

export default {
  components: {
    MainLayout,
    FormText,
  },
  data() {
    return {
      modeAdd: true,
      status: null,
      loading: false,
      dataTA: null,
      ta: {
        nama: this.$helpers.formatInput("nama", "Nama", true, "text"),
        kode: this.$helpers.formatInput("kode", "Kode", true, "text"),
        aktif: this.$helpers.formatOption("aktif", "Aktif", true, "select", [
          { id: 1, nama: "YA" },
          { id: 0, nama: "TIDAK" },
        ]),
      },
    };
  },
  mounted() {
    this.dataTA = this.$page.props.ta;

    if (typeof this.dataTA !== "undefined") {
      this.modeAdd = false;

      Object.entries(this.dataTA).forEach((entry) => {
        const [key, value] = entry;

        if (typeof this.ta[key] !== "undefined") this.ta[key].text = value;
      });
    }

    this.status = this.modeAdd ? "Tambah" : "Edit";
  },
  methods: {
    back() {
      this.$inertia.get(route("ta.index"));
    },
    save() {
      if (this.$notif.formSubmit(this.ta)) {
        this.loading = true;

        var that = this;
        NProgress.start();

        let data = this.$helpers.serializeForm(this.ta);

        axios({
          method: this.modeAdd ? "post" : "put",
          url: this.modeAdd
            ? route("ta.store").url()
            : route("ta.update", {
                id: this.dataTA.id,
              }).url(),
          data,
        })
          .then(function (response) {
            that.$notif.response(response.data);

            if (parseInt(data.aktif) == 1)
              that.$store.dispatch("session/getSession");

            if (response.data.status) that.back();
          })
          .catch(function (error) {
            that.$notif.formError(error, that.ta);
          })
          .then(function () {
            that.loading = false;
            NProgress.done();
          });
      }
    },
  },
};
</script>
