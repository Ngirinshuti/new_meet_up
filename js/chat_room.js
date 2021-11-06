window.addEventListener("DOMContentLoaded", (e) => {
  // intial scrolling

  const chatsListContainer = document.querySelector(".chatMessageList");
  chatsListContainer
    .querySelector(".chatMessageGroup:last-of-type")
    .scrollIntoView(false);
  document.body.scrollIntoView(false);

  // send a message
  const messageInput = document.querySelector("[data-chat-input]");
  const sendButton = document.querySelector("[data-chat-send]");

  // send pressing ENTER
  messageInput.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendMessage(messageInput.value);
    }
  });
  // send using a button
  sendButton.addEventListener("click", (e) => sendMessage(messageInput.value));
});

function sendMessage(msg) {
  if (msg.trim() === "") {
    return showMsg("Empty message");
  }

  showMsg("Sent");
}

function showMsg(msg) {
  alert(msg);
}

async function request(url, body = null) {
  const method = body ? "POST" : "GET";

  const res = await fetch(url, { method, ...(body ? { body } : {}) });
  return await res.json();
}
