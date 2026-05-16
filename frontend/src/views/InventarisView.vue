<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
    <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
        <q-card-section>
           <q-table
              title="Inventaris"
              :rows="filteredRows"
              :columns="columns"
              :filter="filter"
              :pagination="pagination"
              :loading="loading"
              separator="cell"
              row-key="name"
              flat
              dense
              class="my-sticky-column-table my-sticky-header-table"
              
           >
           <template v-slot:loading>
            <q-inner-loading showing>
                <q-spinner-ios size="30px" color="green-7" />
            </q-inner-loading>
          </template>
           <template v-slot:top-right>
            <q-select
              v-model="filterTahun"
              :options="tahunOptions"
              label="Filter Tahun"
              clearable
              dense
              outlined
              style="min-width: 140px"
              class="q-mr-md"
              @update:model-value="getData"
            />
             <q-select
               v-model="filterStokStatus"
              :options="stokStatusOptions"
              label="Filter Stok"
              emit-value
              map-options
              option-value="value"
              option-label="label"
              clearable
              dense
              outlined
              style="min-width: 160px"
              class="q-mr-md"
               @update:model-value="getData"
             />
             <q-select
               v-model="filterJenisBarang"
               :options="filterJenisBarangOptions"
               label="Jenis Barang"
               emit-value
               map-options
               option-value="value"
               option-label="label"
               clearable
               dense
               outlined
               style="min-width: 150px"
               class="q-mr-md"
             />
             <q-select
               v-model="rusakOnly"
               :options="rusakOnlyOptions"
               label="Filter Rusak"
               emit-value
               map-options
               option-value="value"
               option-label="label"
               clearable
               dense
               outlined
               style="min-width: 150px"
               class="q-mr-md"
               @update:model-value="getData"
             />
             <q-select
               v-model="pemutihanOnly"
               :options="pemutihanOnlyOptions"
               label="Filter Pemutihan"
               emit-value
               map-options
               option-value="value"
               option-label="label"
               clearable
               dense
               outlined
               style="min-width: 170px"
               class="q-mr-md"
               @update:model-value="getData"
             />
              <q-input
                v-model="filter"
                label="Search"
                debounce="300"
                clearable
                dense
                outlined
                style="min-width: 220px"
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
             </q-input>
            <q-btn label="Insert" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
          </template>
          <template v-slot:body-cell-albah="props">
            <q-td :props="props">
              <div class="column">
                <div>{{ props.row.nabar }}</div>
                <div class="q-mt-xs q-gutter-xs" v-if="isNeedActionStock(props.row)">
                  <q-badge v-if="isNeedActionStock(props.row)" :color="statusStok(props.row).color" text-color="white">
                    {{ statusStok(props.row).label }}
                  </q-badge>
                </div>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-aksi="props">
            <q-td :props="props">
              <q-btn @click="bukaDetail(props.row)" round icon="o_visibility" color="blue-grey-7" size="xs" flat>
                <q-tooltip>Detail Inventaris</q-tooltip>
              </q-btn>
              <q-btn @click="edit(props.row.id)" round icon="far fa-edit" color="green-7" size="xs" flat>
                <q-tooltip>Edit Inventaris</q-tooltip>
              </q-btn>
              <q-btn @click="bukaRiwayat(props.row)" round icon="o_timeline" color="blue-8" size="xs" flat>
                <q-tooltip>Riwayat Stok Tahunan</q-tooltip>
              </q-btn>
              <q-btn @click="konfirmasi(props.row.id)" round icon="fas fa-trash-alt" color="red" size="xs" flat>
                <q-tooltip>Hapus Inventaris</q-tooltip>
              </q-btn>
              <FotoInv :id="props.row.id" tooltip="Upload Foto Inventaris"/>
            </q-td>
          </template>
          <template v-slot:body-cell-jenis_barang="props">
            <q-td :props="props">
              <q-badge :color="badgeJenisBarang(props.row.jenis_barang).color" text-color="white">
                {{ badgeJenisBarang(props.row.jenis_barang).label }}
              </q-badge>
            </q-td>
          </template>
           </q-table>
        </q-card-section>
       </q-card>
       <q-card v-else flat>
        <q-banner dense class="bg-red text-white">
          <template v-slot:avatar>
            <q-icon name="fas fa-user-lock" color="white" />
          </template>
          MAAF, Anda Tidak Berjak Mengakses Halaman ini
          <template v-slot:action>
            <q-btn flat color="white" label="Back To Dashboard" to="/" />
          </template>
        </q-banner>
       </q-card>
       <!-- DIALOG -->
       <q-dialog v-model="dialogInsert">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Data</span> Inventaris</q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input outlined v-model="form.katalog" label="Katalog*" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.nabar" label="Nama Alat/Bahan*" class="q-my-sm" color="green-3" dense />
            <q-editor v-model="form.spec" min-height="5rem" placeholder="Spesifikasi" />
            <div class="row q-col-gutter-sm q-my-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.satuan" label="Satuan*" color="green-3" dense />
              </div>
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.vol" label="Volume*" color="green-3" dense />
              </div>
            </div>
            <div class="row q-col-gutter-sm q-my-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.thn_masuk" label="Tahun Masuk*" color="green-3" dense />
              </div>
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.thn_pakai" label="Tahun Pakai*" color="green-3" dense />
              </div>
            </div>
            <q-select
              outlined
              v-model="form.jenis_barang"
              :options="jenisBarangOptions"
              emit-value
              map-options
              option-value="value"
              option-label="label"
              label="Jenis Barang"
              class="q-my-sm"
              color="green-3"
              dense
            />
            <q-banner v-if="isHabisPakaiForm" dense rounded class="bg-blue-1 text-blue-10 q-my-sm">
              <template v-slot:avatar>
                <q-icon name="info" />
              </template>
              Barang habis pakai tidak membutuhkan input kondisi. Kondisi baik akan mengikuti jumlah stok dan rusak otomatis 0.
            </q-banner>
            <q-input outlined v-model="form.noreg" label="No Reg*" class="q-my-sm" color="green-3" dense />
            <div class="row q-col-gutter-sm q-my-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.merek" label="Merek" color="green-3" dense />
              </div>
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.tipe" label="Tipe" color="green-3" dense />
              </div>
            </div>
            <div class="row q-col-gutter-sm q-my-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.produsen" label="Produsen" color="green-3" dense />
              </div>
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.asal" label="Asal" color="green-3" dense />
              </div>
            </div>
            <q-input outlined :model-value="form.jml" @update:model-value="ubahJumlah" type="number" min="0" label="Jumlah*" class="q-my-sm" color="green-3" dense />
            <div v-if="!isHabisPakaiForm" class="row q-col-gutter-sm q-my-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined :model-value="form.konbaik" type="number" min="0" label="Kondisi Baik" color="green-3" dense readonly />
              </div>
              <div class="col-12 col-sm-6">
                <q-input
                  ref="konrusakInput"
                  outlined
                  :model-value="form.konrusak"
                  @update:model-value="ubahKondisiRusak"
                  type="text"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  min="0"
                  :max="formNumber(form.jml)"
                  label="Kondisi Rusak"
                  color="green-3"
                  dense
                />
              </div>
            </div>
            <q-input
              outlined
              v-model.number="form.stok_minimum"
              type="number"
              min="0"
              label="Stok Minimum"
              class="q-my-sm"
              color="green-3"
              dense
            />
            <q-input outlined v-model="form.lokasi" autogrow label="Lokasi*" class="q-my-sm" color="green-3" dense />
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="simpan"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogDetail">
      <q-card style="width: 760px; max-width: 95vw;">
        <q-toolbar>
          <q-toolbar-title class="text-blue-grey-8">
            Detail Inventaris - {{ detailTerpilih.nabar || '-' }}
          </q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup @click="tutupDetail" />
        </q-toolbar>
        <q-separator />
        <q-card-section style="max-height: 70vh" class="scroll">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <div v-if="detailTerpilih.foto" class="images" v-viewer>
                <q-img
                  :src="url + detailTerpilih.foto"
                  class="rounded-borders"
                  style="width: 100%; max-width: 260px"
                />
              </div>
              <q-banner v-else dense rounded class="bg-grey-2 text-grey-8">
                Foto belum tersedia
              </q-banner>
            </div>
            <div class="col-12 col-md-8">
              <q-list dense bordered separator>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Nomor REG</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.noreg) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Katalog</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.katalog) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Nama Alat/Bahan</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.nabar) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Jenis Barang</q-item-label>
                    <q-item-label>
                      <q-badge :color="badgeJenisBarang(detailTerpilih.jenis_barang).color" text-color="white">
                        {{ badgeJenisBarang(detailTerpilih.jenis_barang).label }}
                      </q-badge>
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Tahun Masuk / Tahun Pakai</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.thn_masuk) }} / {{ tampilDetail(detailTerpilih.thn_pakai) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Jumlah / Baik / Rusak</q-item-label>
                    <q-item-label>
                      {{ tampilDetail(detailTerpilih.jml) }} / {{ tampilDetail(detailTerpilih.konbaik) }} / {{ tampilDetail(detailTerpilih.konrusak) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Stok Minimum</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.stok_minimum) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Lokasi</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.lokasi) }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <div class="col-12">
              <q-list dense bordered separator>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Spesifikasi</q-item-label>
                    <q-item-label>
                      <div v-if="detailTerpilih.spec" v-html="detailTerpilih.spec" />
                      <span v-else>-</span>
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Satuan / Volume</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.satuan) }} / {{ tampilDetail(detailTerpilih.vol) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Merek / Tipe</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.merek) }} / {{ tampilDetail(detailTerpilih.tipe) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Produsen / Asal</q-item-label>
                    <q-item-label>{{ tampilDetail(detailTerpilih.produsen) }} / {{ tampilDetail(detailTerpilih.asal) }}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item v-if="detailTerpilih.ket">
                  <q-item-section>
                    <q-item-label caption>Keterangan</q-item-label>
                    <q-item-label>{{ detailTerpilih.ket }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right" class="bg-white">
          <q-btn label="Tutup" color="blue-grey-8" @click="tutupDetail" />
        </q-card-actions>
      </q-card>
    </q-dialog>
     <!-- KONFIRMASI -->
     <q-dialog v-model="confirm" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-item>
            <q-item-section side>
              <q-icon color="red" name="fas fa-exclamation-circle" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle2">Apakah Anda Ingin Menghapus Data ini?</q-item-label>
              <q-item-label caption lines="2">Data Inventaris</q-item-label>
            </q-item-section>
          </q-item>
        </q-card-section>
  
        <q-card-actions align="right">
          <q-btn label="No" color="primary" @click="batal" dense />
          <q-btn label="Yes" color="red" @click="hapus" dense />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogRiwayat" persistent>
      <q-card style="width: 900px; max-width: 95vw;">
        <q-toolbar>
          <q-toolbar-title class="text-blue-8">
            Riwayat Stok - {{ riwayatHeader.nabar || '-' }}
          </q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup @click="tutupRiwayat" />
        </q-toolbar>
        <q-separator />

        <q-card-section>
          <div class="row q-col-gutter-md q-mb-md">
            <div class="col-12 col-md-2">
              <q-input v-model="formTambahStok.tahun" label="Tahun" dense outlined />
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model.number="formTambahStok.qty" type="number" min="1" label="Qty" dense outlined />
            </div>
            <div class="col-12 col-md-2">
              <q-input
                v-if="editingRiwayatId && formTambahStok.jenis === 'initial'"
                :model-value="labelJenis(formTambahStok.jenis)"
                label="Jenis"
                dense
                outlined
                readonly
              />
              <q-select
                v-else
                v-model="formTambahStok.jenis"
                :options="jenisMutasiOptions"
                emit-value
                map-options
                option-value="value"
                option-label="label"
                label="Jenis"
                dense
                outlined
                @update:model-value="onJenisMutasiChange"
              />
            </div>
            <div v-if="formTambahStok.jenis === 'pemutihan'" class="col-12 col-md-2">
              <q-select
                v-model="formTambahStok.kondisi_asal"
                :options="kondisiPemutihanOptions"
                emit-value
                map-options
                option-value="value"
                option-label="label"
                label="Diputihkan dari"
                dense
                outlined
              />
            </div>
            <div :class="formTambahStok.jenis === 'pemutihan' ? 'col-12 col-md-4' : 'col-12 col-md-6'">
              <q-input v-model="formTambahStok.keterangan" label="Keterangan" dense outlined />
            </div>
            <div class="col-12 col-md-12 flex justify-end q-gutter-sm">
              <q-btn v-if="editingRiwayatId" label="Batal Edit" flat color="grey-8" @click="batalEditRiwayat" />
              <q-btn :label="editingRiwayatId ? 'Update Riwayat' : 'Simpan Riwayat'" color="blue-8" @click="tambahStok" :loading="savingStok" unelevated />
            </div>
          </div>

          <div class="text-subtitle2 q-mb-sm">Detail Riwayat</div>
          <q-table
            :rows="riwayatItems"
            :columns="riwayatColumns"
            row-key="id"
            dense
            flat
            bordered
            :pagination="{ rowsPerPage: 8 }"
          >
            <template v-slot:body-cell-jenis="props">
              <q-td :props="props">
                <q-badge :color="badgeJenis(props.row).color" text-color="white">
                  {{ badgeJenis(props.row).label }}
                </q-badge>
              </q-td>
            </template>
            <template v-slot:body-cell-aksi_riwayat="props">
              <q-td :props="props">
                <q-btn flat round size="sm" color="blue-8" icon="o_edit" @click="mulaiEditRiwayat(props.row)" />
                <q-btn
                  flat
                  round
                  size="sm"
                  color="red"
                  icon="o_delete"
                  :disable="props.row.jenis === 'initial'"
                  @click="konfirmasiHapusRiwayat(props.row)"
                />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="confirmHapusRiwayat" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-item>
            <q-item-section side>
              <q-icon color="red" name="fas fa-exclamation-circle" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle2">Hapus data riwayat ini?</q-item-label>
              <q-item-label caption lines="2">Aksi ini akan mengubah stok barang secara otomatis.</q-item-label>
            </q-item-section>
          </q-item>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Batal" color="primary" flat @click="batalHapusRiwayat" />
          <q-btn label="Hapus" color="red" unelevated @click="hapusRiwayat" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapState,mapGetters } from 'vuex';
import FotoInv from '@/components/FotoInv.vue';
export default {
components:{
  FotoInv
},
setup(){
  
    const columns = [
      {name: "noreg",label: "Nomor REG",align: "left",field:"noreg",sortable: true},
      {name: "katalog",label: "Katalog",align: "left",field:"katalog",sortable: true},
      {name: "albah",label: "Nama Alat/Bahan",align: "left",field:"nabar",sortable: true},
      {name: "thn_masuk",label: "Tahun Masuk",align: "left",field:"thn_masuk",sortable: true},
      {name: "thn_pakai",label: "Tahun Pakai",align: "left",field:"thn_pakai",sortable: true},
      {name: "jenis_barang",label: "Jenis Barang",align: "left",field:"jenis_barang",sortable: true},
      {name: "jml",label: "Jumlah",align: "left",field:"jml",sortable: true},
      {name: "baik",label: "Baik",align: "left",field:"konbaik",sortable: true},
      {name: "rusak",label: "Rusak",align: "left",field:"konrusak",sortable: true},
      {name: "lokasi",label: "Lokasi",align: "left",field:"lokasi",sortable: true},
      {name: "aksi",align: "left", label:"aksi"},
    ];

    const riwayatColumns = [
      { name: 'tahun', label: 'Tahun', align: 'left', field: 'tahun', sortable: true },
      { name: 'jenis', label: 'Jenis', align: 'left', field: 'jenis', sortable: true },
      { name: 'qty', label: 'Qty', align: 'left', field: 'qty', sortable: true },
      { name: 'keterangan', label: 'Keterangan', align: 'left', field: 'keterangan', sortable: true },
      { name: 'aksi_riwayat', label: 'Aksi', align: 'left', field: 'aksi_riwayat', sortable: false },
    ]

    const jenisBarangOptions = [
      { label: 'Aset', value: 'aset' },
      { label: 'Habis Pakai', value: 'habis_pakai' },
    ]

    const filterJenisBarangOptions = [
      { label: 'Aset', value: 'aset' },
      { label: 'Barang Sekali Pakai', value: 'habis_pakai' },
    ]

    const jenisMutasiOptions = [
      { label: 'Penambahan', value: 'masuk' },
      { label: 'Pemakaian', value: 'keluar' },
      { label: 'Rusak', value: 'rusak' },
      { label: 'Pemutihan', value: 'pemutihan' },
    ]

    const kondisiPemutihanOptions = [
      { label: 'Rusak', value: 'rusak' },
      { label: 'Baik', value: 'baik' },
    ]

    const stokStatusOptions = [
      { label: 'Menipis', value: 'menipis' },
      { label: 'Habis', value: 'habis' },
    ]

    const rusakOnlyOptions = [
      { label: 'Hanya Rusak', value: 1 },
    ]

    const pemutihanOnlyOptions = [
      { label: 'Ada Pemutihan', value: 1 },
    ]

    return{
      pagination: {
          rowsPerPage: 15
         },
        columns,
        riwayatColumns,
        jenisBarangOptions,
        filterJenisBarangOptions,
        jenisMutasiOptions,
        kondisiPemutihanOptions,
        stokStatusOptions,
        rusakOnlyOptions,
        pemutihanOnlyOptions,
        rows:ref([]),
        filter:ref(null),
        filterJenisBarang:ref(null),
        rusakOnly:ref(null),
        pemutihanOnly:ref(null),
        filterTahun:ref(null),
        filterStokStatus:ref(null),
        tahunOptions:ref([]),
        dialogInsert:ref(false),
        dialogDetail:ref(false),
        confirm:ref(false),
        dialogRiwayat:ref(false),
        confirmHapusRiwayat:ref(false),
        loading:ref(false),
        riwayatHeader:ref({}),
        detailTerpilih:ref({}),
        riwayatItems:ref([]),
        savingStok:ref(false),
        editingRiwayatId:ref(null),
        riwayatTerpilihHapus:ref(null),
    }
},
data:()=>({
    form:{
        id:"",
        noreg:"",
        katalog:"",
        nabar:"",
        satuan:"",
        vol:"",
        merek:"",
        tipe:"",
        produsen:"",
        asal:"",
        thn_masuk:"",
        thn_pakai:"",
        jml:"",
        konbaik:"",
        konrusak:"",
        lokasi:"",
        spec:"",
        jenis_barang:"aset",
        stok_minimum:null,
    },
    formTambahStok:{
      inventaris_id:"",
      tahun: String(new Date().getFullYear()),
      qty: 1,
      jenis: 'masuk',
      kondisi_asal: 'rusak',
      keterangan: '',
    }
}),
computed:{
...mapState("kontrol",["url"]),
...mapState("kontrol",["triger"]),
...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    filteredRows(){
      if (!this.filterJenisBarang) return this.rows
      return this.rows.filter(row => (row.jenis_barang || 'aset') === this.filterJenisBarang)
    },
    isHabisPakaiForm(){
      return this.form.jenis_barang === 'habis_pakai'
    },
},
watch:{
 triger(){
  this.getData();
 },
 'form.jml'(){
   this.syncKondisiAset()
 },
 'form.konrusak'(){
   this.syncKondisiAset()
 },
 'form.jenis_barang'(){
   this.syncKondisiAset()
 }
},
methods:{
    batal(){
        this.dialogInsert=false;
        this.confirm=false;
        this.form.id="";
        this.form.noreg="";
        this.form.katalog="";
        this.form.nabar="";
        this.form.satuan="";
        this.form.vol="";
        this.form.merek="";
        this.form.tipe="";
        this.form.produsen="";
        this.form.asal="";
        this.form.thn_masuk="";
        this.form.thn_pakai="";
        this.form.jml="";
        this.form.konbaik="";
        this.form.konrusak="";
        this.form.lokasi="";
        this.form.spec="";
        this.form.jenis_barang="aset";
        this.form.stok_minimum=null;
    },
    konfirmasi($id){
      this.form.id=$id
      this.confirm=true
    },
    bukaDetail(row){
      this.detailTerpilih = row
      this.dialogDetail = true
    },
    tutupDetail(){
      this.dialogDetail = false
      this.detailTerpilih = {}
    },
    tampilDetail(value){
      return value === null || value === undefined || value === '' ? '-' : value
    },
    formNumber(value){
      const parsed = Number(value)
      return Number.isFinite(parsed) ? parsed : 0
    },
    hitungKondisiBaik(jml = this.form.jml, konrusak = this.form.konrusak){
      return Math.max(this.formNumber(jml) - this.formNumber(konrusak), 0)
    },
    batasiKondisiRusak(){
      const jumlah = this.formNumber(this.form.jml)
      const rusak = this.formNumber(this.form.konrusak)
      if (rusak > jumlah) {
        this.form.konrusak = jumlah
      }
    },
    ubahJumlah(value){
      this.form.jml = this.formNumber(value)
      this.batasiKondisiRusak()
      this.syncKondisiAset()
    },
    ubahKondisiRusak(value){
      const jumlah = this.formNumber(this.form.jml)
      const nilaiBersih = String(value ?? '').replace(/\D/g, '')
      if (nilaiBersih === '') {
        this.form.konrusak = ''
        this.form.konbaik = this.hitungKondisiBaik(this.form.jml, 0)
        return
      }

      const nilaiInput = this.formNumber(nilaiBersih)
      const rusak = Math.min(nilaiInput, jumlah)
      this.form.konrusak = rusak
      this.form.konbaik = this.hitungKondisiBaik(this.form.jml, rusak)
      if (nilaiInput > jumlah) {
        this.$nextTick(() => {
          const input = this.$refs.konrusakInput?.$el?.querySelector('input')
          if (!input) return
          input.value = String(rusak)
          input.select()
        })
      }
    },
    syncKondisiAset(){
      if (this.isHabisPakaiForm) return
      this.batasiKondisiRusak()
      this.form.konbaik = this.hitungKondisiBaik()
    },
    async simpan(){
      const payload = { ...this.form }
      if (payload.jenis_barang === 'habis_pakai') {
        payload.konbaik = payload.jml
        payload.konrusak = 0
      } else {
        payload.konrusak = Math.min(this.formNumber(payload.konrusak), this.formNumber(payload.jml))
        payload.konbaik = this.hitungKondisiBaik(payload.jml, payload.konrusak)
      }

      await axios.post("inventaris", payload).then((response)=>{
        this.batal()
        this.getData()
        this.$toast.success(`berhasil tersimpan`)
        return response
      }).catch((error)=>{
        this.$toast.error(`Gagal, Mohon Cek kebali`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
        return error
      })
    },
    async getData(){
      this.loading=true
      const query = {}
      if (this.rusakOnly) query.rusak_only = 1
      if (this.pemutihanOnly) query.pemutihan_only = 1
      if (this.filterTahun) query.tahun = this.filterTahun
      if (this.filterStokStatus) query.stok_status = this.filterStokStatus
      const params = { params: query }

      await axios.get("inventaris", params).then((response)=>{
        this.rows=response.data
      }).finally(()=>{
        this.loading=false
      })
    },
    async edit($id){
      await axios.get("inventaris/"+$id).then((response)=>{
        this.dialogInsert=true
        this.form.id=response.data.id;
        this.form.noreg=response.data.noreg;
        this.form.katalog=response.data.katalog;
        this.form.nabar=response.data.nabar;
        this.form.satuan=response.data.satuan;
        this.form.vol=response.data.vol;
        this.form.merek=response.data.merek;
        this.form.tipe=response.data.tipe;
        this.form.produsen=response.data.produsen;
        this.form.asal=response.data.asal;
        this.form.thn_masuk=response.data.thn_masuk;
        this.form.thn_pakai=response.data.thn_pakai;
        this.form.jml=response.data.jml;
        this.form.konbaik=response.data.konbaik;
        this.form.konrusak=response.data.konrusak;
        this.form.lokasi=response.data.lokasi;
        this.form.spec=response.data.spec;
        this.form.jenis_barang=response.data.jenis_barang || 'aset';
        this.form.stok_minimum=response.data.stok_minimum;
      })
    },
    async hapus(){
      await axios.delete("inventaris/"+this.form.id).then((response)=>{
        this.batal()
        this.$toast.success(`berhasil dihapus`)
        this.getData()
        return response
      })
    },
    async bukaRiwayat(row){
      this.dialogRiwayat = true
      this.riwayatHeader = row
      this.formTambahStok.inventaris_id = row.id
      await this.getRiwayat(row.id)
    },
    tutupRiwayat(){
      this.riwayatHeader = {}
      this.riwayatItems = []
      this.editingRiwayatId = null
      this.confirmHapusRiwayat = false
      this.riwayatTerpilihHapus = null
      this.formTambahStok.inventaris_id = ""
      this.formTambahStok.tahun = String(new Date().getFullYear())
      this.formTambahStok.qty = 1
      this.formTambahStok.jenis = 'masuk'
      this.formTambahStok.kondisi_asal = 'rusak'
      this.formTambahStok.keterangan = ''
    },
    async getRiwayat(id){
      await axios.get(`inventaris/${id}/riwayat`).then((response)=>{
        this.riwayatItems = response.data.items || []
      })
    },
    async tambahStok(){
      if (!this.formTambahStok.inventaris_id) return

      if (this.formTambahStok.jenis === 'pemutihan' && !String(this.formTambahStok.keterangan || '').trim()) {
        this.$toast.error('Keterangan wajib diisi untuk pemutihan')
        return
      }

      this.savingStok = true
      const payload = {
        tahun: this.formTambahStok.tahun,
        qty: this.formTambahStok.qty,
        jenis: this.formTambahStok.jenis,
        keterangan: this.formTambahStok.keterangan,
      }

      if (this.formTambahStok.jenis === 'pemutihan') {
        payload.kondisi_asal = this.formTambahStok.kondisi_asal || 'rusak'
      }

      const request = this.editingRiwayatId
        ? axios.put(`inventaris/${this.formTambahStok.inventaris_id}/riwayat/${this.editingRiwayatId}`, payload)
        : axios.post(`inventaris/${this.formTambahStok.inventaris_id}/tambah-stok`, payload)

      await request.then((response)=>{
        this.$toast.success(response.data.message || 'Riwayat stok berhasil disimpan')
        this.getData()
        this.getRiwayat(this.formTambahStok.inventaris_id)
        this.batalEditRiwayat()
      }).catch((error)=>{
        this.$toast.error(error?.response?.data?.message || 'Gagal menyimpan riwayat')
      }).finally(()=>{
        this.savingStok = false
      })
    },
    mulaiEditRiwayat(item){
      this.editingRiwayatId = item.id
      this.formTambahStok.tahun = String(item.tahun)
      this.formTambahStok.qty = Number(item.qty)
      this.formTambahStok.jenis = item.jenis
      this.formTambahStok.kondisi_asal = item.jenis === 'pemutihan' ? (item.kondisi_asal || 'rusak') : 'rusak'
      this.formTambahStok.keterangan = item.keterangan || ''
    },
    batalEditRiwayat(){
      this.editingRiwayatId = null
      this.formTambahStok.tahun = String(new Date().getFullYear())
      this.formTambahStok.qty = 1
      this.formTambahStok.jenis = 'masuk'
      this.formTambahStok.kondisi_asal = 'rusak'
      this.formTambahStok.keterangan = ''
    },
    konfirmasiHapusRiwayat(item){
      if (item.jenis === 'initial') {
        this.$toast.error('Data awal tidak boleh dihapus')
        return
      }

      this.riwayatTerpilihHapus = item
      this.confirmHapusRiwayat = true
    },
    batalHapusRiwayat(){
      this.confirmHapusRiwayat = false
      this.riwayatTerpilihHapus = null
    },
    async hapusRiwayat(){
      if (!this.riwayatTerpilihHapus) return

      await axios.delete(`inventaris/${this.formTambahStok.inventaris_id}/riwayat/${this.riwayatTerpilihHapus.id}`)
        .then((response)=>{
          this.$toast.success(response.data.message || 'Riwayat berhasil dihapus')
          this.batalHapusRiwayat()
          this.getData()
          this.getRiwayat(this.formTambahStok.inventaris_id)
        }).catch(()=>{
          this.$toast.error('Gagal menghapus riwayat')
        })
    },
    statusStok(row){
      const jml = Number(row.jml || 0)
      const minRaw = row.stok_minimum
      const min = minRaw === null || minRaw === undefined || minRaw === ''
        ? 5
        : Number(minRaw)

      if (jml <= 0) return { label: 'Habis', color: 'red' }
      if (jml <= min) return { label: 'Menipis', color: 'orange-8' }
      return { label: 'Aman', color: 'green-7' }
    },
    isNeedActionStock(row){
      const label = this.statusStok(row).label
      return label === 'Habis' || label === 'Menipis'
    },
    isBarangSekaliPakai(row){
      return row.jenis_barang === 'habis_pakai'
    },
    badgeJenisBarang(jenis){
      return (jenis || 'aset') === 'habis_pakai'
        ? { label: 'Barang Sekali Pakai', color: 'blue-8' }
        : { label: 'Aset', color: 'green-7' }
    },
    labelJenisBarang(jenis){
      return (jenis || 'aset') === 'habis_pakai' ? 'Habis Pakai' : 'Aset'
    },
    labelJenis(jenis){
      if (jenis === 'initial') return 'Data Awal'
      if (jenis === 'masuk') return 'Penambahan'
      if (jenis === 'keluar') return 'Pemakaian'
      if (jenis === 'rusak') return 'Rusak'
      if (jenis === 'pemutihan') return 'Pemutihan'
      return jenis
    },
    labelKondisiPemutihan(kondisi){
      return (kondisi || 'rusak') === 'baik' ? 'Baik' : 'Rusak'
    },
    badgeJenis(item){
      const jenis = typeof item === 'string' ? item : item?.jenis
      const kondisiAsal = typeof item === 'string' ? 'rusak' : item?.kondisi_asal
      if (jenis === 'initial') return { label: 'Data Awal', color: 'grey-7' }
      if (jenis === 'masuk') return { label: 'Penambahan', color: 'green-7' }
      if (jenis === 'keluar') return { label: 'Pemakaian', color: 'orange-8' }
      if (jenis === 'rusak') return { label: 'Rusak', color: 'deep-orange-7' }
      if (jenis === 'pemutihan') return { label: `Pemutihan ${this.labelKondisiPemutihan(kondisiAsal)}`, color: 'red-8' }
      return { label: this.labelJenis(jenis), color: 'blue-grey-7' }
    },
    onJenisMutasiChange(jenis){
      if (jenis === 'pemutihan' && !this.formTambahStok.kondisi_asal) {
        this.formTambahStok.kondisi_asal = 'rusak'
      }
    },
    setTahunOptions(){
      const current = new Date().getFullYear()
      const start = 2000
      const options = []
      for (let y = current; y >= start; y--) {
        options.push(String(y))
      }
      this.tahunOptions = options
    }
},
created(){
  this.setTahunOptions()
  this.getData();
}
}
</script>
<style lang="sass">
.my-sticky-column-table
  /* specifying max-width so the example can
    highlight the sticky column on any browser window */
  max-width: 100%



  thead tr:nth-child(1) th:nth-child(1)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

  thead tr:nth-child(1) th:nth-child(2)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

  thead tr:nth-child(1) th:nth-child(3)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

    
  td:nth-child(1),
  td:nth-child(1)

    background-color: #F1F8E9

  td:nth-child(2),
  td:nth-child(3)

    background-color: #F1F8E9


  td:nth-child(3)
    background-color: #F1F8E9


    
  th:nth-child(1),
  td:nth-child(1)
    position: sticky
    left: 0
    z-index: 1

  th:nth-child(2),
  td:nth-child(2)
    position: sticky
    left: 120px
    z-index: 2

  th:nth-child(3),
  td:nth-child(3)
    position: sticky
    left: 190px
    z-index: 3
    padding-right: 30px


  

  .my-sticky-header-table
  .q-table thead tr th
        background: rgb(248, 249, 251)

  .q-table--no-wrap td
      white-space: pre-wrap !important

</style>
