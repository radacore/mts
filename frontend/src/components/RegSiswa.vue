<template>
  <div>
    <q-badge v-if="cekin" color="green" label="ACTIVE" />
    <q-badge v-else color="grey" label="INACTIVE" />
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from '@vue/reactivity';

export default {
props:["email",'dataId'],
setup(){
    return{
        cekin:ref(""),
    }
},
watch:{
email(){
    this.cekUser()
}
},
methods:{
    async cekUser(){
        await axios.get("cekUser/"+this.email).then((response)=>{
            this.cekin=response.data.id
        })
    },
},
created(){
this.cekUser()
}
}
</script>

