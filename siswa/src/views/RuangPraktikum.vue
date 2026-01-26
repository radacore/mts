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

        <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <q-card-section>
                <div class="row justify-start q-col-gutter-md">
                    <div v-for="row in classroom" :key="row.id" class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                        <q-card class="bayangan">
                            <q-card-section class="bg-primary text-white">
                                <div class="text-h6">{{row.katalog.topik}}</div>
                                <div class="text-subtitle2">{{row.kelas.kelas}}</div>
                              </q-card-section>
                              <q-separator/>
                            <q-card-section>
                             <span class="text-caption">By {{row.user.name}}</span>
                            </q-card-section>
                            <q-card-actions align="right">
                                <q-btn flat color="primary" :to="{ name: 'labroom', params: { class_id: row.id }}">Masuk Lab</q-btn>
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
        await axios.get("labsiswa").then((response)=>{
            this.classroom=response.data
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

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>

