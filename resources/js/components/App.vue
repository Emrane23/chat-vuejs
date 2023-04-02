<template>
  <div class="row">
    <div class="col-8">
      <div class="card card-default">
        <div class="card-header">Group chat</div>

        <div
          class="card-body p-0"
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
          <chat-form
            v-on:messagesent="addMessage"
            :user="user"
            :userTyper="userTyper"
          ></chat-form>
        </div>
      </div>
    </div>
    <list-users></list-users>
  </div>
</template>

<script>
export default {
  props: ["user"],
  emits: ["messagesent"],

  data() {
    return {
      messages: "",
      paginate: 5,
      loading: false,
      userTyper: false,
      typingTimer: false,
    };
  },
  mounted() {
    this.scrollBottom();
  },
  //Upon initialisation, run fetchMessages().
  created() {
    this.fetchMessages();

    Echo.join("chat")
      .listen("MessageSent", (e) => {
        console.log(e);
        this.messages.push({
          message: e.message.message,
          user: e.user,
        });
        document.querySelectorAll(".messages").forEach((item) => {
          item.classList.remove("new");
        });
        let newLength = this.messages.length - 1;
        let audio = new Audio("/notification/notification.mp3");

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
        console.log("received");
        audio.play();
        this.scrollBottom();
        setTimeout(() => {
          document
            .querySelectorAll(".messages")
            [newLength].classList.add("new");
        }, "300");
      })
      .listenForWhisper("typing", (user) => {
        this.userTyper = user;
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }
        this.typingTimer = setTimeout(() => {
          this.userTyper = false;
        }, "3000");
      });
  },

  methods: {
    fetchMessages() {
      axios.get("/messages").then((response) => {
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