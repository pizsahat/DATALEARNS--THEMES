import $ from "jquery";

class Search {
  // Describe / Declare Object
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = $("#search-overlay__results");
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.events();
    this.isOverlayOpen = false;
    this.isLoading = false;
    this.previousValue;
    this.typingTimer;
  }
  // events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    // $(document).on("keydown", this.keyPressDispatcher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  // methods
  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.val()) {
        if (!this.isLoading) {
          this.resultsDiv.html(
            '<div class="spinner-loader-wrapper"><div class="spinner-loader"></div></div>'
          );
          this.isLoading = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);
      } else {
        this.resultsDiv.html("");
        this.isLoading = false;
      }
    }
    this.previousValue = this.searchField.val();
  }
  getResults() {
    $.getJSON(
      datalearnsData.root_url +
        "/wp-json/wp/v2/search?q=" +
        this.searchField.val(),
      (results) => {
        this.resultsDiv.html(`
          <div class="container">
            <h2 class=" search-overlay__section-title" style="margin: 0 0 15px 0;">Contents</h2>
            ${
              results.contents.length
                ? '<ul class="link-list min-list article-shortcode-plugin">'
                : "<p>No Data</p>"
            }
            ${results.contents
              .map(
                (data) => `
                <div class="article-card-plugin layout-2">
                  <div class="article-thumbnail-plugin layout-2">
                      <a href="${data.permalink}">
                          <img src="${data.thumbnail}" alt="Thumbnail">
                      </a>
                  </div>
                  <div class="article-details-plugin layout-2">
                      <div>
                          <h6 class="article-title-plugin layout-2">
                              <a href="${data.permalink}">${data.title}</a>
                          </h6>
                          <span class="article-time-plugin layout-2">${
                            data.time_ago
                          }</span>
                      </div>
                      <div class="article-meta-plugin layout-2">
                          <img class="author-thumbnail layout-2" src="${
                            data.author_avatar
                          }" alt="${
                            data.author
                          }" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 5px;">
                          <span class="article-author-plugin layout-2">${
                            data.author
                          }</span>
  ${
    data.category
      ? `<span class="article-category-plugin layout-2"> - ${data.category}</span>`
      : ""
  }
                      </div>
                  </div>
                </div>
              `
              )
              .join("")}
          </div>
          <div class="container">
            <h2 class=" search-overlay__section-title">Courses</h2>
            ${
              results.courses.length
                ? '<div class="archive-course-container">'
                : "<p>No Data</p>"
            }
            ${results.courses
              .map(
                (data) => `
                <div class="article-card-plugin layout-1 ">
                    <div class="article-thumbnail-plugin layout-1">
                        <a href="${data.permalink}">
                            <img src="${data.thumbnail}" alt="Thumbnail">
                        </a>
                    </div>
                    <div class="article-details-plugin layout-1">
                        <div>
                            <span class="article-group-plugin layout-1">${data.course_type}</span>
                            <h6 class="article-title-plugin layout-1">
                                <a href="${data.permalink}">${data.title}</a>
                            </h6>
                        </div>
                       <span class="article-time-plugin layout-1">${data.skill_level}</span>
                    </div>
                </div>
              `
              )
              .join("")}
          </div>
        `);
        this.isLoading = false;
      }
    );
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.searchField.val("");
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
  }
  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }
  // keyPressDispatcher(e) {
  //   const activeElement = document.activeElement;
  //   const isInputFocused =
  //     activeElement.tagName === "INPUT" || activeElement.tagName === "TEXTAREA";
  //   if (e.keyCode == 83 && !this.isOverlayOpen && !isInputFocused) {
  //     this.openOverlay();
  //   }
  //   if (e.keyCode == 27 && this.isOverlayOpen && !isInputFocused) {
  //     this.closeOverlay();
  //   }
  // }

  addSearchHTML() {
    $("body").append(`
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="what are you looking for?" id="search-term">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
        <div class="container">
          <div id="search-overlay__results"></div>
        </div>
      </div>
      `);
  }
}

export default Search;
