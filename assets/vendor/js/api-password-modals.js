document.addEventListener("DOMContentLoaded", () => {
    // App Password Modal
    const createAppPasswordBtn = document.getElementById("createAppPasswordBtn")
    const generatedPasswordContainer = document.getElementById("generatedPasswordContainer")
    const generatedPassword = document.getElementById("generatedPassword")
    const copyPasswordBtn = document.getElementById("copyPassword")
    const appPasswordForm = document.getElementById("appPasswordForm")
  
    if (createAppPasswordBtn && generatedPasswordContainer && generatedPassword) {
      createAppPasswordBtn.addEventListener("click", function () {
        // Validate form
        if (appPasswordForm.checkValidity()) {
          // Generate a random password (in a real app, this would be done server-side)
          const randomPassword = generateRandomPassword()
  
          // Display the password
          generatedPassword.textContent = randomPassword
          generatedPasswordContainer.classList.remove("d-none")
  
          // Hide the create button
          this.classList.add("d-none")
        } else {
          appPasswordForm.reportValidity()
        }
      })
    }
  
    if (copyPasswordBtn && generatedPassword) {
      copyPasswordBtn.addEventListener("click", () => {
        // Copy the password to clipboard
        navigator.clipboard.writeText(generatedPassword.textContent).then(() => {
          // Show a tooltip or some indication that it was copied
          copyPasswordBtn.innerHTML = '<i class="bx bx-check me-1"></i> Copied!'
          setTimeout(() => {
            copyPasswordBtn.innerHTML = '<i class="bx bx-copy me-1"></i> Copy Password'
          }, 2000)
        })
      })
    }
  
    // API Key Modal
    const generateApiKeyBtn = document.getElementById("generateApiKeyBtn")
    const generatedApiKeyContainer = document.getElementById("generatedApiKeyContainer")
    const generatedApiKey = document.getElementById("generatedApiKey")
    const copyApiKeyBtn = document.getElementById("copyApiKey")
    const apiKeyForm = document.getElementById("apiKeyForm")
  
    if (generateApiKeyBtn && generatedApiKeyContainer && generatedApiKey) {
      generateApiKeyBtn.addEventListener("click", function () {
        // Validate form
        if (apiKeyForm.checkValidity()) {
          // Generate a random API key (in a real app, this would be done server-side)
          const randomApiKey = "sk_live_" + generateRandomString(32)
  
          // Display the API key
          generatedApiKey.textContent = randomApiKey
          generatedApiKeyContainer.classList.remove("d-none")
  
          // Hide the generate button
          this.classList.add("d-none")
        } else {
          apiKeyForm.reportValidity()
        }
      })
    }
  
    if (copyApiKeyBtn && generatedApiKey) {
      copyApiKeyBtn.addEventListener("click", () => {
        // Copy the API key to clipboard
        navigator.clipboard.writeText(generatedApiKey.textContent).then(() => {
          // Show a tooltip or some indication that it was copied
          copyApiKeyBtn.innerHTML = '<i class="bx bx-check me-1"></i> Copied!'
          setTimeout(() => {
            copyApiKeyBtn.innerHTML = '<i class="bx bx-copy me-1"></i> Copy API Key'
          }, 2000)
        })
      })
    }
  
    // Helper functions
    function generateRandomPassword() {
      // Generate a random password in the format xxxx-xxxx-xxxx-xxxx
      const segments = []
      for (let i = 0; i < 4; i++) {
        segments.push(generateRandomString(4))
      }
      return segments.join("-")
    }
  
    function generateRandomString(length) {
      const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"
      let result = ""
      for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length))
      }
      return result
    }
  })
  