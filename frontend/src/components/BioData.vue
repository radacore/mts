<template>
  <div class="q-py-md">
    <q-card flat>
        <q-card-section>
            <span class="text-h6">Data User</span>
            <q-form>
                <q-input outlined v-model="form.nip" class="q-my-sm" dense label="NIP/NIK/NIM" />
                <q-input outlined v-model="form.hp" class="q-my-sm" dense label="No Handphone" />
            </q-form>
            <q-btn @click="update" label="Update" color="green-7" no-caps style="width:100px"/>
        </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { reactive } from '@vue/reactivity'
import axios from 'axios'
export default {
    name:'BioData',
setup(){
    const form=reactive({
        id:"",
        nip:"",
        hp:"",
    })
    return{
        form,
    }
},
methods:{
    async getBio(){
        await axios.get("biodata").then((response)=>{
            this.form.id=response.data.id
            this.form.nip=response.data.nip
            this.form.hp=response.data.hp
        })
    },
    async update(){
        await axios.put("biodata",this.form).then((response)=>{
            this.getBio()
            return response
        })
    }
},
created(){
    this.getBio()

}
}
</script>

