<!-- Create App Password Modal -->
<div class="modal fade" id="createAppPasswordModal" tabindex="-1" aria-labelledby="createAppPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createAppPasswordModalLabel">Create App Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="appPasswordForm">
          <div class="mb-3">
            <label for="appPasswordName" class="form-label">App Name</label>
            <input type="text" class="form-control" id="appPasswordName" placeholder="e.g., Email Client, Mobile App" required>
            <small class="form-text text-muted">Give this app password a name to help you identify it later.</small>
          </div>
          <div class="mb-3">
            <label for="appPasswordPermission" class="form-label">Permission Level</label>
            <select class="form-select" id="appPasswordPermission">
              <option value="full">Full Access</option>
              <option value="readonly">Read Only</option>
              <option value="limited">Limited Access</option>
            </select>
          </div>
        </form>
        <div id="generatedPasswordContainer" class="d-none">
          <div class="alert alert-success">
            <h6 class="alert-heading fw-bold mb-1">Your App Password</h6>
            <p class="mb-2">Use this password to sign in to your app. It will only be shown once.</p>
            <div class="bg-light p-2 rounded mb-2">
              <code id="generatedPassword">xxxx-xxxx-xxxx-xxxx</code>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyPassword">
              <i class="bx bx-copy me-1"></i> Copy Password
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="createAppPasswordBtn">Create</button>
      </div>
    </div>
  </div>
</div>
