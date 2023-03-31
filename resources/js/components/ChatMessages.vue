<template >
  <ul class="chat" >
    <li  class="messages" v-for="message in messages" :key="message.id">
      <div class="clearfix">
        <div class="header">
          <strong>
            {{ message.user.name }}
          </strong>
        </div>
        <p >
          {{ message.message }}
        </p>
      </div>
    </li>
  </ul>
</template>
<script>
export default {
  props: ["messages","user"],
  data(){
    return{

    }
  }
};
</script>
