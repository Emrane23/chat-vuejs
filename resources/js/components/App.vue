<template>
  <div class="card">
    <div class="card-header">Chats</div>

    <div
      class="card-body"
      @scroll="onScroll"
      ref="card-messages"
      style="overflow-y: scroll; height: 300px"
    >
      <div class="text-center">
        <div
          v-if="loading"
          class="spinner-border spinner-border-sm text-secondary"
          role="status"
        >
          <span class="sr-only"></span>
        </div>
      </div>
      <chat-messages :messages="messages" :user="user"></chat-messages>
    </div>
    <div class="card-footer">
      <chat-form v-on:messagesent="addMessage" :user="user"></chat-form>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],
  data() {
    return {
      messages: "",
      paginate: 5,
      loading: false,
    };
  },
  mounted() {
    this.scrollBottom();
  },
  //Upon initialisation, run fetchMessages().
  created() {
    this.fetchMessages();

    window.Echo.private("chat").listen("MessageSent", (e) => {
      this.messages.push({
        message: e.message.message,
        user: e.user,
      });
      document.querySelectorAll(".messages").forEach((item) => {
        item.classList.remove("new");
      });
      let newLength = this.messages.length - 1;

      if (!("Notification" in window)) {
        alert("Web Notification is not supported");
        return;
      }
      if (e.user.name != this.user.name) {
        Notification.requestPermission((permission) => {
          let notification = new Notification("New message alert!", {
            body: e.user?.name + " has sent a message", // content for the alert
            icon: "https://www.svgrepo.com/show/31480/notification-bell.svg", // optional image url
          });

          // link to page on clicking the notification
          notification.onclick = () => {
            window.open(window.location.href);
          };
        });
      }
      this.scrollBottom();
      setTimeout(() => {
        document.querySelectorAll(".messages")[newLength].classList.add("new");
      }, "300");
    });
  },

  methods: {
    fetchMessages() {
      //GET request to the messages route in our Laravel server to fetch all the messages
      axios.get("/messages").then((response) => {
        //Save the response in the messages array to display on the chat view
        this.messages = response.data;
      });
    },

    scrollBottom() {
      var container = document.querySelector(".card-body");
      var scrollHeight = container.scrollHeight;
      var newScroll = scrollHeight + 3000;

      setTimeout(() => {
        container.scrollTop = newScroll;
      }, "300");
    },

    showMore() {
      this.paginate += 5;
      this.loading = true;
      axios
        .get(`/showmore/${this.paginate}`)
        .then((response) => {
          this.messages = response.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    //Receives the message that was emitted from the ChatForm Vue component
    addMessage(message) {
      this.messages.push(message);
        axios.post("/messages", message).then((response) => {});
      this.scrollBottom();
    },
    onScroll(e) {
      const { scrollTop, offsetHeight, scrollHeight } = e.target;
      if (scrollTop + offsetHeight >= scrollHeight) {
        console.log("bottom!");
      }
      if (scrollTop == 0) {
        this.showMore();
      }
    },
  },
};
</script>

<style>
.messages.new {
  background-color: #fff;
  animation-name: MessageSent;
  -webkit-animation-name: MessageSent;
  animation-duration: 6s;
  -webkit-animation-duration: 6s;
  animation-iteration-count: 1;
  -webkit-animation-iteration-count: 1;
}
@keyframes MessageSent {
  from {
    background-color: rgb(241, 245, 24);
  }
  to {
    background-color: inherit;
  }
}
</style>