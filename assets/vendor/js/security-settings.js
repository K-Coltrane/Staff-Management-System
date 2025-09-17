document.addEventListener("DOMContentLoaded", () => {
    // Password strength meter
    const passwordInput = document.getElementById("new_password")
    const strengthBar = document.querySelector("#password-strength .progress-bar")
  
    if (passwordInput && strengthBar) {
      passwordInput.addEventListener("input", function () {
        const password = this.value
        let strength = 0
  
        // Calculate password strength
        if (password.length >= 8) strength += 20
        if (password.match(/[a-z]+/)) strength += 20
        if (password.match(/[A-Z]+/)) strength += 20
        if (password.match(/[0-9]+/)) strength += 20
        if (password.match(/[^a-zA-Z0-9]+/)) strength += 20
  
        // Update progress bar
        strengthBar.style.width = strength + "%"
  
        // Update color based on strength
        if (strength < 40) {
          strengthBar.className = "progress-bar bg-danger"
        } else if (strength < 80) {
          strengthBar.className = "progress-bar bg-warning"
        } else {
          strengthBar.className = "progress-bar bg-success"
        }
      })
    }
  
    // Two-factor authentication toggle
    const twoFactorToggle = document.getElementById("twoFactorEnabled")
    const twoFactorOptions = document.getElementById("twoFactorOptions")
  
    if (twoFactorToggle && twoFactorOptions) {
      twoFactorToggle.addEventListener("change", function () {
        if (this.checked) {
          twoFactorOptions.classList.remove("d-none")
        } else {
          twoFactorOptions.classList.add("d-none")
        }
      })
    }
  
    // 2FA method selection
    const twoFactorMethods = document.querySelectorAll('input[name="twoFactorMethod"]')
    const authenticatorSetup = document.getElementById("authenticatorSetup")
  
    if (twoFactorMethods && authenticatorSetup) {
      twoFactorMethods.forEach((method) => {
        method.addEventListener("change", function () {
          if (this.value === "app") {
            authenticatorSetup.classList.remove("d-none")
          } else {
            authenticatorSetup.classList.add("d-none")
          }
        })
      })
    }
  
    // Backup codes generation
    const generateBackupBtn = document.querySelector('button[class*="btn-outline-primary"]')
    const backupCodes = document.getElementById("backupCodes")
  
    if (generateBackupBtn && backupCodes) {
      generateBackupBtn.addEventListener("click", () => {
        backupCodes.classList.remove("d-none")
      })
    }
  
    // Tab navigation with scrolling
    const tabLinks = document.querySelectorAll(".nav-link")
  
    if (tabLinks) {
      tabLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
          // Get the target tab
          const targetId = this.getAttribute("href")
  
          // If it's the security tab, handle scrolling
          if (targetId === "#security") {
            // Small delay to ensure tab is visible before scrolling
            setTimeout(() => {
              const securityTab = document.querySelector(targetId)
              if (securityTab) {
                securityTab.scrollIntoView({ behavior: "smooth" })
              }
            }, 150)
          }
        })
      })
    }
  
    // Handle URL hash for direct navigation
    if (window.location.hash === "#security") {
      // Find the security tab link and click it
      const securityTabLink = document.querySelector('a[href="#security"]')
      if (securityTabLink) {
        securityTabLink.click()
      }
    }
  })
  