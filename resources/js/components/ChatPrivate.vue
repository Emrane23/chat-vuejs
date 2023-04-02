<template>
  <div class="card">
    <div class="card-header">
      <h3>{{ receiver.name }}</h3>
    </div>

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
      <ul class="chat">
        <li class="messages" v-for="message in messages" :key="message.id">
          <div class="clearfix">
            <div class="header">
              <strong>
                {{ message.sender.name }}
              </strong>
            </div>
            <p>
              {{ message.message }}
            </p>
          </div>
        </li>
      </ul>
    </div>
    <div class="card-footer">
      <div class="input-group">
        <input
          id="btn-input"
          type="text"
          class="form-control input-sm"
          placeholder="Type your message here..."
          v-model="newMessage"
          @keyup.enter="sendMessage"
        />
        
        <span class="input-group-btn">
            <button
            class="btn btn-primary btn-sm"
            id="btn-chat"
            @click="sendMessage"
            >
            Send
        </button>
    </span>
    
      </div>
    </div>
  </div>
</template>
  
  <script>
  // import EmojiPicker from 'vue3-emoji-picker';
  // import 'vue3-emoji-picker/css';
  

export default {
  props: ["user", "receiverid"],
  // emits: ["messagesent"],

  data() {
    return {
      messages: "",
      newMessage: "",
      receiver: "",
      paginate: 5,
      loading: false,
      userTyper: false,
      typingTimer: false,
    };
  },
  // components: {
  //   EmojiPicker,
  // },
  mounted() {
    this.scrollBottom();
    this.getReceiver();
  },
  //Upon initialisation, run fetchMessages().
  created() {
    this.fetchMessages();

      Echo.join(`privatechat.${this.user.id}.${this.receiverid}`)
      .listen("PrivateMessageSent", (e) => {
        // console.log("event work");
        console.log(e);
        this.messages.push({
          message: e.message.message,
          sender: e.user,
        });
        document.querySelectorAll(".messages").forEach((item) => {
          item.classList.remove("new");
        });
        let newLength = this.messages.length - 1;
        let audio =new Audio ('/notification/notification.mp3');

        if (!("Notification" in window)) {
          alert("Web Notification is not supported");
          return;
        }
        // if (e.user.name != this.user.name) {
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
        // }
        audio.play();
        this.scrollBottom();
        setTimeout(() => {
          document.querySelectorAll(".messages")[newLength].classList.add("new");
        }, "300");
      })/* .listenForWhisper('typing', user => {
        console.log("étyping");
        this.userTyper = user;
        console.log("type");
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }
         this.typingTimer = setTimeout(() => {
          this.userTyper = false;
        }, "3000");
      }); */
  },

  methods: {
     onSelectEmoji(emoji) {
  console.log(emoji)
  /*
    // result
    { 
        i: "😚", 
        n: ["kissing face"], 
        r: "1f61a", // with skin tone
        t: "neutral", // skin tone
        u: "1f61a" // without tone
    }
    */
},
    sendMessage() {
      this.messages.push({
        sender: this.user,
        message: this.newMessage,
      });
      axios
        .post(`/privatemessages/${this.receiverid}`, {
          message: this.newMessage,
        })
        .then((response) => {});
      this.newMessage = "";
      this.scrollBottom();
    },
    getReceiver() {
      axios.get(`/getreceiver/${this.receiverid}`).then((response) => {
        this.receiver = response.data;
      });
    },
    fetchMessages() {
      //GET request to the messages route in our Laravel server to fetch all the messages
      axios.get(`/privatemessages/${this.receiverid}`).then((response) => {
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