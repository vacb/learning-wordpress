import $ from "jquery";

class Search {
  // Describe and create/initiate object
  constructor() {
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.resultsDiv = $(".search-overlay__results");
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  // Events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    // keyup fires once, keydown fires multiple times as long as you hold down the key
    // Added loging in keyPressDispatcher below to ensure it only calls once either way
    $(document).on("keydown", this.keyPressDispatcher.bind(this));
    // Keyup gives browser more time to register the input value, otherwise may not detect change
    this.searchField.on("keyup", this.typingLogic.bind(this));
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

    if (
      keyPressed.key == "s" &&
      !this.isOverlayOpen &&
      // Ensure that this doesn't happen for users typing 's' in any input or text fields
      !$("input, textarea").is(":focus")
    ) {
      this.openOverlay();
    }
    if (keyPressed.key == "Escape" && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  typingLogic() {
    // Only run if keypress changes the value of the search field i.e. exclude things like arrow keys etc
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }

    // Set value for comparison at the start of the function
    this.previousValue = this.searchField.val();
  }

  getResults() {
    this.resultsDiv.html("Imagine real search results here.");
    this.isSpinnerVisible = false;
  }
}

export default Search;
