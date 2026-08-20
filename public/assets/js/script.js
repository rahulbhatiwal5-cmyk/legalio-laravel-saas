

// ------------------------------------------------

// ---------------------------------------------
// footer_dropdown
// const optionMenu = document.querySelector(".select-menu"),
//   selectBtn = optionMenu.querySelector(".select-btn"),
//   options = optionMenu.querySelectorAll(".option"),
//   sBtn_text = optionMenu.querySelector(".sBtn-text");

const optionMenu = document.querySelector(".select-menu");

if (optionMenu) {
    const selectBtn = optionMenu.querySelector(".select-btn");
    const options = optionMenu.querySelectorAll(".option");
    const sBtn_text = optionMenu.querySelector(".sBtn-text");

    // your custom dropdown logic here...

    // Ensure the menu is closed by default
    optionMenu.classList.remove("active");

    selectBtn.addEventListener("click", () =>
      optionMenu.classList.toggle("active")
    );

    options.forEach((option) => {
      option.addEventListener("click", () => {
        let selectedOption = option.querySelector(".option-text").innerText;
        sBtn_text.innerText = selectedOption;

        optionMenu.classList.remove("active");
      });
    });

    // Close the dropdown when clicking outside
    document.addEventListener("click", (event) => {
      if (!optionMenu.contains(event.target) && event.target !== selectBtn) {
        optionMenu.classList.remove("active");
      }
    });
}






// ===================================
// contact page
let index = 1;

const on = (listener, query, fn) => {
  document.querySelectorAll(query).forEach((item) => {
    item.addEventListener(listener, (el) => {
      fn(el);
    });
  });
};

on("click", ".selectBtn", (item) => {
  const next = item.target.nextElementSibling;
  if (next) {
    next.classList.toggle("toggle");
  }
});

on("click", ".option", (item) => {
  const dropdown = item.target.closest(".select");
  if (dropdown) {
    dropdown.classList.remove("toggle");
    const parent = dropdown.querySelector(".selectBtn");
    parent.setAttribute("data-type", item.target.getAttribute("data-type"));
    parent.innerHTML = item.target.innerHTML; // Copy the inner HTML including the image
  }
});

document.addEventListener("click", (event) => {
  const select = document.querySelectorAll(".select");
  select.forEach((dropdown) => {
    if (!dropdown.contains(event.target)) {
      dropdown.classList.remove("toggle");
    }
  });
});

// ===============

//////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const selectBtn = document.querySelector(".selectBtn");
  const options = document.querySelectorAll(".option");
  const selectDropdown = document.querySelector(".selectDropdown");
  if (!selectBtn || !selectDropdown) return;
  // Function to toggle the dropdown
  function toggleDropdown() {
    const isDisplayed = selectDropdown.style.display === "block";
    selectDropdown.style.display = isDisplayed ? "none" : "block";
  }

  // Event listener for the select button
  selectBtn.addEventListener("click", function () {
    toggleDropdown();
  });

  // Close the dropdown when an option is selected
  options.forEach((option) => {
    option.addEventListener("click", function () {
      const rating = this.innerHTML;
      selectBtn.innerHTML = rating; // Update the select button with the selected option
      selectDropdown.style.display = "none"; // Close the dropdown
    });
  });

  // Optional: Close the dropdown if clicked outside
  document.addEventListener("click", function (event) {
    if (
      !selectBtn.contains(event.target) &&
      !selectDropdown.contains(event.target)
    ) {
      selectDropdown.style.display = "none";
    }
  });
});



// /////////////////////////30sep/////////////////////////////////


$('.tc-item a').click(function(event){
  event.preventDefault();
  var target = $($(this).attr('href'));
  var offset = 200; // Adjust this value based on your header's height
  $('html, body').animate({
      scrollTop: target.offset().top - offset
  }, 500); // 500 ms for smooth scroll
});



// $(document).ready(function() {
//   function checkScroll() {
//       const $myElement = $('#myID');
//       if($(window).scrollTop() > 460) {
//           $myElement.show();
//       }else {
//           $myElement.hide();
//       }
//   }
//   checkScroll();
//   $(window).on('scroll', checkScroll);
// });




$(document).ready(function() {
  const $myElement = $('#myID');
  const $searchInput = $('.searchTerm');

  function checkScroll() {
    const scrollTop = $(window).scrollTop();

    if (scrollTop > 460) {
      $myElement.show();
    } else {
      $myElement.hide();
    }
  }

  checkScroll();
  $(window).on('scroll', checkScroll);
  $searchInput.on('input focus blur', function() {
    checkScroll();
  });
});


$(document).ready(function () {
  // Function to handle menu item clicks
  function handleMenuClick() {
    // Bind the click event to menu items
    $('.menu-item > a').off('click').on('click', function (e) {
      // e.preventDefault(); // Prevent default link behavior

      const $dropdownMenu = $(this).siblings('.dropdown_menu');
      const $menuItem = $(this).parent('.menu-item');

      // Toggle the clicked item
      $dropdownMenu.stop(true, true).slideToggle(0);
      $menuItem.toggleClass('active');
      $(this).toggleClass('clicked');

      // Close other dropdowns
      $('.menu-item')
        .not($menuItem)
        .removeClass('active')
        .find('.dropdown_menu')
        .slideUp(0);
      $('.menu-item > a')
        .not($(this))
        .removeClass('clicked');
    });
  }

  handleMenuClick(); // Apply menu functionality

  // Close dropdowns when clicking outside of the menu
  $(document).on('click', function (e) {
    // Check if the click was outside the menu
    if (!$(e.target).closest('.menu-item').length) {
      // Close all dropdowns and remove active/clicked classes
      $('.menu-item').removeClass('active').find('.dropdown_menu').slideUp(0);
      $('.menu-item > a').removeClass('clicked');
    }
  });

  // Prevent closing the dropdown if clicking inside the menu
  $('.menu-item').on('click', function (e) {
    e.stopPropagation(); // Prevent document click from triggering
  });

  // Reapply menu functionality on window resize (in case DOM changes)
  $(window).resize(function () {
    handleMenuClick();
  });
});

// Checkout form js //
jQuery(document).ready(function () {
  $('.check_out .row .pymnt_form .pymnt > .opt').on('click', function () {
    $(this).children('.check_out .row .pymnt_form .pymnt > .opt > .form-check').addClass('active')
     .parent().siblings().find('.form-check.active').removeClass('active');
  });
});

// after login dropdown in home //

// $(document).ready(function () {
//   $(".drop_menu").click(function (event) {
//       event.stopPropagation(); // Prevents click event from propagating to the document

//       // Hide other dropdowns before showing the clicked one
//       $(".drop_menu .dropdown-menu").not($(this).find(".dropdown-menu")).removeClass("show");

//       // Toggle the dropdown for the clicked menu
//       $(this).find(".dropdown-menu").toggleClass("show");
//   });

//   // Hide dropdown if clicking outside of any drop_menu
//   $(document).click(function () {
//       $(".drop_menu .dropdown-menu").removeClass("show");
//   });
// });





$(document).ready(function () {

    const slider = $('.left_menu');

    let isDown = false;
    let isDragging = false;
    let startX;
    let scrollLeft;

    slider.on('mousedown', function (e) {
        isDown = true;
        isDragging = false;
        startX = e.pageX - slider.offset().left;
        scrollLeft = slider.scrollLeft();
    });

    slider.on('mouseleave mouseup', function () {
        isDown = false;

        // small timeout to allow click block
        setTimeout(() => {
            isDragging = false;
        }, 50);
    });

    slider.on('mousemove', function (e) {
        if (!isDown) return;

        const x = e.pageX - slider.offset().left;
        const walk = (x - startX) * 2;

        if (Math.abs(walk) > 5) {
            isDragging = true;
        }

        e.preventDefault();
        slider.scrollLeft(scrollLeft - walk);
    });

    // 🚀 Prevent click when dragging
    slider.find('a').on('click', function (e) {
        if (isDragging) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

});




// ====================checkoutpage  radio js=====================  
document.querySelectorAll('.opn_wth_js_us').forEach(card => {

    card.addEventListener('click', function(e) {

        // Prevent double triggering when clicking directly on radio
        if (e.target.classList.contains('pay_type')) {
            return;
        }

        const radio = this.querySelector('.pay_type');

        if (radio) {
            radio.checked = true;

            // Trigger change event if needed
            radio.dispatchEvent(new Event('change'));
        }
    });

})