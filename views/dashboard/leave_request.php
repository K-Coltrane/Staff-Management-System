<?php
$pageTitle = 'Request Leave';
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
                                <h5 class="card-title text-primary">Request Leave</h5>
                                <p class="mb-4">Submit a leave request for approval. Please ensure you have sufficient leave balance and provide all required information.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Request Leave" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Balance Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-calendar text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Annual Leave</span>
                                <h3 class="card-title mb-0">25</h3>
                                <small class="text-muted">Days Available</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-plus-circle text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Sick Leave</span>
                                <h3 class="card-title mb-0">10</h3>
                                <small class="text-muted">Days Available</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Personal Leave</span>
                                <h3 class="card-title mb-0">5</h3>
                                <small class="text-muted">Days Available</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-calendar-check text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Used</span>
                                <h3 class="card-title mb-0">15</h3>
                                <small class="text-muted">Days This Year</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Leave Request Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Leave Request Form</h5>
                    </div>
                    <div class="card-body">
                        <form id="leaveRequestForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="leaveType" class="form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="leaveType" name="leaveType" required onchange="updateLeaveTypeInfo()">
                                        <option value="">Select Leave Type</option>
                                        <option value="annual">Annual Leave</option>
                                        <option value="sick">Sick Leave</option>
                                        <option value="personal">Personal Leave</option>
                                        <option value="maternity">Maternity Leave</option>
                                        <option value="paternity">Paternity Leave</option>
                                        <option value="emergency">Emergency Leave</option>
                                        <option value="unpaid">Unpaid Leave</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="leaveReason" class="form-label">Reason for Leave <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="leaveReason" name="leaveReason" placeholder="Brief reason for leave" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="startDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="startDate" name="startDate" required onchange="calculateDays()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="endDate" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="endDate" name="endDate" required onchange="calculateDays()">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="totalDays" class="form-label">Total Days</label>
                                    <input type="number" class="form-control" id="totalDays" name="totalDays" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="halfDay" class="form-label">Half Day Leave</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="halfDay" name="halfDay" onchange="calculateDays()">
                                        <label class="form-check-label" for="halfDay">
                                            Request half day leave
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="leaveDescription" class="form-label">Detailed Description</label>
                                <textarea class="form-control" id="leaveDescription" name="leaveDescription" rows="4" placeholder="Provide additional details about your leave request"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="emergencyContact" class="form-label">Emergency Contact During Leave</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" placeholder="Contact Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="tel" class="form-control" id="emergencyContactPhone" name="emergencyContactPhone" placeholder="Contact Phone">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="handoverNotes" class="form-label">Work Handover Notes</label>
                                <textarea class="form-control" id="handoverNotes" name="handoverNotes" rows="3" placeholder="Notes for work handover during your absence"></textarea>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="acknowledgePolicy" name="acknowledgePolicy" required>
                                    <label class="form-check-label" for="acknowledgePolicy">
                                        I acknowledge that I have read and understood the leave policy and agree to the terms and conditions.
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                                <button type="button" class="btn btn-primary" onclick="submitLeaveRequest()">
                                    <i class="bx bx-send me-1"></i>Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Leave Information & History -->
            <div class="col-md-4">
                <!-- Leave Type Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Leave Type Information</h5>
                    </div>
                    <div class="card-body">
                        <div id="leaveTypeInfo">
                            <p class="text-muted">Select a leave type to view information.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Leave Requests -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Vacation Leave</h6>
                                    <p class="timeline-text">Feb 1-5, 2024 (5 days)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-warning">Pending</span></small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Sick Leave</h6>
                                    <p class="timeline-text">Jan 20-22, 2024 (3 days)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-success">Approved</span></small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Personal Leave</h6>
                                    <p class="timeline-text">Jan 15, 2024 (1 day)</p>
                                    <small class="text-muted">Status: <span class="badge bg-label-danger">Rejected</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Policy -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Leave Policy</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="leavePolicyAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#policy1">
                                        Annual Leave
                                    </button>
                                </h2>
                                <div id="policy1" class="accordion-collapse collapse" data-bs-parent="#leavePolicyAccordion">
                                    <div class="accordion-body">
                                        <ul class="mb-0">
                                            <li>25 days per year</li>
                                            <li>Minimum 2 weeks notice</li>
                                            <li>Maximum 30 days at once</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#policy2">
                                        Sick Leave
                                    </button>
                                </h2>
                                <div id="policy2" class="accordion-collapse collapse" data-bs-parent="#leavePolicyAccordion">
                                    <div class="accordion-body">
                                        <ul class="mb-0">
                                            <li>10 days per year</li>
                                            <li>Medical certificate required for 3+ days</li>
                                            <li>Can be taken immediately</li>
                                        </ul>
                                    </div>
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

<?php include '../../../includes/footer.php'; ?>

<script>
// Leave Request Functions
function updateLeaveTypeInfo() {
    const leaveType = document.getElementById('leaveType').value;
    const infoDiv = document.getElementById('leaveTypeInfo');
    
    const leaveInfo = {
        'annual': {
            title: 'Annual Leave',
            description: 'For vacation and personal time off',
            requirements: ['25 days per year', 'Minimum 2 weeks notice', 'Maximum 30 days at once'],
            balance: '25 days available'
        },
        'sick': {
            title: 'Sick Leave',
            description: 'For illness and medical appointments',
            requirements: ['10 days per year', 'Medical certificate for 3+ days', 'Can be taken immediately'],
            balance: '10 days available'
        },
        'personal': {
            title: 'Personal Leave',
            description: 'For personal matters and emergencies',
            requirements: ['5 days per year', 'Manager approval required', 'Can be taken with short notice'],
            balance: '5 days available'
        },
        'maternity': {
            title: 'Maternity Leave',
            description: 'For new mothers',
            requirements: ['12 weeks paid leave', 'Medical certificate required', 'Advance notice required'],
            balance: '12 weeks available'
        },
        'paternity': {
            title: 'Paternity Leave',
            description: 'For new fathers',
            requirements: ['2 weeks paid leave', 'Birth certificate required', 'Can be taken within 6 months'],
            balance: '2 weeks available'
        },
        'emergency': {
            title: 'Emergency Leave',
            description: 'For urgent personal matters',
            requirements: ['Unpaid leave', 'Manager approval required', 'Can be taken immediately'],
            balance: 'Unlimited (unpaid)'
        },
        'unpaid': {
            title: 'Unpaid Leave',
            description: 'For extended personal time off',
            requirements: ['Manager approval required', 'Maximum 90 days per year', 'Advance notice required'],
            balance: '90 days per year'
        }
    };
    
    if (leaveType && leaveInfo[leaveType]) {
        const info = leaveInfo[leaveType];
        infoDiv.innerHTML = `
            <h6>${info.title}</h6>
            <p class="text-muted">${info.description}</p>
            <h6>Requirements:</h6>
            <ul class="mb-2">
                ${info.requirements.map(req => `<li>${req}</li>`).join('')}
            </ul>
            <div class="alert alert-info">
                <i class="bx bx-info-circle me-2"></i>
                ${info.balance}
            </div>
        `;
    } else {
        infoDiv.innerHTML = '<p class="text-muted">Select a leave type to view information.</p>';
    }
}

function calculateDays() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const halfDay = document.getElementById('halfDay').checked;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            const timeDiff = end.getTime() - start.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            
            let totalDays = daysDiff;
            if (halfDay) {
                totalDays = 0.5;
            }
            
            document.getElementById('totalDays').value = totalDays;
            
            // Check if leave type is selected and validate balance
            const leaveType = document.getElementById('leaveType').value;
            if (leaveType) {
                validateLeaveBalance(leaveType, totalDays);
            }
        } else {
            document.getElementById('totalDays').value = '';
            showToast('End date must be after start date', 'error');
        }
    }
}

function validateLeaveBalance(leaveType, requestedDays) {
    const balances = {
        'annual': 25,
        'sick': 10,
        'personal': 5,
        'maternity': 12,
        'paternity': 2,
        'emergency': 999,
        'unpaid': 90
    };
    
    const availableBalance = balances[leaveType] || 0;
    
    if (requestedDays > availableBalance && leaveType !== 'emergency' && leaveType !== 'unpaid') {
        showToast(`Insufficient leave balance. Available: ${availableBalance} days`, 'error');
    }
}

function submitLeaveRequest() {
    const form = document.getElementById('leaveRequestForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const requiredFields = ['leaveType', 'leaveReason', 'startDate', 'endDate', 'acknowledgePolicy'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!formData.get(field)) {
            isValid = false;
            document.getElementById(field).classList.add('is-invalid');
        } else {
            document.getElementById(field).classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    
    // Validate dates
    const startDate = new Date(formData.get('startDate'));
    const endDate = new Date(formData.get('endDate'));
    
    if (endDate < startDate) {
        showToast('End date must be after start date', 'error');
        return;
    }
    
    // Validate leave balance
    const leaveType = formData.get('leaveType');
    const totalDays = parseFloat(formData.get('totalDays'));
    
    if (leaveType && totalDays) {
        validateLeaveBalance(leaveType, totalDays);
    }
    
    // Simulate form submission
    showToast('Leave request submitted successfully!', 'success');
    
    // Reset form after successful submission
    setTimeout(() => {
        resetForm();
    }, 2000);
}

function resetForm() {
    document.getElementById('leaveRequestForm').reset();
    document.getElementById('totalDays').value = '';
    document.getElementById('leaveTypeInfo').innerHTML = '<p class="text-muted">Select a leave type to view information.</p>';
    
    // Remove validation classes
    const form = document.getElementById('leaveRequestForm');
    const inputs = form.querySelectorAll('.is-invalid');
    inputs.forEach(input => input.classList.remove('is-invalid'));
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
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
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('startDate').min = today;
    document.getElementById('endDate').min = today;
    
    console.log('Leave Request page initialized');
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e7e7e7;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 5px;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}

.accordion-button:focus {
    box-shadow: none;
}
</style>
