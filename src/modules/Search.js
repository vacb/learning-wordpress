import $ from "jquery";

class Search {
  // Describe and create/initiate object
  constructor() {
    // Must be first, otherwise elements we're using won't exist
    this.addSearchHTML();
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
    // Empty out search field if you open the overlay a second time
    this.searchField.val("");
    setTimeout(() => this.searchField.trigger("focus"), 301);
    this.isOverlayOpen = true;
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
        this.typingTimer = setTimeout(this.getResults.bind(this), 500);
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }

    // Set value for comparison at the start of the function
    this.previousValue = this.searchField.val();
  }

  getResults() {
    // this.resultsDiv.html("Imagine real search results here.");

    $.getJSON(
      universityData.root_url +
        "/wp-json/wp/v2/posts?search=" +
        this.searchField.val(),
      (posts) => {
        this.resultsDiv.html(`
            <h2 class="search-overlay__section-title">Search Results</h2>
            ${
              posts.length
                ? '<ul class="link-list min-list">'
                : "<p>Your search returned no results.</p>"
            }
                ${posts
                  .map(
                    (item) =>
                      `<li><a href="${item.link}">${item.title.rendered}</a></li>`
                  )
                  .join("")}
            ${posts.length ? "</ul>" : ""}
          `);
        this.isSpinnerVisible = false;
      }
    );
  }

  addSearchHTML() {
    $("body").append(` 
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
      <div class="container">
        <div class="search-overlay__results"></div>
      </div>
    </div>
    `);
  }
}

export default Search;
