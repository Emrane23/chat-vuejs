<template>
  <div class="col-3">
      <div class="card card-default">
        <div class="card-header">List Users</div>
        <div class="card-body">
            <template v-for="user in users" :key="user.id">
                <span class="text-success">• </span><a :href="'/chatprivate?receiverid='+user.id" style="text-decoration: none">{{ user.name }}</a> <br>
            </template>
        </div>
      </div>
    </div>
</template>

<script>
export default {
data(){
    return {
        users: "",
    }
},
mounted() {
  this.getListUsers();
},
methods:{
    getListUsers(){
        axios.get("/users").then((response) => {
        this.users = response.data;
      });
    }
}
}
</script>

<style>

</style>