<!-- Create API Key Modal -->
<div class="modal fade" id="createApiKeyModal" tabindex="-1" aria-labelledby="createApiKeyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createApiKeyModalLabel">Generate API Key</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="apiKeyForm">
          <div class="mb-3">
            <label for="apiKeyName" class="form-label">Key Name</label>
            <input type="text" class="form-control" id="apiKeyName" placeholder="e.g., Integration Key, External App" required>
          </div>
          <div class="mb-3">
            <label for="apiKeyExpiration" class="form-label">Expiration</label>
            <select class="form-select" id="apiKeyExpiration">
              <option value="never">Never</option>
              <option value="30days">30 Days</option>
              <option value="60days">60 Days</option>
              <option value="90days">90 Days</option>
              <option value="1year">1 Year</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="permissionRead" checked>
              <label class="form-check-label" for="permissionRead">Read</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="permissionWrite">
              <label class="form-check-label" for="permissionWrite">Write</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="permissionDelete">
              <label class="form-check-label" for="permissionDelete">Delete</label>
            </div>
          </div>
        </form>
        <div id="generatedApiKeyContainer" class="d-none">
          <div class="alert alert-warning">
            <h6 class="alert-heading fw-bold mb-1">Your API Key</h6>
            <p class="mb-2">This key will only be shown once. Make sure to copy it now.</p>
            <div class="bg-light p-2 rounded mb-2">
              <code id="generatedApiKey">sk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyApiKey">
              <i class="bx bx-copy me-1"></i> Copy API Key
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="generateApiKeyBtn">Generate</button>
      </div>
    </div>
  </div>
</div>
