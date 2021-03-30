<template>
  <main-layout>
    <div>
      <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Tahun Ajaran PSB</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
          <button
            class="button w-32 mb-2 flex items-center justify-center bg-theme-1 text-white shadow-md"
            @click="openAdd()"
          >
            <PlusIcon class="w-4 h-4 mr-2" /> Tambah
          </button>
        </div>
      </div>
      <tabulator v-bind:tabulator="tabulator"></tabulator>
      <dialog-delete
        id="delete-form"
        :name-delete="nameDelete"
        :url="urlDelete"
      ></dialog-delete>
    </div>
  </main-layout>
</template>

<script>
import MainLayout from "./../../Layouts/MainLayout";
import Tabulator from "./../../App/tabulator/Datatables";
import DialogDelete from "./../../app/dialog/Delete";
import { tagTabulator } from "./../../app/libraries/tagTabulator";

export default {
  components: {
    MainLayout,
    Tabulator,
    DialogDelete,
  },
  data() {
    return {
      tabulator: {
        url: route("ta.list"),
        columns: [],
      },
      nameDelete: "",
      dataId: "",
    };
  },
  beforeMount() {
    var that = this;

    that.tabulator.columns = [
      {
        title: "KODE",
        minWidth: 150,
        field: "kode",
        hozAlign: "left",
        vertAlign: "middle",
        print: true,
        filter: true,
        download: false,
      },
      {
        title: "NAMA",
        minWidth: 200,
        field: "nama",
        hozAlign: "left",
        vertAlign: "middle",
        print: true,
        filter: true,
        download: false,
      },
      {
        title: "AKTIF",
        minWidth: 100,
        field: "aktif",
        hozAlign: "center",
        vertAlign: "middle",
        print: true,
        filter: true,
        download: false,
        formatter(cell, formatterParams, onRendered) {
          var data = cell.getRow().getData();

          return `
          <div class="flex">
            <div class=" text-white rounded-md text-xs px-2
            ${data.aktif ? "bg-theme-1" : "bg-theme-9"}
            ">
                ${data.aktif ? "YA" : "TIDAK"}
            </div>
          </div>
          `;
        },
      },
      {
        title: "AKSI",
        width: 80,
        field: "actions",
        responsive: 0,
        hozAlign: "center",
        vertAlign: "middle",
        print: false,
        filter: false,
        download: false,
        formatter(cell, formatterParams, onRendered) {
          var data = cell.getRow().getData();
          var tag = new tagTabulator();
          tag
            // .button(that.showData, data.id, "eye")
            .button(that.editData, data.id, "edit");

          if (!data.aktif) tag.button(that.deleteData, data, "trash-2");

          return tag.generate();
        },
      },
    ];
  },
  computed: {
    urlDelete() {
      return route("ta.destroy", {
        id: this.dataId,
      }).url();
    },
  },
  methods: {
    openAdd() {
      this.$inertia.get(route("ta.create"));
    },
    editData(id) {
      this.$inertia.get(route("ta.edit", { id }));
    },
    deleteData(data) {
      this.nameDelete = data.nama;
      this.dataId = data.id;

      cash("#delete-form").modal("show");
    },
  },
};
</script>
