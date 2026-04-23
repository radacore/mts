<template>
  <q-page class="q-pa-md">
    <div class="row justify-center">
        <!-- ✅ BANNER GANTI PASSWORD UNTUK USER BARU -->
        <q-card v-if="isNewUser" class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 q-mb-md bg-orange-1">
          <q-card-section horizontal class="items-center">
            <q-card-section class="col-auto">
              <q-icon name="warning" color="orange" size="xl" />
            </q-card-section>
            <q-card-section class="col">
              <div class="text-h6 text-orange-9">Ganti Password Default</div>
              <div class="text-caption">Anda terdeteksi masih menggunakan password default (NIS). Demi keamanan akun, segera ganti password Anda.</div>
            </q-card-section>
            <q-card-actions vertical class="justify-around q-px-md">
              <q-btn flat to="/profile" label="Ganti Password" color="orange" icon="lock" no-caps />
            </q-card-actions>
          </q-card-section>
        </q-card>

        <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 classroom-panel" flat>
            <q-card-section>
                <div v-if="!loading && classroom.length === 0" class="text-center q-py-xl">
                  <q-icon name="o_school" size="48px" color="grey-5" />
                  <div class="text-h6 q-mt-md">Selamat datang, {{ user?.user?.name || 'Siswa' }}</div>
                  <div class="text-subtitle2 text-grey-7 q-mt-sm">
                    Saat ini kamu belum ada kelas praktikum.
                  </div>
                  <div class="text-caption text-grey q-mt-xs">
                    Silakan hubungi guru atau laboran untuk didaftarkan ke kelas.
                  </div>
                </div>

                <div v-else class="row justify-start q-col-gutter-md">
                    <div v-for="row in classroom" :key="row.id" class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                        <q-card class="bayangan classroom-card" flat>
                          <q-card-section class="classroom-card__head text-white">
                            <div class="row items-center justify-between no-wrap">
                              <q-icon name="o_science" size="26px" class="q-mr-sm" />
                              <q-chip dense color="white" text-color="green-9" icon="o_groups">
                                {{ row.kelas.kelas }}
                              </q-chip>
                            </div>
                            <div class="text-subtitle1 text-weight-bold q-mt-sm ellipsis-2-lines">
                              {{ row.katalog.topik }}
                            </div>
                          </q-card-section>

                          <q-card-section class="q-pt-md">
                            <div class="row items-center no-wrap">
                              <q-avatar color="green-1" text-color="green-8" icon="o_person" size="34px" />
                              <div class="q-ml-sm">
                                <div class="text-caption text-grey-6">Pengajar</div>
                                <div class="text-body2 text-weight-medium text-grey-9">{{ row.user.name }}</div>
                              </div>
                            </div>
                          </q-card-section>

                          <q-card-actions align="right" class="q-px-md q-pb-md">
                            <q-btn
                              unelevated
                              color="green-7"
                              icon="o_login"
                              :to="{ name: 'labroom', params: { class_id: row.id }}"
                              label="Masuk Lab"
                              no-caps
                            />
                          </q-card-actions>
                        </q-card>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
setup(){
    return{
        classroom:ref([]),
        loading:ref(false),
    }
},
computed:{
    ...mapGetters({
      user: "auth/user",
    }),
    isNewUser() {
        if (!this.user || !this.user.user) return false;
        return this.user.user.created_at === this.user.user.updated_at;
    }
},
methods:{
    async getClassroom(){
        this.loading = true
        await axios.get("labsiswa").then((response)=>{
            this.classroom=response.data
        }).finally(()=>{
            this.loading = false
        })
    }
},
created(){
this.getClassroom();
}
}
</script>
<style lang="sass">
.bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.2) !important
  transition: transform 0.3s ease
  &:hover
    transform: translateY(-5px)

.classroom-panel
  background: linear-gradient(180deg, #f8fff8 0%, #ffffff 100%)
  border: 1px solid #d7efdb

.classroom-card
  border-radius: 16px
  border: 1px solid #dfe9df
  overflow: hidden

.classroom-card__head
  background: linear-gradient(135deg, #1b5e20 0%, #43a047 100%)

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>
