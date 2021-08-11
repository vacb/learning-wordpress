import $ from "jquery";

class Like {
  constructor() {
    this.events();
  }

  events() {
    $(".like-box").on("click", this.ourClickDispatcher.bind(this));
  }

  // Methods
  ourClickDispatcher(e) {
    let currentLikeBox = $(e.target).closest(".like-box");

    if (currentLikeBox.data("exists") == "yes") {
      this.deleteLike();
    } else {
      this.createLike();
    }
  }

  createLike() {
    alert("create test message");
  }

  deleteLike() {
    alert("delete test message");
  }
}

export default Like;
