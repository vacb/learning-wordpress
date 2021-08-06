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
    $.getJSON(
      // Custom route called 'search' and 'term' specified in search-route.php
      universityData.root_url +
        "/wp-json/university/v1/search?term=" +
        this.searchField.val(),

      (results) => {
        this.resultsDiv.html(`
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${
              results.generalInfo.length
                ? '<ul class="link-list min-list">'
                : "<p>Your search returned no results in this category.</p>"
            }
            ${results.generalInfo
              .map(
                (item) =>
                  `<li><a href="${item.permalink}">${item.title}</a> ${
                    item.postType == "post" ? `by ${item.authorName}` : ""
                  }</li>`
              )
              .join("")}
            ${results.generalInfo.length ? "</ul>" : ""}
          </div>
          <div class="one-third">
  
            <h2 class="search-overlay__section-title">Programs</h2>
              ${
                results.programs.length
                  ? '<ul class="link-list min-list">'
                  : `<p>Your search returned no results. <a href="${universityData.root_url}/programs">View all programs</a></p>`
              }
              ${results.programs
                .map(
                  (item) =>
                    `<li><a href="${item.permalink}">${item.title}</a></li>`
                )
                .join("")}
              ${results.programs.length ? "</ul>" : ""}
            
            <h2 class="search-overlay__section-title">Academics</h2>

            ${
              results.academics.length
                ? '<ul class="academic-cards">'
                : `<p>Your search returned no results.</p>`
            }
            ${results.academics
              .map(
                (item) => `
              <li class="academic-card__list-item">
                <a class="academic-card" href="${item.permalink}">
                    <img class="academic-card__image" src="${item.img}">
                    <span class="academic-card__name">
                        ${item.title}
                    </span>
                </a>
              </li>
            `
              )
              .join("")}
            ${results.academics.length ? "</ul>" : ""}

          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Campuses</h2>

            ${
              results.campuses.length
                ? '<ul class="link-list min-list">'
                : `<p>Your search returned no results. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`
            }
            ${results.campuses
              .map(
                (item) =>
                  `<li><a href="${item.permalink}">${item.title}</a></li>`
              )
              .join("")}
            ${results.campuses.length ? "</ul>" : ""}

            <h2 class="search-overlay__section-title">Events</h2>

            ${
              results.events.length
                ? ""
                : `<p>Your search returned no results. <a href="${universityData.root_url}/events">View all events</a></p>`
            }
            ${results.events
              .map(
                (item) =>
                  `
                  <div class="event-summary">
                  <a class="event-summary__date t-center" href="${item.permalink}">
                      <span class="event-summary__month">${item.month}</span>
                      <span class="event-summary__day">${item.day}</span>
                  </a>
                  <div class="event-summary__content">
                      <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                      <p>${item.description}
                          <a href="${item.permalink}" class="nu gray">Learn more</a>
                      </p>
                  </div>
               </div>
                  `
              )
              .join("")}

          </div>
        </div>
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
