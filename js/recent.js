const recentLinks = document.querySelectorAll(".recentChatLink");

recentLinks.forEach((link) => {
  link.closest(".recentChat").addEventListener("click", (e) => {
    if (e.target.matches(".recentChat")) {
      link.click();
    }
  });
});
