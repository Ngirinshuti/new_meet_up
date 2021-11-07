window.addEventListener("DOMContentLoaded", (e) => {
  // intial scrolling

  const chatsListContainer = document.querySelector(".chatMessageList");
  setTimeout(() => {
    chatsListContainer.scrollTo({
      top: chatsListContainer.scrollHeight,
      behavior: "smooth",
    });
    // .querySelector(".chatMessageGroup:last-of-type")
    // .scrollIntoView(false);
  }, 0);
  document.body.scrollIntoView(false);

  // send a message
  const messageInput = document.querySelector("[data-chat-input]");
  const messageEditor = messageInput.nextElementSibling;
  const sendButton = document.querySelector("[data-chat-send]");

  // send pressing ENTER
  messageInput.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendMessage(messageInput.value);
    }
  });
  // send pressing ENTER
  messageEditor.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      messageInput.value = messageEditor.textContent;
      sendMessage(() => {
        messageEditor.textContent = "";
      });
    }
  });
  // send using a button
  sendButton.addEventListener("click", (e) => sendMessage(messageInput.value));

  function sendMessage(callback) {
    const msg = messageInput.value;

    if (msg.trim() === "") {
      return showMsg("Empty message!");
    }

    const reciever = messageInput.dataset.reciever;
    const groupId = messageInput.dataset.groupId;
    const csrfKey = messageInput.dataset.csrfKey;
    const csrfToken = messageInput.dataset.csrfToken;

    const body = new FormData();

    body.append(csrfKey, csrfToken);
    body.append("reciever", reciever);
    body.append("group_id", groupId);
    body.append("message", msg);

    request("../chat/send_message.php", body).then((data) => {
      // console.log({ data });
      addMessage(data);
      callback && callback();
      // window.location.reload();
    });
  }

  function showMsg(msg) {
    alert(msg);
  }

  // create message element
  function addMessage(msg) {
    const lastMessageDate = messageInput.dataset.lastMessageDate;
    const lastMessageSender = messageInput.dataset.lastMessageSender;
    const myUsername = messageInput.dataset.me;

    const messageTemp = document
      .querySelector("[data-message-template]")
      .content.cloneNode(true);

    const senderImg = messageTemp.querySelector("img");
    const body = messageTemp.querySelector(".chatMessageBody");
    const messageInfo = messageTemp.querySelector(".chatMessageInfo");
    const messageTime = messageTemp.querySelector(".chatMessageTime");
    const messageStatus = messageTemp.querySelector(".chatStatus");

    senderImg.src += msg.profile_pic;
    body.textContent = msg.body;
    messageTime.textContent = formatDate(msg.created_at);
    messageStatus.textContent = msg.status;

    if (
      !lastMessageDate.trim() ||
      lastMessageSender !== msg.sender ||
      !shouldCombine(msg.created_at, lastMessageDate)
    ) {
      const messageGroup = document.createElement("div");
      messageGroup.classList.add("chatMessageGroup");
      messageGroup.classList.add(
        msg.sender === myUsername.trim() ? "sent" : "recieved"
      );
      messageGroup.append(
        messageTemp.querySelector(".chatMessage"),
        messageInfo
      );
      messageGroup.prepend(messageTemp.querySelector(".chatMessageUser"));
      chatsListContainer.appendChild(messageGroup);
    } else {
      messageInfo.remove();
      const messageGroup = chatsListContainer.querySelector(
        ".chatMessageGroup:last-of-type"
      );

      messageGroup.insertBefore(
        messageTemp.querySelector(".chatMessage"),
        messageGroup.querySelector(".chatMessageInfo")
      );
    }

    messageInput.textContent = "";
    messageInput.setAttribute("data-last-message-date", msg.created_at);
    messageInput.setAttribute("data-last-message-sender", msg.sender);
    messageInput.value = "";
    chatsListContainer.scrollTo({
      top: chatsListContainer.scrollHeight,
      behavior: "smooth",
    });
  }

  // check for new messages

  function check_new_messages() {
    const lastMessageDate =
      messageInput.dataset.lastMessageDate.trim() ||
      new Date(new Date().setFullYear(1970, 12, 31)).toJSON();
    const groupId = messageInput.dataset.groupId;
    const chatFriend = messageInput.dataset.chatFriend;

    const url = `
    ../chat/check_new_messages.php?group_id=${groupId}&sender=${chatFriend}&last_message_date=${lastMessageDate}
    `;

    request(url).then((messages) => {
      messages.forEach((msg) => {
        addMessage(msg);
      });
    });
  }

  setInterval(check_new_messages, 3000);
});

function shouldCombine(date1, date2) {
  const diff = (new Date(date1) - new Date(date2)) / (1000 * 60);
  // console.log({ diff });
  if (1 > diff && diff > -1) {
    return true;
  }

  return false;
}

function formatDate(date) {
  const inputDate = Date.parse(date);
  const dateNow = Date.now();
  const diff = Math.abs((dateNow - inputDate) / 1000);
  const days = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];
  const months = [
    "Jan",
    "Feb",
    "Mar",
    "Aprl",
    "May",
    "June",
    "July",
    "Aug",
    "Sept",
    "Oct",
    "Nov",
    "Dec",
  ];
  const today = new Date(dateNow);
  const myDate = new Date(inputDate);
  const hours =
    myDate.getHours() < 10 ? `0${myDate.getHours()}` : myDate.getHours();
  const minutes =
    myDate.getMinutes() < 10 ? `0${myDate.getMinutes()}` : myDate.getMinutes();

  if (today.getFullYear() !== myDate.getFullYear()) {
    const monthName = months[myDate.getMonth()];
    return `${monthName} ${myDate.getDate()}, ${myDate.getFullYear()}`;
  }
  if (diff < 60) {
    return `now`;
  } else if (diff >= 60 && diff < 3600) {
    const result = Math.round(diff / 60);
    return result > 1 ? `${result} mins` : `1 min`;
  } else if (diff >= 3600 && diff < 86400) {
    if (today.getDay() === myDate.getDay()) {
      return `${hours}:${minutes}`;
    } else {
      return `Yesterday ${hours}:${minutes}`;
    }
  } else if (diff >= 86400 && diff < 86400 * 7) {
    const result = ``;
    const dayName = days[myDate.getDay()];
    const yester = days[today.getDay() - 1];

    if (dayName === yester) {
      return `Yesterday ${hours}:${minutes}`;
    } else {
      return `${dayName} ${hours}:${minutes}`;
    }
  } else {
    const monthName = months[myDate.getMonth()];
    return `${monthName} ${myDate.getDate()} ${hours}:${minutes}`;
  }
}

async function request(url, body = null) {
  const method = body ? "POST" : "GET";

  const res = await fetch(url, { method, ...(body ? { body } : {}) });
  return await res.json();
}
