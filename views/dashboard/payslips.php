<?php
$pageTitle = 'View Payslips';
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
                                <h5 class="card-title text-primary">Payslips</h5>
                                <p class="mb-4">View and download your payslips. All payslips are available in PDF format for your records.</p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-0">
                                <img src="<?php echo BASE_URL; ?>assets/img/illustrations/man-with-laptop-light.png" height="140" alt="Payslips" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <i class="bx bx-dollar text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Current Salary</span>
                                <h3 class="card-title mb-0">$8,500</h3>
                                <small class="text-muted">Monthly</small>
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
                                <i class="bx bx-calendar text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Pay Period</span>
                                <h3 class="card-title mb-0">Monthly</h3>
                                <small class="text-muted">1st - 31st</small>
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
                                <i class="bx bx-file text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Payslips</span>
                                <h3 class="card-title mb-0">12</h3>
                                <small class="text-muted">This Year</small>
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
                                <i class="bx bx-download text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Last Download</span>
                                <h3 class="card-title mb-0">Jan 2024</h3>
                                <small class="text-muted">2 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filter Payslips</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="yearFilter" class="form-label">Year</label>
                                <select class="form-select" id="yearFilter" onchange="filterPayslips()">
                                    <option value="all">All Years</option>
                                    <option value="2024" selected>2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monthFilter" class="form-label">Month</label>
                                <select class="form-select" id="monthFilter" onchange="filterPayslips()">
                                    <option value="all">All Months</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" onchange="filterPayslips()">
                                    <option value="all">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-primary" onclick="downloadAllPayslips()">
                                        <i class="bx bx-download me-1"></i>Download All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslips Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payslips</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshPayslips()">
                                <i class="bx bx-refresh me-1"></i>Refresh
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="requestPayslip()">
                                <i class="bx bx-plus me-1"></i>Request Payslip
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="payslipsTable">
                                <thead>
                                    <tr>
                                        <th>Pay Period</th>
                                        <th>Gross Pay</th>
                                        <th>Net Pay</th>
                                        <th>Status</th>
                                        <th>Generated Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">January 2024</span>
                                                <small class="text-muted">Jan 1 - Jan 31, 2024</small>
                                            </div>
                                        </td>
                                        <td>$8,500.00</td>
                                        <td>$6,800.00</td>
                                        <td><span class="badge bg-label-success">Available</span></td>
                                        <td>Feb 1, 2024</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewPayslip('2024-01')"><i class="bx bx-show me-2"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="downloadPayslip('2024-01')"><i class="bx bx-download me-2"></i>Download PDF</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="emailPayslip('2024-01')"><i class="bx bx-envelope me-2"></i>Email</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">December 2023</span>
                                                <small class="text-muted">Dec 1 - Dec 31, 2023</small>
                                            </div>
                                        </td>
                                        <td>$8,500.00</td>
                                        <td>$6,800.00</td>
                                        <td><span class="badge bg-label-success">Available</span></td>
                                        <td>Jan 1, 2024</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewPayslip('2023-12')"><i class="bx bx-show me-2"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="downloadPayslip('2023-12')"><i class="bx bx-download me-2"></i>Download PDF</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="emailPayslip('2023-12')"><i class="bx bx-envelope me-2"></i>Email</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">November 2023</span>
                                                <small class="text-muted">Nov 1 - Nov 30, 2023</small>
                                            </div>
                                        </td>
                                        <td>$8,500.00</td>
                                        <td>$6,800.00</td>
                                        <td><span class="badge bg-label-success">Available</span></td>
                                        <td>Dec 1, 2023</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewPayslip('2023-11')"><i class="bx bx-show me-2"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="downloadPayslip('2023-11')"><i class="bx bx-download me-2"></i>Download PDF</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="emailPayslip('2023-11')"><i class="bx bx-envelope me-2"></i>Email</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">October 2023</span>
                                                <small class="text-muted">Oct 1 - Oct 31, 2023</small>
                                            </div>
                                        </td>
                                        <td>$8,500.00</td>
                                        <td>$6,800.00</td>
                                        <td><span class="badge bg-label-warning">Processing</span></td>
                                        <td>Nov 1, 2023</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" disabled>
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewPayslip('2023-10')"><i class="bx bx-show me-2"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="downloadPayslip('2023-10')"><i class="bx bx-download me-2"></i>Download PDF</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="emailPayslip('2023-10')"><i class="bx bx-envelope me-2"></i>Email</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip Details Modal -->
        <div class="modal fade" id="payslipModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payslip Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="payslipContent">
                            <!-- Payslip content will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="downloadCurrentPayslip()">
                            <i class="bx bx-download me-1"></i>Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Payslip Modal -->
        <div class="modal fade" id="requestPayslipModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request Payslip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="requestPayslipForm">
                            <div class="mb-3">
                                <label for="requestYear" class="form-label">Year</label>
                                <select class="form-select" id="requestYear" name="requestYear" required>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="requestMonth" class="form-label">Month</label>
                                <select class="form-select" id="requestMonth" name="requestMonth" required>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="requestReason" class="form-label">Reason for Request</label>
                                <textarea class="form-control" id="requestReason" name="requestReason" rows="3" placeholder="Please specify the reason for requesting this payslip"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitPayslipRequest()">Submit Request</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
let currentPayslipId = null;

// Payslip Functions
function filterPayslips() {
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    // Simulate filtering
    showToast('Payslips filtered successfully!', 'success');
}

function refreshPayslips() {
    showToast('Payslips refreshed successfully!', 'success');
}

function downloadAllPayslips() {
    if (confirm('Are you sure you want to download all available payslips?')) {
        showToast('Downloading all payslips...', 'info');
        // Simulate download
        setTimeout(() => {
            showToast('All payslips downloaded successfully!', 'success');
        }, 2000);
    }
}

function viewPayslip(payslipId) {
    currentPayslipId = payslipId;
    const modal = new bootstrap.Modal(document.getElementById('payslipModal'));
    
    // Load payslip content
    document.getElementById('payslipContent').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Employee Information</h6>
                <p><strong>Name:</strong> John Smith</p>
                <p><strong>Employee ID:</strong> EMP001</p>
                <p><strong>Department:</strong> IT</p>
                <p><strong>Position:</strong> Senior Developer</p>
            </div>
            <div class="col-md-6">
                <h6>Pay Period</h6>
                <p><strong>Period:</strong> ${payslipId}</p>
                <p><strong>Pay Date:</strong> ${getPayDate(payslipId)}</p>
                <p><strong>Pay Method:</strong> Direct Deposit</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <h6>Earnings</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Basic Salary</td>
                        <td class="text-end">$8,500.00</td>
                    </tr>
                    <tr>
                        <td>Overtime</td>
                        <td class="text-end">$0.00</td>
                    </tr>
                    <tr>
                        <td>Bonus</td>
                        <td class="text-end">$0.00</td>
                    </tr>
                    <tr class="table-active">
                        <td><strong>Total Gross Pay</strong></td>
                        <td class="text-end"><strong>$8,500.00</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Deductions</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Federal Tax</td>
                        <td class="text-end">$1,275.00</td>
                    </tr>
                    <tr>
                        <td>State Tax</td>
                        <td class="text-end">$425.00</td>
                    </tr>
                    <tr>
                        <td>Social Security</td>
                        <td class="text-end">$527.00</td>
                    </tr>
                    <tr>
                        <td>Medicare</td>
                        <td class="text-end">$123.25</td>
                    </tr>
                    <tr>
                        <td>Health Insurance</td>
                        <td class="text-end">$350.75</td>
                    </tr>
                    <tr class="table-active">
                        <td><strong>Total Deductions</strong></td>
                        <td class="text-end"><strong>$2,700.00</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h5>Net Pay: $5,800.00</h5>
                </div>
            </div>
        </div>
    `;
    
    modal.show();
}

function downloadPayslip(payslipId) {
    showToast(`Downloading payslip for ${payslipId}...`, 'info');
    // Simulate download
    setTimeout(() => {
        showToast('Payslip downloaded successfully!', 'success');
    }, 1500);
}

function downloadCurrentPayslip() {
    if (currentPayslipId) {
        downloadPayslip(currentPayslipId);
    }
}

function emailPayslip(payslipId) {
    if (confirm('Are you sure you want to email this payslip to your registered email address?')) {
        showToast(`Emailing payslip for ${payslipId}...`, 'info');
        // Simulate email
        setTimeout(() => {
            showToast('Payslip emailed successfully!', 'success');
        }, 1500);
    }
}

function requestPayslip() {
    const modal = new bootstrap.Modal(document.getElementById('requestPayslipModal'));
    modal.show();
}

function submitPayslipRequest() {
    const form = document.getElementById('requestPayslipForm');
    const formData = new FormData(form);
    
    const year = formData.get('requestYear');
    const month = formData.get('requestMonth');
    const reason = formData.get('requestReason');
    
    if (year && month && reason) {
        showToast('Payslip request submitted successfully!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('requestPayslipModal')).hide();
        form.reset();
    } else {
        showToast('Please fill in all required fields', 'error');
    }
}

function getPayDate(payslipId) {
    const [year, month] = payslipId.split('-');
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    return `${monthNames[parseInt(month) - 1]} ${year}`;
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
    console.log('Payslips page initialized');
});
</script>

<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.dropdown-toggle::after {
    margin-left: 0.5em;
}

.badge {
    font-size: 0.75em;
}

.table-active {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>
