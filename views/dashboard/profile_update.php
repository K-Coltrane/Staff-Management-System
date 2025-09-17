<?php
$pageTitle = 'Update Profile';
$pageCss = ['assets/vendor/css/pages/page-auth.css'];
include '../../../includes/header.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Update Personal Profile</h5>
                                <p class="mb-4">Keep your personal information up to date. This information is used for HR records and emergency contacts.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Update Profile" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="personalInfoForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="John" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="Smith" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="john.smith@company.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="+1 (555) 123-4567" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your full address">123 Main Street, City, State 12345</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="1985-06-15">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="male" selected>Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                        <option value="prefer-not-to-say">Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="maritalStatus" class="form-label">Marital Status</label>
                                    <select class="form-select" id="maritalStatus" name="maritalStatus">
                                        <option value="single">Single</option>
                                        <option value="married" selected>Married</option>
                                        <option value="divorced">Divorced</option>
                                        <option value="widowed">Widowed</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="American">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="updatePersonalInfo()">
                                    <i class="bx bx-save me-1"></i>Update Personal Info
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Emergency Contact</h5>
                    </div>
                    <div class="card-body">
                        <form id="emergencyContactForm">
                            <div class="mb-3">
                                <label for="emergencyName" class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emergencyName" name="emergencyName" value="Jane Smith" required>
                            </div>
                            <div class="mb-3">
                                <label for="emergencyRelationship" class="form-label">Relationship</label>
                                <select class="form-select" id="emergencyRelationship" name="emergencyRelationship">
                                    <option value="spouse" selected>Spouse</option>
                                    <option value="parent">Parent</option>
                                    <option value="sibling">Sibling</option>
                                    <option value="child">Child</option>
                                    <option value="friend">Friend</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="emergencyPhone" class="form-label">Emergency Contact Phone</label>
                                <input type="tel" class="form-control" id="emergencyPhone" name="emergencyPhone" value="+1 (555) 987-6543" required>
                            </div>
                            <div class="mb-3">
                                <label for="emergencyEmail" class="form-label">Emergency Contact Email</label>
                                <input type="email" class="form-control" id="emergencyEmail" name="emergencyEmail" value="jane.smith@email.com">
                            </div>
                            <div class="mb-3">
                                <label for="emergencyAddress" class="form-label">Emergency Contact Address</label>
                                <textarea class="form-control" id="emergencyAddress" name="emergencyAddress" rows="3" placeholder="Enter emergency contact address">123 Main Street, City, State 12345</textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="updateEmergencyContact()">
                                    <i class="bx bx-save me-1"></i>Update Emergency Contact
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Professional Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="professionalInfoForm">
                            <div class="mb-3">
                                <label for="employeeId" class="form-label">Employee ID</label>
                                <input type="text" class="form-control" id="employeeId" name="employeeId" value="EMP001" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value="IT" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name="position" value="Senior Developer" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="manager" class="form-label">Manager</label>
                                <input type="text" class="form-control" id="manager" name="manager" value="Sarah Johnson" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="hireDate" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="hireDate" name="hireDate" value="2020-01-15" readonly>
                            </div>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Professional information can only be updated by HR or your manager.
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Skills & Certifications -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Skills & Certifications</h5>
                        <button class="btn btn-sm btn-primary" onclick="addSkill()">
                            <i class="bx bx-plus me-1"></i>Add Skill
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="skills" class="form-label">Technical Skills</label>
                            <div id="skillsContainer">
                                <div class="skill-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="PHP" readonly>
                                        <button class="btn btn-outline-danger" type="button" onclick="removeSkill(this)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="skill-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="JavaScript" readonly>
                                        <button class="btn btn-outline-danger" type="button" onclick="removeSkill(this)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="skill-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="MySQL" readonly>
                                        <button class="btn btn-outline-danger" type="button" onclick="removeSkill(this)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="certifications" class="form-label">Certifications</label>
                            <div id="certificationsContainer">
                                <div class="certification-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="AWS Certified Developer" readonly>
                                        <input type="date" class="form-control" value="2023-12-15" readonly>
                                        <button class="btn btn-outline-danger" type="button" onclick="removeCertification(this)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Skills and certifications can be added by HR or through the training system.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Picture -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Picture</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img src="<?php echo BASE_URL; ?>assets/img/avatars/1.png" alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                                    <div>
                                        <button class="btn btn-primary btn-sm" onclick="changeProfilePicture()">
                                            <i class="bx bx-camera me-1"></i>Change Picture
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Profile Picture Guidelines</h6>
                                    <ul class="mb-0">
                                        <li>Use a professional headshot</li>
                                        <li>File size should be less than 2MB</li>
                                        <li>Supported formats: JPG, PNG</li>
                                        <li>Recommended size: 300x300 pixels</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSkillForm">
                    <div class="mb-3">
                        <label for="newSkill" class="form-label">Skill Name</label>
                        <input type="text" class="form-control" id="newSkill" name="newSkill" required>
                    </div>
                    <div class="mb-3">
                        <label for="skillLevel" class="form-label">Skill Level</label>
                        <select class="form-select" id="skillLevel" name="skillLevel">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewSkill()">Add Skill</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
// Profile Update Functions
function updatePersonalInfo() {
    const form = document.getElementById('personalInfoForm');
    const formData = new FormData(form);
    
    // Simulate API call
    showToast('Personal information updated successfully!', 'success');
}

function updateEmergencyContact() {
    const form = document.getElementById('emergencyContactForm');
    const formData = new FormData(form);
    
    // Simulate API call
    showToast('Emergency contact updated successfully!', 'success');
}

function addSkill() {
    const addModal = new bootstrap.Modal(document.getElementById('addSkillModal'));
    addModal.show();
}

function saveNewSkill() {
    const skillName = document.getElementById('newSkill').value;
    const skillLevel = document.getElementById('skillLevel').value;
    
    if (skillName) {
        const skillsContainer = document.getElementById('skillsContainer');
        const skillItem = document.createElement('div');
        skillItem.className = 'skill-item mb-2';
        skillItem.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" value="${skillName} (${skillLevel})" readonly>
                <button class="btn btn-outline-danger" type="button" onclick="removeSkill(this)">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        skillsContainer.appendChild(skillItem);
        
        // Close modal and reset form
        bootstrap.Modal.getInstance(document.getElementById('addSkillModal')).hide();
        document.getElementById('addSkillForm').reset();
        
        showToast('Skill added successfully!', 'success');
    }
}

function removeSkill(button) {
    if (confirm('Are you sure you want to remove this skill?')) {
        button.closest('.skill-item').remove();
        showToast('Skill removed successfully!', 'success');
    }
}

function removeCertification(button) {
    if (confirm('Are you sure you want to remove this certification?')) {
        button.closest('.certification-item').remove();
        showToast('Certification removed successfully!', 'success');
    }
}

function changeProfilePicture() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                showToast('File size must be less than 2MB', 'error');
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showToast('Please select a valid image file', 'error');
                return;
            }
            
            // Simulate upload
            showToast('Profile picture updated successfully!', 'success');
        }
    };
    input.click();
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to toast container
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Initialize page
$(document).ready(function() {
    console.log('Profile Update page initialized');
});
</script>

<style>
.skill-item, .certification-item {
    transition: all 0.3s ease;
}

.skill-item:hover, .certification-item:hover {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    padding: 0.5rem;
}

.form-control:read-only {
    background-color: #f8f9fa;
    opacity: 1;
}
</style>
