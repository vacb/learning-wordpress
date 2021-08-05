import $ from "jquery";

class Search {
  // Describe and create/initiate object
  constructor() {
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.events();
  }

  // Events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
  }

  // Methods (function/action)
  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
  }
}

export default Search;
