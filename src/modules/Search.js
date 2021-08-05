import $ from "jquery";

class Search {
  // Describe and create/initiate object
  constructor() {
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.events();
    this.isOverlayOpen = false;
  }

  // Events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    // keyup fires once, keydown fires multiple times as long as you hold down the key
    // Added loging in keyPressDispatcher below to ensure it only calls once either way
    $(document).on("keydown", this.keyPressDispatcher.bind(this));
  }

  // Methods (function/action)
  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    // Add class that removes scroll bar as soon as overlay is opened by hiding overflows
    $("body").addClass("body-no-scroll");
    this.isOverlayOpen = true;
    // console.log("Open method ran");
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
    // console.log("Close method ran");
  }

  keyPressDispatcher(keyPressed) {
    // keyCode deprecated
    // console.log(key.keyCode);

    if (keyPressed.key == "s" && !this.isOverlayOpen) {
      this.openOverlay();
    }
    if (keyPressed.key == "Escape" && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }
}

export default Search;
