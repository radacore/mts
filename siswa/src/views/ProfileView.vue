<template>
  <q-page padding>
    <div class="row justify-center q-my-xl">
      <div class="col-12 col-md-8">
        <q-card class="my-card shadow-10">
          <q-card-section class="bg-green-7 text-white">
            <div class="text-h6">Profile Siswa</div>
          </q-card-section>

          <q-card-section class="text-center relative-position">
            <q-avatar size="150px" class="shadow-10 q-mb-md relative-position">
                <img :src="url+user.pp.foto" v-if="user.pp && user.pp.foto">
                <img :src="`https://ui-avatars.com/api/?name=${user.user.name}&background=random&size=150`" v-else>
                
                <!-- Tombol Ganti Foto -->
                <q-btn 
                  round color="primary" 
                  icon="edit" 
                  size="sm" 
                  class="absolute-bottom-right" 
                  style="bottom: 0; right: 0;"
                  @click="dialogFoto = true"
                >
                  <q-tooltip>Ganti Foto</q-tooltip>
                </q-btn>
            </q-avatar>
            <div class="text-h5 text-weight-bold">{{ user.user.name }}</div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <q-list separator>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Username</q-item-label>
                  <q-item-label class="text-subtitle1">{{ user.user.username }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                   <q-btn flat round icon="edit" color="primary" @click="openDialogUsername" />
                </q-item-section>
              </q-item>
              
               <!-- Menampilkan Kelas (Read Only) -->
               <q-item v-if="user.kelas">
                <q-item-section>
                  <q-item-label caption>Kelas</q-item-label>
                  <q-item-label class="text-subtitle1">{{ user.kelas.kelas }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-icon name="school" color="grey" />
                </q-item-section>
              </q-item>

               <q-item>
                <q-item-section>
                  <q-item-label caption>Password</q-item-label>
                  <q-item-label>********</q-item-label>
                </q-item-section>
                 <q-item-section side>
                   <q-btn flat round icon="lock_reset" color="primary" @click="openDialogPassword" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Dialog Ganti Foto -->
    <q-dialog v-model="dialogFoto">
      <q-card style="width: 300px">
        <q-card-section>
          <div class="text-h6">Ganti Foto Profil</div>
        </q-card-section>
        <q-card-section>
           <div class="row q-col-gutter-sm">
             <div class="col-12">
               <q-file filled bottom-slots v-model="fileFoto" label="Pilih Foto" accept=".jpg, .jpeg, .png">
                <template v-slot:prepend>
                  <q-icon name="cloud_upload" @click.stop.prevent />
                </template>
                <template v-slot:append>
                  <q-icon name="close" @click.stop.prevent="fileFoto = null" class="cursor-pointer" />
                </template>
              </q-file>
             </div>
             <!-- Tombol Hapus Foto (Muncul jika user punya foto custom) -->
             <div class="col-12" v-if="user.pp && user.pp.foto">
                <q-btn 
                  outline 
                  color="negative" 
                  label="Hapus Foto Saat Ini" 
                  icon="delete" 
                  class="full-width"
                  @click="deleteFoto"
                />
             </div>
           </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn flat label="Simpan" color="primary" @click="uploadFoto" :disable="!fileFoto" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog Ganti Username -->
    <q-dialog v-model="dialogUsername">
      <q-card style="width: 300px">
        <q-card-section>
          <div class="text-h6">Ganti Username</div>
        </q-card-section>
        <q-card-section>
          <q-input v-model="editData.username" label="Username Baru" dense autofocus />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn flat label="Simpan" color="primary" @click="updateProfile('username')" :disable="!editData.username" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog Ganti Password -->
    <q-dialog v-model="dialogPassword">
      <q-card style="width: 300px">
        <q-card-section>
          <div class="text-h6">Ganti Password</div>
        </q-card-section>
        <q-card-section>
          <q-input 
            v-model="editData.password" 
            label="Password Baru" 
            type="password" 
            dense 
            autofocus 
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn flat label="Simpan" color="primary" @click="updateProfile('password')" :disable="!editData.password" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
import { mapGetters, mapState, mapActions } from 'vuex';
import axios from 'axios';
import { ref, reactive } from 'vue';

export default {
  name: 'ProfileView',
  setup() {
    const dialogFoto = ref(false);
    const dialogUsername = ref(false);
    const dialogPassword = ref(false);
    const fileFoto = ref(null);
    const editData = reactive({
      username: '',
      password: '',
    });

    return {
      dialogFoto,
      dialogUsername,
      dialogPassword,
      fileFoto,
      editData,
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
    ...mapState('kontrol', ['url']),
  },
  methods: {
    ...mapActions('auth', ['getUser']), // Action untuk refresh user data

    openDialogUsername() {
      this.editData.username = this.user.user.username;
      this.dialogUsername = true;
    },

    openDialogPassword() {
      this.editData.password = '';
      this.dialogPassword = true;
    },

    async uploadFoto() {
      if (!this.fileFoto) return;

      const formData = new FormData();
      formData.append('foto', this.fileFoto);

      try {
        await axios.post('profiles', formData);
        this.$toast.success('Foto profil berhasil diperbarui');
        this.dialogFoto = false;
        this.fileFoto = null;
        this.getUser(); // Refresh data user
      } catch (error) {
        console.error(error);
        this.$toast.error('Gagal mengupload foto');
      }
    },

    async deleteFoto() {
        if(!confirm('Apakah Anda yakin ingin menghapus foto profil?')) return;
        
        try {
            await axios.delete('profiles/foto');
            this.$toast.success('Foto profil berhasil dihapus');
            this.dialogFoto = false;
            this.getUser(); // Refresh data -> akan kembali ke inisial
        } catch (error) {
            console.error(error);
            this.$toast.error('Gagal menghapus foto');
        }
    },

    async updateProfile(type) {
      const payload = {
        id: this.user.user.id,
        // Jika sedang ganti username, name juga ikut diupdate jadi username baru
        name: type === 'username' ? this.editData.username : this.user.user.name,
        email: this.user.user.email, 
        username: type === 'username' ? this.editData.username : this.user.user.username,
      };

      if (type === 'password') {
        payload.password = this.editData.password;
      }

      try {
        await axios.post('profiles/update', payload);
        this.$toast.success(`Berhasil mengubah ${type}`);
        
        if (type === 'username') this.dialogUsername = false;
        if (type === 'password') this.dialogPassword = false;

        this.getUser(); // Refresh data user
      } catch (error) {
        console.error(error);
        this.$toast.error(`Gagal mengubah ${type}`);
      }
    }
  }
}
</script>

<style scoped>
.my-card {
  width: 100%;
}
</style>
