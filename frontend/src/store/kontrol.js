import axios from 'axios'
export default {
    namespaced: true,
    state: {
        url:'https://localhost:8000/',
        triger:false,
        role:[],
        users:[],
        kelas:[],
        katalog:[],
    },
    getters: {
        role: (state) => state.role,
        users: (state) => state.users,
        kelas: (state) => state.kelas,
        katalog: (state) => state.katalog,
        triger(state){
            return state.triger
         },
    },
    mutations: {
        SET_ROLE(state,role) {
            state.role = role
        },
        SET_USERS(state,users) {
            state.users = users
        },
        SET_KELAS(state,kelas) {
            state.kelas = kelas
        },
        SET_KATALOG(state,katalog) {
            state.katalog = katalog
        },
        SET_TRIGER(state){
            state.triger=!state.triger
          },
    },
    actions: {
        async getRole({ commit }) {
            let res = await axios.get('role')
            commit('SET_ROLE', res.data)
        },
        async getUsers({ commit }) {
            let res = await axios.get('users')
            commit('SET_USERS', res.data)
        },
        async getKelas({ commit }) {
            let res = await axios.get('rombel')
            commit('SET_KELAS', res.data)
        },
        async getKatalog({ commit }) {
            let res = await axios.get('katalog')
            commit('SET_KATALOG', res.data)
        },
  
    }
}