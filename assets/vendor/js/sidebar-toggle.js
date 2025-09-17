document.addEventListener("DOMContentLoaded", () => {
    // Get the toggle button and the sidebar
    const menuToggler = document.querySelector(".layout-menu-toggle")
    const layoutMenu = document.querySelector("#layout-menu")
    const layoutWrapper = document.querySelector(".layout-wrapper")
  
    // Add event listener to menu toggler
    if (menuToggler) {
      menuToggler.addEventListener("click", () => {
        if (layoutMenu && layoutWrapper) {
          // Toggle the 'layout-menu-collapsed' class on the wrapper
          layoutWrapper.classList.toggle("layout-menu-collapsed")
  
          // Store the state in local storage
          const isCollapsed = layoutWrapper.classList.contains("layout-menu-collapsed")
          localStorage.setItem("sidebarCollapsed", isCollapsed.toString())
        }
      })
    }
  
    // Check if there's a stored state and apply it
    const storedState = localStorage.getItem("sidebarCollapsed")
    if (storedState === "true" && layoutWrapper) {
      layoutWrapper.classList.add("layout-menu-collapsed")
    }
  
    // Add event listener for mobile toggle
    const mobileToggler = document.querySelector(".layout-menu-toggle.navbar-nav")
    if (mobileToggler) {
      mobileToggler.addEventListener("click", () => {
        if (layoutMenu) {
          layoutMenu.classList.toggle("show-menu")
        }
      })
    }
  })
  